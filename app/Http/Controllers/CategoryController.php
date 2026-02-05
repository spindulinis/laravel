<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryCsvService;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryCsvService $csvService
    )
    {
    }

    public function index()
    {
        $categories = Category::all();

        return CategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $category = Category::create($validated);
        return new CategoryResource($category);
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function csv()
    {
        $csvData = $this->csvService->build();
        return response()->streamDownload(function () use ($csvData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, collect($csvData['fields'])->pluck('label')->toArray());
            foreach ($csvData['data'] as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, "{$csvData['filename']}.csv");
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(null, 204);
    }

    public function changeOrder(Category $firstCategory, Category $secondCategory)
    {
        DB::transaction(function () use ($firstCategory, $secondCategory) {
            $firstOrder = $firstCategory->order;
            $secondOrder = $secondCategory->order;

            $firstCategory->update(['order' => $secondOrder]);
            $secondCategory->update(['order' => $firstOrder]);
        });

        return response()->json(null, 204);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $validated = $request->validated();
        $category->update($validated);
        return new CategoryResource($category);
    }
}
