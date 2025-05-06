<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Clear the table first
        \DB::table('ingredients')->truncate();

        $ingredients = [
            'chicken',
            'rice',
            'tofu',
            'spinach',
            'lemongrass',
            'egg',
            'fish',
            'beef',
            'onion',
            'garlic',
            'potato',
            'carrot',
            'cabbage',
            'coconut milk',
            'ginger',
            'turmeric',
            'chili',
            'shrimp',
            'mushroom',
            'pumpkin'
        ];

        foreach ($ingredients as $ingredient) {
            DB::table('ingredients')->insert([
                'name' => $ingredient,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


    }


}


