<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Database\Seeders\PostSeeder;
use Tests\TestCase;

class PostTest extends TestCase
{
    public function test_it_soft_deletes(): void
    {
        $category = Category::factory()->create();
        $post = Post::query()->create([
            'title' => 'soft-delete-me',
            'content' => 'content',
            'category_id' => $category->id,
        ]);

        $post->delete();

        $this->assertNull(Post::query()->whereKey($post->id)->first());
        $this->assertNotNull(Post::withTrashed()->whereKey($post->id)->first());
    }

    public function test_it_belongs_to_category_and_has_many_tags(): void
    {
        $category = Category::factory()->create(['name' => 'Technology']);
        $tagA = Tag::factory()->create(['name' => 'alpha']);
        $tagB = Tag::factory()->create(['name' => 'beta']);

        $post = Post::query()->create([
            'title' => 't',
            'content' => 'c',
            'category_id' => $category->id,
        ]);
        $post->tags()->sync([$tagA->id, $tagB->id]);

        $post->load(['category', 'tags']);

        $this->assertSame('Technology', $post->category->name);
        $this->assertSame(['alpha', 'beta'], $post->tags->pluck('name')->sort()->values()->all());
    }

    public function test_post_seeder_creates_expected_posts_and_is_idempotent(): void
    {
        $this->seed(PostSeeder::class);
        $this->seed(PostSeeder::class);

        $this->assertSame(2, Post::query()->count());
        $this->assertSame(
            ['Hello World', 'Second Post'],
            Post::query()->orderBy('title')->pluck('title')->all()
        );

        $helloWorld = Post::query()->where('title', 'Hello World')->firstOrFail();
        $helloWorld->load('tags');
        $this->assertSame(['news', 'tech'], $helloWorld->tags->pluck('name')->sort()->values()->all());
    }
}
