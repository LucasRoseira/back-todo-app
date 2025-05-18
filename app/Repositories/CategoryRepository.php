<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;


class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategories(): array
    {
        return Category::all()->toArray();
    }

    public function createCategory(array $data): Category
    {
        return Category::create($data);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $category->update($data);
        return $category;
    }

    public function deleteCategory(Category $category): void
    {
        $category->delete();
    }
}