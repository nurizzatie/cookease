<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    public function browse(Request $request)
    {
        $query = Recipe::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('calories_min')) {
            $query->where('calories', '>=', (int) $request->calories_min);
        }

        if ($request->filled('calories_max')) {
            $query->where('calories', '<=', (int) $request->calories_max);
        }

        $recipes = $query->latest()->paginate(9)->appends($request->all());
        return view('browse-recipes', compact('recipes'));
    }

    public function saveRecipe(Request $request)
    {
        $data = $request->all();

        // Save or find recipe (avoid duplicates by name + description as example)
        $recipe = Recipe::firstOrCreate(
            ['name' => $data['name'], 'description' => $data['description']],
            [
                'duration'       => $data['duration'] ?? null,
                'servings'       => $data['servings'] ?? null,
                'difficulty'     => $data['difficulty'] ?? 'easy',
                'calories'       => $data['calories'] ?? null,
                'image'          => $data['image'] ?? null,
                'ingredients'    => json_encode($data['ingredients']),
                'instructions'   => $data['instructions'],
                'grocery_lists'  => json_encode($data['groceryLists']),
            ]
        );

        // Save to favorites
        Favorite::firstOrCreate([
            'user_id'   => Auth::id(),
            'recipe_id' => $recipe->id,
        ]);

        return redirect()->route('recipe.detail', [
            'index' => $recipe->id,
            'from' => 'db'
        ])->with('message', 'Recipe saved to favorites!');
    }

    public function unsaveRecipe($id)
    {
        $user = Auth::user();

        // Delete favorite entry
        Favorite::where('user_id', $user->id)
            ->where('recipe_id', $id)
            ->delete();

        return back()->with('message', 'Recipe removed from favorites.');
    }
}
