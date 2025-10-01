<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\CategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(10);
        return $this->jsonResponseWithPagination($categories, $categories->total());
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
        return $this->successfulResponse($category, __('Category created'), 201);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return $this->successfulResponse($category, __('Category updated'));
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return $this->successfulResponse(null, __('Category deleted'));
    }
}

