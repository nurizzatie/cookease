<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MealPlanController extends Controller
{
    // 🔹 Show meal plan for current week
    public function index()
    {
        $userId = Auth::id();

        $startOfWeek = Carbon::now()->startOfWeek(); // Monday
        $endOfWeek = Carbon::now()->endOfWeek();     // Sunday

        $plans = MealPlan::with('recipe')
            ->where('user_id', $userId)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        $recipes = Recipe::all(); // if you want to let user select from all recipes

        return view('meal_plan.index', compact('plans', 'recipes'));
    }

    // 🔹 Store new meal plan entry
    public function store(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner,others',
        ]);

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
}
