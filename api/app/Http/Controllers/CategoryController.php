<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Context;
use Spatie\RouteAttributes\Attributes\{Delete, Get, Middleware, Post, Prefix, Put};
use Symfony\Component\HttpFoundation\Response;

#[Prefix('api/categories')]
#[Middleware('api')]
class CategoryController extends Controller
{
    #[Get('', 'categories.index',)]
    public function index(): AnonymousResourceCollection
    {
        Context::add("use_case", "List Categories");

        $categories = CategoryResource::collection(Category::get(['id', 'name']));

        return $categories;
    }

    #[Post('', 'categories.store')]
    public function store(StoreCategoryRequest $request): Response
    {
        Context::add("use_case", "Create Category");

        $category = Category::create($request->validated());

        return (new CategoryResource($category))
            ->response()->setStatusCode(Response::HTTP_CREATED);
    }

    #[Get('{category}', 'categories.show')]
    public function show(Category $category)
    {
        Context::add("use_case", "Show Category");

        return (new CategoryResource($category));
    }

    #[Put('{category}', 'categories.update')]
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        Context::add("use_case", "Update Category");

        $category->update($request->validated());

        return (new CategoryResource($category));
    }

    #[Delete('{category}', 'categories.delete')]
    public function destroy(Category $category)
    {
        Context::add("use_case", "Delete Category");

        $category->delete();

        return response()->noContent();
    }
}
