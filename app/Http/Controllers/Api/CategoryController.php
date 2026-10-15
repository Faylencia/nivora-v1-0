<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $business = $this->business($request);
        $categories = Category::query()
            ->where(fn (Builder $query): Builder => $query->whereNull('business_id')->orWhere('business_id', $business->id))
            ->orderBy('name')
            ->get();

        return $this->success(CategoryResource::collection($categories), 'Categories retrieved successfully');
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $business = $this->business($request);
        $data = $request->validated();

        if (Category::query()->where('name', $data['name'])->where(fn (Builder $query): Builder => $query->whereNull('business_id')->orWhere('business_id', $business->id))->exists()) {
            return response()->json(['success' => false, 'message' => 'Category name already exists.'], 409);
        }

        $category = $business->categories()->create($data);

        return $this->success(new CategoryResource($category), 'Category created successfully', 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($category->business_id === $business->id, 404);

        $data = $request->validated();
        if (isset($data['name']) && $business->categories()->where('name', $data['name'])->whereKeyNot($category->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Category name already exists.'], 409);
        }

        $category->update($data);

        return $this->success(new CategoryResource($category->fresh()), 'Category updated successfully');
    }

    public function destroy(Request $request, Category $category): JsonResponse
    {
        $business = $this->business($request);
        abort_unless($category->business_id === $business->id, 404);

        if ($category->products()->exists()) {
            return response()->json(['success' => false, 'message' => 'Category cannot be deleted while products are attached.'], 409);
        }

        $category->delete();

        return $this->success(null, 'Category deleted successfully');
    }
}
