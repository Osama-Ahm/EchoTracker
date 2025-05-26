<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Water Pollution',
                'description' => 'Issues related to water contamination and pollution',
                'icon' => 'bi-droplet',
                'color' => '#0d6efd',
            ],
            [
                'name' => 'Air Quality',
                'description' => 'Air pollution and quality concerns',
                'icon' => 'bi-cloud',
                'color' => '#6c757d',
            ],
            [
                'name' => 'Waste Management',
                'description' => 'Improper waste disposal and management issues',
                'icon' => 'bi-trash',
                'color' => '#dc3545',
            ],
            [
                'name' => 'Wildlife Protection',
                'description' => 'Concerns related to wildlife and habitat protection',
                'icon' => 'bi-tree',
                'color' => '#198754',
            ],
            [
                'name' => 'Noise Pollution',
                'description' => 'Excessive noise affecting communities and wildlife',
                'icon' => 'bi-volume-up',
                'color' => '#fd7e14',
            ],
            [
                'name' => 'Land Degradation',
                'description' => 'Soil erosion, deforestation, and land misuse',
                'icon' => 'bi-geo',
                'color' => '#6f42c1',
            ],
            [
                'name' => 'Other',
                'description' => 'Other environmental concerns not listed above',
                'icon' => 'bi-question-circle',
                'color' => '#20c997',
            ],
        ];

        $sortOrder = 1;
        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'icon' => $category['icon'],
                'color' => $category['color'],
                'is_active' => true,
                'sort_order' => $sortOrder++,
            ]);
        }
    }
}