<?php

namespace Database\Seeders;

use App\Models\IncidentCategory;
use Illuminate\Database\Seeder;

class IncidentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Illegal Dumping',
                'slug' => 'illegal-dumping',
                'description' => 'Unauthorized disposal of waste materials',
                'icon' => 'bi-trash',
                'color' => '#dc3545',
                'sort_order' => 1,
            ],
            [
                'name' => 'Water Pollution',
                'slug' => 'water-pollution',
                'description' => 'Contamination of water bodies',
                'icon' => 'bi-droplet',
                'color' => '#0d6efd',
                'sort_order' => 2,
            ],
            [
                'name' => 'Air Quality Issues',
                'slug' => 'air-quality',
                'description' => 'Poor air quality or pollution',
                'icon' => 'bi-cloud-haze',
                'color' => '#6c757d',
                'sort_order' => 3,
            ],
            [
                'name' => 'Noise Pollution',
                'slug' => 'noise-pollution',
                'description' => 'Excessive or disturbing noise',
                'icon' => 'bi-volume-up',
                'color' => '#fd7e14',
                'sort_order' => 4,
            ],
            [
                'name' => 'Habitat Destruction',
                'slug' => 'habitat-destruction',
                'description' => 'Damage to natural habitats',
                'icon' => 'bi-tree',
                'color' => '#198754',
                'sort_order' => 5,
            ],
            [
                'name' => 'Chemical Spills',
                'slug' => 'chemical-spills',
                'description' => 'Hazardous chemical releases',
                'icon' => 'bi-exclamation-triangle',
                'color' => '#ffc107',
                'sort_order' => 6,
            ],
            [
                'name' => 'Wildlife Issues',
                'slug' => 'wildlife-issues',
                'description' => 'Problems affecting local wildlife',
                'icon' => 'bi-bug',
                'color' => '#20c997',
                'sort_order' => 7,
            ],
            [
                'name' => 'Other',
                'slug' => 'other',
                'description' => 'Other environmental concerns',
                'icon' => 'bi-question-circle',
                'color' => '#6f42c1',
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            IncidentCategory::create($category);
        }
    }
}
