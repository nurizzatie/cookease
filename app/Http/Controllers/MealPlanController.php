<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MealPlanController extends Controller
{
    // 🔹 Show meal plan for selected day
    public function index(Request $request)
    {
        $userId = Auth::id();

        // ✅ Use selected date or fallback to today
        $date = $request->input('date', Carbon::today()->toDateString());

        // Get list of all distinct dates with meal plans
       $availableDates = MealPlan::where('user_id', $userId)
    ->orderBy('date')
    ->pluck('date')
    ->unique()
    ->toArray();


        // Get the selected day's meal plan grouped by meal_type
        $plans = MealPlan::with('recipe')
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->orderBy('meal_type')
            ->get()
            ->groupBy('meal_type');

        $recipes = Recipe::all(); // let user select from all recipes

        return view('meal_plan.index', compact('plans', 'recipes', 'date', 'availableDates'));
    }

    // 🔹 Store new meal plan entry
    public function store(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
        ]);

        // 🔒 Check if already 3 entries exist for that meal type and date
        $existingCount = MealPlan::where('user_id', Auth::id())
            ->where('date', $request->date)
            ->where('meal_type', $request->meal_type)
            ->count();

        if ($existingCount >= 3) {
            return back()->withErrors(['meal_type' => '⚠️ You can only add up to 3 ' . ucfirst($request->meal_type) . ' meals per day.'])->withInput();
        }

        MealPlan::create([
            'user_id' => Auth::id(),
            'recipe_id' => $request->recipe_id,
            'date' => $request->date,
            'meal_type' => $request->meal_type,
        ]);

        return back()->with('message', 'Meal plan added!');
    }

    // 🔹 Delete a meal plan entry
    public function destroy(MealPlan $mealPlan)
    {
        if ($mealPlan->user_id != Auth::id()) {
            abort(403);
        }

        $mealPlan->delete();

        return back()->with('message', 'Meal removed.');
    }

    // 🔹 Store from generated recipe input
   // 🔹 Store from generated recipe input
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

    // 🔒 Prevent more than 3 of same meal_type per day
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

    // 🔒 Check for duplicate by name+meal_type+date
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

    // ✅ Clean the ingredient list to only keep names
    $ingredientNames = collect($request->ingredients)->pluck('value')->toArray();

    // ✅ Save recipe
    $recipe = Recipe::create([
        'name' => $request->name,
        'description' => $request->description ?? null,
        'instructions' => $request->instructions ?? null,
        'ingredients' => json_encode($request->ingredients),
        'groceryLists' => json_encode($ingredientNames),
        'duration' => $request->duration ?? null,
        'difficulty' => $request->difficulty ?? null,
        'servings' => $request->servings ?? null,
        'calories' => $request->calories ?? null,
        'image' => $request->image ?? null,
    ]);

    // ✅ Save to meal plan
    MealPlan::create([
        'user_id' => Auth::id(),
        'recipe_id' => $recipe->id,
        'date' => $request->date,
        'meal_type' => $request->meal_type,
    ]);

    return response()->json(['success' => true, 'message' => 'Meal plan added successfully!']);
}


    // 🔹 Show saved recipe details
    public function showSaved($id)
    {
        $recipe = Recipe::findOrFail($id);

        // Decode fields if stored as JSON
        $recipe->ingredients = json_decode($recipe->ingredients, true);
        $recipe->groceryLists = json_decode($recipe->groceryLists ?? '[]', true);

        return view('recipe-saved-detail', ['recipe' => $recipe]);
    }
}
