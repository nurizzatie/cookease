<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\HealthGoalController;
use App\Http\Controllers\BMIController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GenerateController;
use App\Http\Controllers\RecipeController;
use App\Models\Favorite;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MealPlanController;




// 🔐 Authenticated user routes
Route::middleware(['auth'])->group(function () {

    // 🧠 BMI
    Route::get('/profile/bmi', [ProfileController::class, 'modify'])->name('profile.bmi.edit');
    Route::get('/bmi/form', [BMIController::class, 'showForm'])->name('bmi.form');
    Route::post('/bmi/store', [BMIController::class, 'store'])->name('profile.bmi.update');
    Route::post('/bmi/update', [BmiController::class, 'update'])->name('bmi.update');
    Route::get('/health-goals', [HealthGoalController::class, 'create'])->name('health_goals.create');
    Route::post('/health-goals', [HealthGoalController::class, 'store'])->name('health_goals.store');
    Route::get('/profile/health-goal', [HealthGoalController::class, 'show'])->name('health_goals.show');
    Route::put('/profile/health-goal', [HealthGoalController::class, 'update'])->name('health_goals.update');



    // 👤 Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 🍳 Recipe Generation Flow
    // Route::get('/generate', fn () => view('generate'))->name('generate');
    Route::get('/generate', [GenerateController::class, 'showForm'])->name('generate');

    Route::post('/generate', [IngredientController::class, 'process'])->name('generate.process');
    Route::get('/generate-result', [IngredientController::class, 'showResult'])->name('generate.result');

    // 🧂 API for frontend Tagify
    Route::get('/api/ingredients', [IngredientController::class, 'getIngredients']);

    // Recipe List and Detail
    Route::get('/browse-recipes', [RecipeController::class, 'browse'])->name('recipes.browse');
    Route::get('/recipe-detail/{index}', function ($index) {
        $isSession = request()->query('from') === 'session';
        $user = Auth::user();
        $isFavorited = false;

        if ($isSession) {
            $recipes = session('generated_recipes', []);
            if (!isset($recipes[$index])) {
                abort(404);
            }
            $recipe = $recipes[$index];

            // Check if this recipe is already saved in DB by name + description
            $existing = Recipe::where('name', $recipe['name'])
                ->where('description', $recipe['description'])
                ->first();

            if ($existing && $user) {
                $isFavorited = Favorite::where('user_id', $user->id)
                    ->where('recipe_id', $existing->id)
                    ->exists();
            }

            return view('recipe-detail', [
                'recipe' => $recipe,
                'isSession' => true,
                'isFavorited' => $isFavorited,
            ]);
        }

        $recipe = Recipe::findOrFail($index);

        // Decode fields for Blade view
        $recipeArray = [
            'name'         => $recipe->name,
            'description'  => $recipe->description,
            'duration'     => $recipe->duration,
            'servings'     => $recipe->servings,
            'difficulty'   => $recipe->difficulty,
            'calories'     => $recipe->calories,
            'image'        => $recipe->image,
            'ingredients'  => is_array($recipe->ingredients) ? $recipe->ingredients : json_decode(json_decode($recipe->ingredients, true), true),
            'instructions' => $recipe->instructions,
            'groceryLists' => is_array($recipe->grocery_lists) ? $recipe->grocery_lists : json_decode(json_decode($recipe->grocery_lists, true), true),
        ];

        // Check if user favorited this recipe
        if ($user) {
            $isFavorited = Favorite::where('user_id', $user->id)
                ->where('recipe_id', $recipe->id)
                ->exists();
        }

        return view('recipe-detail', [
            'recipe' => $recipeArray,
            'isSession' => false,
            'isFavorited' => $isFavorited,
            'recipeId' => $recipe->id,
        ]);
    })->name('recipe.detail');

    // Save/Unsave recipe
    Route::post('/save-recipe', [RecipeController::class, 'saveRecipe'])->name('recipe.save');
    Route::delete('/recipe/unsave/{id}', [RecipeController::class, 'unsaveRecipe'])->name('recipe.unsave');
    Route::get('/saved-recipes', [FavoriteController::class, 'saved'])->middleware('auth')->name('recipes.saved');

});

// 🌐 Landing and OAuth
Route::get('/', fn () => redirect()->route('login'));

Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
Route::get('auth/facebook', [SocialAuthController::class, 'redirectToFacebook']);
Route::get('auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);

// 📊 Dashboard
Route::get('/dashboard', fn () => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

// 🔍 Dev-only testing view (optional)
Route::get('/test-filters', fn () => view('test-filters'));

// My Meals Plan
Route::middleware(['auth'])->group(function () {
    Route::get('/meal-plan', [MealPlanController::class, 'index'])->name('meal-plan.index');
    Route::post('/meal-plan', [MealPlanController::class, 'store'])->name('meal-plan.store');
    Route::delete('/meal-plan/{mealPlan}', [MealPlanController::class, 'destroy'])->name('meal-plan.destroy');
    Route::post('/meal-plan/store-generated', [MealPlanController::class, 'storeFromGenerated'])->name('meal-plan.storeGenerated');
    Route::get('/recipe-saved/{id}', [MealPlanController::class, 'showSaved'])->name('recipe.showSaved');
    Route::get('/recipe-saved/{id}', [MealPlanController::class, 'showSaved'])->name('recipe.saved.detail');
    Route::get('/meal-plan/{id}/edit', [MealPlanController::class, 'edit'])->name('meal-plan.edit');
Route::put('/meal-plan/{id}', [MealPlanController::class, 'update'])->name('meal-plan.update');


});

// Route::get('/recipe-saved-/{id}', function ($id) {
//     $recipe = \App\Models\Recipe::findOrFail($id);

//     return view('recipe-saved', ['recipe' => $recipe]);
// })->name('recipe.saved.detail');



require __DIR__.'/auth.php';

