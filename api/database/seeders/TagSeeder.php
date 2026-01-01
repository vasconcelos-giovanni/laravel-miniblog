<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        Tag::query()->insertOrIgnore([
            ['name' => 'tech', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'economy', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
