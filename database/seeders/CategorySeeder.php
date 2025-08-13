<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['key' => 'mid', 'display_name' => 'الوسط'], 
            ['key' => 'left', 'display_name' => 'اليسار'],
            ['key' => 'right', 'display_name' => 'اليمين'], 
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['key' => $category['key']],
                ['display_name' => $category['display_name']]
            );
        }
    }
}
