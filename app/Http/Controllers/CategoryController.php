<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Requests\IndexCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Interfaces\CategoryServiceInterface;
use App\Models\Category;
use App\Support\PaginatedResource;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="Category management operations"
 * )
 */
class CategoryController extends Controller
{
    public function __construct(private readonly CategoryServiceInterface $categoryService) {}

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get a paginated list of categories",
     *     tags={"Categories"},
     *
     *     @OA\Parameter(name="per_page", in="query", required=false, description="Items per page (1-100)", @OA\Schema(type="integer", example=10)),
     *     @OA\Parameter(name="name", in="query", required=false, description="Partial name match", @OA\Schema(type="string")),
     *
     *     @OA\Response(response=200, description="Paginated categories", @OA\JsonContent(ref="#/components/schemas/CategoryPage")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function index(IndexCategoryRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 10);
        unset($filters['per_page']);

        $paginated = $this->categoryService->getAllCategories($perPage, $filters);

        return response()->json(PaginatedResource::make($paginated, CategoryResource::class));
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Create a category",
     *     tags={"Categories"},
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/CategoryRequest")),
     *
     *     @OA\Response(response=201, description="Category created", @OA\JsonContent(ref="#/components/schemas/Category")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(CategoryRequest $request): CategoryResource
    {
        return new CategoryResource($this->categoryService->createCategory($request->validated()));
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{category}",
     *     summary="Get a category",
     *     tags={"Categories"},
     *
     *     @OA\Parameter(name="category", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Category", @OA\JsonContent(ref="#/components/schemas/Category")),
     *     @OA\Response(response=404, description="Category not found", @OA\JsonContent(ref="#/components/schemas/ErrorMessage"))
     * )
     */
    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($this->categoryService->getCategory($category));
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{category}",
     *     summary="Update a category",
     *     tags={"Categories"},
     *
     *     @OA\Parameter(name="category", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/CategoryRequest")),
     *
     *     @OA\Response(response=200, description="Category updated", @OA\JsonContent(ref="#/components/schemas/Category")),
     *     @OA\Response(response=404, description="Category not found", @OA\JsonContent(ref="#/components/schemas/ErrorMessage")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(CategoryRequest $request, Category $category): CategoryResource
    {
        return new CategoryResource(
            $this->categoryService->updateCategory($category, $request->validated())
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{category}",
     *     summary="Delete a category",
     *     description="Tasks in the category are kept and their category_id is set to null.",
     *     tags={"Categories"},
     *
     *     @OA\Parameter(name="category", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=204, description="Category deleted"),
     *     @OA\Response(response=404, description="Category not found", @OA\JsonContent(ref="#/components/schemas/ErrorMessage"))
     * )
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->deleteCategory($category);

        return response()->json(null, 204);
    }
}
