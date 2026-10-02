<?php

namespace App\Services;

use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\CategoryServiceInterface;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService implements CategoryServiceInterface
{
    public function __construct(private readonly CategoryRepositoryInterface $categoryRepository) {}

    public function getAllCategories(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->categoryRepository->getAllCategories($perPage, $filters);
    }

    public function getCategory(Category $category): Category
    {
        return $this->categoryRepository->loadForDisplay($category);
    }

    public function createCategory(array $data): Category
    {
        $category = $this->categoryRepository->createCategory($data);

        return $this->categoryRepository->loadForDisplay($category);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $category = $this->categoryRepository->updateCategory($category, $data);

        return $this->categoryRepository->loadForDisplay($category);
    }

    public function deleteCategory(Category $category): void
    {
        $this->categoryRepository->deleteCategory($category);
    }
}
