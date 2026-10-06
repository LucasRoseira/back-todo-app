<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategories(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Category::query()->withCount('tasks')->orderBy('name');

        if (array_key_exists('name', $filters) && $filters['name'] !== null && $filters['name'] !== '') {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }

        return $query->paginate($perPage);
    }

    public function loadForDisplay(Category $category): Category
    {
        return $category->loadCount('tasks');
    }

    public function createCategory(array $data): Category
    {
        return Category::query()->create($data);
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
