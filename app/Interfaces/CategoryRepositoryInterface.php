<?php

namespace App\Interfaces;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    public function getAllCategories(int $perPage = 10, array $filters = []): LengthAwarePaginator;

    public function loadForDisplay(Category $category): Category;

    public function createCategory(array $data): Category;

    public function updateCategory(Category $category, array $data): Category;

    public function deleteCategory(Category $category): void;
}
