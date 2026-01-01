<?php

namespace Tests\Feature\Http;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    public function test_it_creates_missing_tags_and_attaches_all_tags(): void
    {
        $category = Category::factory()->create(['name' => 'Technology']);
        $existingTag = Tag::factory()->create(['name' => 'tech']);

        $response = $this->postJson('/api/posts', [
            'title' => 'My First Blog Post',
            'content' => 'This is the content of my first blog post.',
            'category' => $category->id,
            'tags' => [
                ['id' => $existingTag->id, 'name' => 'tech'],
                ['id' => null, 'name' => 'finances'],
            ],
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('tags', ['name' => 'finances']);
        $postId = $response->json('id');
        $this->assertNotNull($postId);

        $post = Post::query()->with(['category', 'tags'])->findOrFail($postId);

        $this->assertSame('Technology', $post->category->name);
        $this->assertSame(['finances', 'tech'], $post->tags->pluck('name')->sort()->values()->all());

        $response->assertJson(fn (AssertableJson $json) =>
            $json->where('id', $post->id)
                ->where('title', 'My First Blog Post')
                ->where('content', 'This is the content of my first blog post.')
                ->where('category', 'Technology')
                ->has('tags', 2)
                ->whereType('createdAt', 'string')
                ->whereType('updatedAt', 'string')
                ->where('deletedAt', null)
                ->etc()
        );
    }

    public function test_it_fetches_post_with_category_name_and_tags(): void
    {
        $category = Category::factory()->create(['name' => 'Technology']);
        $tag = Tag::factory()->create(['name' => 'tech']);

        $post = Post::query()->create([
            'title' => 'My First Blog Post',
            'content' => 'This is the content of my first blog post.',
            'category_id' => $category->id,
        ]);
        $post->tags()->sync([$tag->id]);

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) =>
            $json->where('id', $post->id)
                ->where('title', 'My First Blog Post')
                ->where('content', 'This is the content of my first blog post.')
                ->where('category', 'Technology')
                ->has('tags', 1)
                ->where('tags.0.id', $tag->id)
                ->where('tags.0.name', 'tech')
                ->whereType('createdAt', 'string')
                ->whereType('updatedAt', 'string')
                ->where('deletedAt', null)
                ->etc()
        );
    }
}
