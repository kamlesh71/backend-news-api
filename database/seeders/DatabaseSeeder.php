<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\NewsDataSource;
use App\Models\NewsCategory;
use App\Models\NewsSource;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        $categories = [
//            'business',
//            'entertainment',
//            'general',
//            'health',
//            'science',
//            'sports',
//            'technology',
//        ];
//
//        NewsCategory::query()->truncate();

        // NewsCategory::insert(collect($categories)->map(fn($category) => ['name' => $category, 'source' => NewsDataSource::NEWS_API->value])->toArray());

       // $sources = array_column(NewsDataSource::cases(), 'value');

//        NewsSource::query()->truncate();
//
//        NewsSource::insert(collect($sources)->map(fn($source) => ['name' => $source])->toArray());

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
