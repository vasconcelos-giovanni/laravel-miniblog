<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Database\Seeders\CategorySeeder;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    public function test_it_orders_categories_by_name_by_default(): void
    {
        Category::factory()->create(['name' => 'zulu']);
        Category::factory()->create(['name' => 'alpha']);

        $names = Category::query()->pluck('name')->all();

        $this->assertSame(['alpha', 'zulu'], $names);
    }

    public function test_it_enforces_unique_name(): void
    {
        Category::query()->create(['name' => 'news']);

        $this->expectException(QueryException::class);
        Category::query()->create(['name' => 'news']);
    }

    public function test_it_soft_deletes(): void
    {
        $category = Category::factory()->create(['name' => 'soft-delete-me']);
        $category->delete();

        $this->assertNull(Category::query()->whereKey($category->id)->first());
        $this->assertNotNull(Category::withTrashed()->whereKey($category->id)->first());
    }

    public function test_category_seeder_creates_expected_categories_and_is_idempotent(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(CategorySeeder::class);

        $this->assertSame(2, Category::query()->count());
        $this->assertSame(['news', 'tutorials'], Category::query()->pluck('name')->all());
    }
}
