<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Spatie\RouteAttributes\Attributes\{Delete, Get, Post, Prefix, Put, Middleware};
use Illuminate\Support\Facades\{Context};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

#[Prefix('api/tags')]
#[Middleware('api')]
class TagController extends Controller
{
    #[Get('', 'tags.index',)]
    public function index(): AnonymousResourceCollection
    {
        Context::add("use_case", "List Tags");

        $tags = TagResource::collection(Tag::get(['id', 'name']));

        return $tags;
    }

    #[Post('', 'tags.store')]
    public function store(StoreTagRequest $request): Response
    {
        Context::add("use_case", "Create Tag");

        $tag = Tag::create($request->validated());

        return (new TagResource($tag))
            ->response()->setStatusCode(Response::HTTP_CREATED);
    }

    #[Get('{tag}', 'tags.show')]
    public function show(Tag $tag)
    {
        Context::add("use_case", "Show Tag");

        return (new TagResource($tag));
    }

    #[Put('{tag}', 'tags.update')]
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        Context::add("use_case", "Update Tag");

        $tag->update($request->validated());

        return (new TagResource($tag));
    }

    #[Delete('{tag}', 'tags.delete')]
    public function destroy(Tag $tag)
    {
        Context::add("use_case", "Delete Tag");

        $tag->delete();

        return response()->noContent();
    }
}
