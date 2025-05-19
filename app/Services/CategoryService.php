<?php

namespace App\Services;

use App\Interfaces\CategoryServiceInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService implements CategoryServiceInterface
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository) {}

    public function getAllCategories($perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Category::query();

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        return $query->paginate($perPage);
    }

    public function createCategory(array $data): Category
    {
        return $this->categoryRepository->createCategory($data);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        return $this->categoryRepository->updateCategory($category, $data);
    }

    public function deleteCategory(Category $category): void
    {
        $this->categoryRepository->deleteCategory($category);
    }
}
