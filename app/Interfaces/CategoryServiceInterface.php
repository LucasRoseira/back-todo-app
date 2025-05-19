<?php

namespace App\Interfaces;

use App\Models\Category;

interface CategoryServiceInterface
{
    public function getAllCategories(int $perPage = 10, array $filters);
    public function createCategory(array $data): Category;
    public function updateCategory(Category $category, array $data): Category;
    public function deleteCategory(Category $category): void;
}
