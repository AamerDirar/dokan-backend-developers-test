<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var $categories */
        $categories[] = [
            'name' => 'Home',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $categories[] = [
            'name' => 'Health',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $categories[] = [
            'name' => 'Education',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $categories[] = [
            'name' => 'Groceries',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $categories[] = [
            'name' => 'Utilities',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $categories[] = [
            'name' => 'Transportation',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $categories[] = [
            'name' => 'Shopping',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $categories[] = [
            'name' => 'Food and Beverage',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $categories[] = [
            'name' => 'Entertainment',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $categories[] = [
            'name' => 'Travel',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        Category::insert($categories);

    }
}
