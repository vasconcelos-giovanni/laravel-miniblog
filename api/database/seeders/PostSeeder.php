<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $technology = Category::query()->firstOrCreate(['name' => 'technology']);
        $lifestyle = Category::query()->firstOrCreate(['name' => 'lifestyle']);

        $tech = Tag::query()->firstOrCreate(['name' => 'tech']);
        $news = Tag::query()->firstOrCreate(['name' => 'news']);

        $helloWorld = Post::query()->updateOrCreate(
            ['title' => 'Hello World'],
            [
                'content' => 'Welcome to the miniblog!',
                'category_id' => $technology->id,
            ]
        );

        $second = Post::query()->updateOrCreate(
            ['title' => 'Second Post'],
            [
                'content' => 'Another post, another day.',
                'category_id' => $lifestyle->id,
            ]
        );

        $helloWorld->tags()->sync([$tech->id, $news->id]);
        $second->tags()->sync([$news->id]);
    }
}
