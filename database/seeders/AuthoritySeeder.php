<?php

namespace Database\Seeders;

use App\Models\Authority;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AuthoritySeeder extends Seeder
{
    public function run(): void
    {
        // Create a test authority
        $authority = Authority::create([
            'name' => 'City Environmental Department',
            'type' => 'government',
            'jurisdiction_name' => 'Metro City',
            'jurisdiction_boundary' => 'POLYGON((-74.0 40.7, -74.0 40.8, -73.9 40.8, -73.9 40.7, -74.0 40.7))', // Simple polygon for NYC area
            'contact_email' => 'environment@metrocity.gov',
            'contact_phone' => '555-123-4567',
            'notification_email' => 'alerts@metrocity.gov',
            'notification_preferences' => ['email', 'dashboard'],
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);

        // Assign categories to monitor
        $categories = Category::take(3)->get();
        $authority->monitoredCategories()->attach($categories->pluck('id'));

        // Create a test authority user
        User::create([
            'name' => 'Authority User',
            'email' => 'authority@example.com',
            'password' => Hash::make('password'),
            'role' => 'authority',
            'authority_id' => $authority->id,
        ]);

        $this->command->info('Authority test data created successfully!');
    }
}