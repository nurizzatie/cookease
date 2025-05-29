<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MealPlanController extends Controller
{
    // Show meal plan for selected day
    public function index(Request $request)
    {
        $userId = Auth::id();
        $date = $request->input('date', Carbon::today()->toDateString());

        $availableDates = MealPlan::where('user_id', $userId)
            ->orderBy('date')
            ->pluck('date')
            ->unique()
            ->toArray();

        $plans = MealPlan::with('recipe')
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->orderBy('meal_type')
            ->get()
            ->groupBy('meal_type');

        $recipes = Recipe::all();

        return view('meal_plan.index', compact('plans', 'recipes', 'date', 'availableDates'));
    }

    // Store new meal plan entry
    public function store(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
        ]);

        $existingCount = MealPlan::where('user_id', Auth::id())
            ->where('date', $request->date)
            ->where('meal_type', $request->meal_type)
            ->count();

        if ($existingCount >= 3) {
            return back()->withErrors(['meal_type' => 'You can only add up to 3 ' . ucfirst($request->meal_type) . ' meals per day.'])->withInput();
        }

        MealPlan::create([
            'user_id' => Auth::id(),
            'recipe_id' => $request->recipe_id,
            'date' => $request->date,
            'meal_type' => $request->meal_type,
        ]);

        return back()->with('message', 'Meal plan added successfully.');
    }

    // Delete a meal plan entry
    public function destroy(MealPlan $mealPlan)
    {
        if ($mealPlan->user_id != Auth::id()) {
            abort(403);
        }

        $mealPlan->delete();

        return back()->with('message', 'Meal removed.');
    }

    // Store from generated recipe input
    public function storeFromGenerated(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'instructions' => 'required|string',
            'ingredients' => 'required|array',
            'duration' => 'nullable|string',
            'difficulty' => 'nullable|string',
            'servings' => 'nullable|integer',
            'calories' => 'nullable|integer',
            'image' => 'nullable|string',
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
        ]);

        $existingCount = MealPlan::where('user_id', Auth::id())
            ->where('date', $request->date)
            ->where('meal_type', $request->meal_type)
            ->count();

        if ($existingCount >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'You can only add up to 3 ' . ucfirst($request->meal_type) . ' meals per day.'
            ], 422);
        }

        $duplicate = MealPlan::where('user_id', Auth::id())
            ->whereDate('date', $request->date)
            ->where('meal_type', $request->meal_type)
            ->whereHas('recipe', function ($query) use ($request) {
                $query->where('name', $request->name);
            })
            ->exists();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'This recipe has already been added for ' . ucfirst($request->meal_type) . ' on this date.'
            ], 422);
        }

        $ingredientNames = collect($request->ingredients)->pluck('value')->toArray();

        $recipe = Recipe::create([
            'name' => $request->name,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'ingredients' => json_encode($request->ingredients),
            'groceryLists' => json_encode($ingredientNames),
            'duration' => $request->duration,
            'difficulty' => $request->difficulty,
            'servings' => $request->servings,
            'calories' => $request->calories,
            'image' => $request->image,
        ]);

        MealPlan::create([
            'user_id' => Auth::id(),
            'recipe_id' => $recipe->id,
            'date' => $request->date,
            'meal_type' => $request->meal_type,
        ]);

        return response()->json(['success' => true, 'message' => 'Meal plan added successfully.']);
    }

    // Show saved recipe details
    public function showSaved($id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->ingredients = json_decode($recipe->ingredients, true);
        $recipe->groceryLists = json_decode($recipe->groceryLists ?? '[]', true);

        return view('recipe-saved-detail', ['recipe' => $recipe]);
    }

    // Show edit form (if needed)
    public function edit($id)
    {
        $meal = MealPlan::findOrFail($id);
        $recipes = Recipe::all();

        return view('meal-plan.edit', compact('meal', 'recipes'));
    }

    // Update meal plan
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'meal_type' => 'required|string',
        ]);

        $meal = MealPlan::findOrFail($id);
        $meal->update($validated);

        // 🛠️ Redirect to index with the newly updated date
    return redirect()->route('meal-plan.index', ['date' => $validated['date']])
                     ->with('message', 'Meal updated successfully!');
    }
}
