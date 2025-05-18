<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Interfaces\CategoryServiceInterface;
use Illuminate\Http\JsonResponse;
use App\Models\Category;


/**
 * @OA\Tag(
 *     name="Categories",
 *     description="Categories management operations"
 * )
 */
class CategoryController extends Controller
{
    public function __construct(private readonly CategoryServiceInterface $categoryService) {}

        /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get list of all categories",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Category")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        return response()->json($categories);
    }

        /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Create a new category",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CategoryRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());
        return response()->json($category, 201);
    }

        /**
     * @OA\Get(
     *     path="/api/categories/{category}",
     *     summary="Get specific category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */

    public function show(Category $category): JsonResponse
    {
        return response()->json($category);
    }

      /**
     * @OA\Put(
     *     path="/api/categories/{category}",
     *     summary="Update a category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CategoryRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $updatedCategory = $this->categoryService->updateCategory($category, $request->validated());
        return response()->json($updatedCategory);
    }

        /**
     * @OA\Delete(
     *     path="/api/categories/{category}",
     *     summary="Delete a category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Category deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->deleteCategory($category);
        return response()->json(null, 204);
    }
}