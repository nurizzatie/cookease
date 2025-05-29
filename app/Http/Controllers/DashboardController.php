<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\BMI;
use App\Models\Favorite;
use App\Models\MealPlan;
use App\Models\Recipe;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // BMI
        $bmiRecord = $user->bmi;
        $bmi = $bmiRecord ? number_format($bmiRecord->getBmiAttribute(), 1) : null;
        $bmiCategory = $bmiRecord ? $bmiRecord->getBmiCategory() : 'N/A';

        // Calories Intake Today
        $todayCalories = MealPlan::where('user_id', $user->id)
            ->whereDate('date', today())
            ->with('recipe')
            ->get()
            ->sum(fn($plan) => $plan->recipe->calories ?? 0);

        // Saved Recipes Count
        $savedCount = Favorite::where('user_id', $user->id)->count();

        // Recipes Generated This Week (based on created_at and user’s session or tracking)
        $generatedCount = Recipe::where('created_at', '>=', now()->startOfWeek())->count();

        // Today's Meal Plan
        $todaysPlans = MealPlan::where('user_id', $user->id)
            ->whereDate('date', today())
            ->with('recipe')
            ->get();

        // Recently Saved Recipes
        $recentFavorites = Favorite::where('user_id', $user->id)
            ->latest()
            ->with('recipe')
            ->take(3)
            ->get()
            ->pluck('recipe');

        // Recipe Recommendations (latest recipes from DB)
        $recommendedRecipes = Recipe::latest()->take(4)->get();

        // AI Cooking Tips
        $tips = $this->generateDailyCookingTips();

        return view('dashboard', compact(
            'bmi', 'bmiCategory',
            'todayCalories', 'savedCount', 'generatedCount',
            'todaysPlans', 'recentFavorites', 'recommendedRecipes', 'tips'
        ));
    }

    protected function generateDailyCookingTips()
    {
        $apiKey = config('services.groq.key');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'meta-llama/llama-4-scout-17b-16e-instruct',
                'messages' => [
                    ['role' => 'user', 'content' => 'Give 3 simple daily cooking tips. Return only array of strings.'],
                ],
            ]);

            $json = $response->json();
            $content = $json['choices'][0]['message']['content'] ?? '[]';

            return json_decode($content, true) ?? [];
        } catch (\Exception $e) {
            return ['Use fresh ingredients.', 'Taste as you cook.', 'Keep your knives sharp.'];
        }
    }
}