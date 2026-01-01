<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Context;
use Spatie\RouteAttributes\Attributes\{Delete, Get, Middleware, Post as PostRoute, Prefix, Put};
use Symfony\Component\HttpFoundation\Response;

#[Prefix('api/posts')]
#[Middleware('api')]
class PostController extends Controller
{
    #[Get('', 'posts.index',)]
    public function index(): AnonymousResourceCollection
    {
        Context::add("use_case", "List Posts");

        $posts = Post::query()
            ->with(['category:id,name', 'tags:id,name'])
            ->get();

        return PostResource::collection($posts);
    }

    #[PostRoute('', 'posts.store')]
    public function store(StorePostRequest $request): Response
    {
        Context::add("use_case", "Create Post");

        $validated = $request->validated();

        $post = Post::query()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category'],
        ]);

        $tagIds = collect($validated['tags'])
            ->map(function (array $tagData): int {
                if (!empty($tagData['id'])) {
                    return (int) $tagData['id'];
                }

                return Tag::query()->firstOrCreate([
                    'name' => $tagData['name'],
                ])->id;
            })
            ->unique()
            ->values()
            ->all();

        $post->tags()->sync($tagIds);
        $post->load(['category:id,name', 'tags:id,name']);

        return (new PostResource($post))
            ->response()->setStatusCode(Response::HTTP_CREATED);
    }

    #[Get('{post}', 'posts.show')]
    public function show(Post $post)
    {
        Context::add("use_case", "Show Post");

        $post->load(['category:id,name', 'tags:id,name']);

        return new PostResource($post);
    }

    #[Put('{post}', 'posts.update')]
    public function update(UpdatePostRequest $request, Post $post)
    {
        Context::add("use_case", "Update Post");

        $validated = $request->validated();

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category'],
        ]);

        $tagIds = collect($validated['tags'])
            ->map(function (array $tagData): int {
                if (!empty($tagData['id'])) {
                    return (int) $tagData['id'];
                }

                return Tag::query()->firstOrCreate([
                    'name' => $tagData['name'],
                ])->id;
            })
            ->unique()
            ->values()
            ->all();

        $post->tags()->sync($tagIds);
        $post->load(['category:id,name', 'tags:id,name']);

        return new PostResource($post);
    }

    #[Delete('{post}', 'posts.delete')]
    public function destroy(Post $post)
    {
        Context::add("use_case", "Delete Post");

        $post->delete();

        return response()->noContent();
    }
}
