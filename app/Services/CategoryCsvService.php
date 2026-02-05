<?php

namespace App\Services;

use App\Interfaces\CsvServiceInterface;
use App\Models\Category;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryCsvService implements CsvServiceInterface
{
    public function build(mixed ...$args): array
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            throw new NotFoundHttpException('Categories not found');
        }

        $data = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'parentId' => $category->parent_id,
                'order' => $category->order,
                'title' => $category->title,
                'description' => $category->description,
            ];
        })->toArray();

        return [
            'data' => $data,
            'fields' => $this->fields(),
            'filename' => $this->filename(),
        ];
    }

    public function fields(mixed ...$args): array
    {
        return [
            ['label' => 'ID', 'value' => 'id'],
            ['label' => 'Parent ID', 'value' => 'parentId'],
            ['label' => 'Order', 'value' => 'order'],
            ['label' => 'Title', 'value' => 'title'],
            ['label' => 'Description', 'value' => 'description'],
        ];
    }

    public function filename(mixed ...$args): string
    {
        return 'categories';
    }
}
