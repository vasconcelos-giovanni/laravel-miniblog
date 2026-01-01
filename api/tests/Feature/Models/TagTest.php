<?php

namespace Tests\Feature\Models;

use App\Models\Tag;
use Database\Seeders\TagSeeder;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class TagTest extends TestCase
{
    public function test_it_orders_tags_by_name_by_default(): void
    {
        Tag::factory()->create(['name' => 'zulu']);
        Tag::factory()->create(['name' => 'alpha']);

        $names = Tag::query()->pluck('name')->all();

        $this->assertSame(['alpha', 'zulu'], $names);
    }

    public function test_it_enforces_unique_name(): void
    {
        Tag::query()->create(['name' => 'tech']);

        $this->expectException(QueryException::class);
        Tag::query()->create(['name' => 'tech']);
    }

    public function test_it_soft_deletes(): void
    {
        $tag = Tag::factory()->create(['name' => 'soft-delete-me']);
        $tag->delete();

        $this->assertNull(Tag::query()->whereKey($tag->id)->first());
        $this->assertNotNull(Tag::withTrashed()->whereKey($tag->id)->first());
    }

    public function test_tag_seeder_creates_expected_tags_and_is_idempotent(): void
    {
        $this->seed(TagSeeder::class);
        $this->seed(TagSeeder::class);

        $this->assertSame(2, Tag::query()->count());
        $this->assertSame(['economy', 'tech'], Tag::query()->pluck('name')->all());
    }
}
