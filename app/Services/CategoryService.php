<?php

namespace App\Services;

use App\Interfaces\CategoryRepositoryInterface;

use App\Interfaces\CategoryServiceInterface;
use App\Models\Category;

class CategoryService implements CategoryServiceInterface
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories(): array
    {
        return $this->categoryRepository->getAllCategories();
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