<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::query()->insertOrIgnore([
            ['name' => 'News', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tutorials', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
