<?php

namespace Database\Seeders;

use App\Models\Recipe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Recipe::create([
            'name'       => 'Nasi Lemak',
            'description' => 'A traditional Malaysian dish with coconut rice, sambal, and fried anchovies.',
            'duration'    => '45 minutes',
            'difficulty'  => 'medium',
            'calories'    => 600,
            'image'       => 'https://via.placeholder.com/300x200',
            'ingredients' => json_encode([
                ['name' => 'rice', 'quantity' => 2, 'unit' => 'cups'],
                ['name' => 'coconut milk', 'quantity' => 1, 'unit' => 'cup'],
                ['name' => 'anchovies', 'quantity' => 100, 'unit' => 'grams'],
                ['name' => 'boiled eggs', 'quantity' => 2, 'unit' => 'pcs'],
            ]),
            'instructions' => "Wash the rice and soak for 30 minutes. Cook rice with coconut milk and a pinch of salt. Fry anchovies until crispy. Serve with boiled eggs, cucumber, and sambal.",
            'grocery_lists' => json_encode([
                'rice', 'coconut milk', 'anchovies', 'eggs', 'cucumber'
            ]),
        ]);

        Recipe::create([
            'name'       => 'Chicken Rendang',
            'description' => 'Spicy and flavorful chicken curry cooked with coconut milk and spices.',
            'duration'    => '1 hour 30 minutes',
            'difficulty'  => 'hard',
            'calories'    => 750,
            'image'       => 'https://via.placeholder.com/300x200',
            'ingredients' => json_encode([
                ['name' => 'chicken', 'quantity' => 1, 'unit' => 'whole (cut into pieces)'],
                ['name' => 'coconut milk', 'quantity' => 2, 'unit' => 'cups'],
                ['name' => 'lemongrass', 'quantity' => 2, 'unit' => 'stalks'],
                ['name' => 'galangal', 'quantity' => 1, 'unit' => 'inch, sliced'],
                ['name' => 'turmeric powder', 'quantity' => 1, 'unit' => 'tsp'],
                ['name' => 'coriander powder', 'quantity' => 1, 'unit' => 'tsp'],
                ['name' => 'garlic', 'quantity' => 4, 'unit' => 'cloves'],
                ['name' => 'onion', 'quantity' => 2, 'unit' => 'pcs'],
                ['name' => 'ginger', 'quantity' => 1, 'unit' => 'inch'],
                ['name' => 'salt', 'quantity' => 1, 'unit' => 'tsp'],
                ['name' => 'cooking oil', 'quantity' => 2, 'unit' => 'tbsp'],
            ]),
            'instructions' => "Blend onion, garlic, ginger, and galangal into a smooth paste. Heat oil in a pot and sauté the blended paste until fragrant. Add chicken pieces and cook until lightly browned. Add coconut milk, lemongrass, and all spices. Stir well. Serve hot with steamed rice.",
            'grocery_lists' => json_encode([
                'chicken', 'coconut milk', 'lemongrass', 'galangal', 'turmeric powder',
                'coriander powder', 'garlic', 'onion', 'ginger', 'salt', 'cooking oil'
            ]),
        ]);

        Recipe::create([
            'name' => 'Mee Goreng Mamak',
            'description' => 'Spicy and savory stir-fried noodles with tofu, potatoes, and bean sprouts.',
            'duration' => '30 minutes',
            'servings' => 2,
            'difficulty' => 'medium',
            'calories' => 550,
            'image' => 'https://via.placeholder.com/300x200',
            'ingredients' => json_encode([
                '200g yellow noodles',
                '100g fried tofu, cubed',
                '1 boiled potato, sliced',
                '1/2 cup bean sprouts',
                '2 cloves garlic, minced',
                '2 tablespoons chili paste',
                '1 tablespoon soy sauce',
                '1 tablespoon ketchup',
                '1 egg',
                'Lime wedges (for garnish)'
            ]),
            'instructions' => "Heat oil in a wok. Sauté garlic until fragrant. Add chili paste, tofu, and potato slices. Stir-fry for 2 minutes. Add noodles, soy sauce, ketchup, and mix well. Push noodles aside and scramble the egg. Add bean sprouts and stir everything together. Serve hot with lime wedges.",
            'grocery_lists' => json_encode([
                'yellow noodles',
                'fried tofu',
                'potato',
                'bean sprouts',
                'garlic',
                'chili paste',
                'soy sauce',
                'ketchup',
                'egg',
                'lime'
            ]),
        ]);
    }
}
