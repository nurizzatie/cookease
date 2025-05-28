<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\HealthGoalController;
use App\Http\Controllers\BMIController;
use App\Http\Controllers\GenerateController;

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

    // 🔍 Recipe Detail
    Route::get('/recipe-detail/{index}', function ($index) {
        $recipes = session('generated_recipes', []);
        if (!isset($recipes[$index])) {
            abort(404);
        }
        return view('recipe-detail', ['recipe' => $recipes[$index]]);
    })->name('recipe.detail');
});

// 🌐 Landing and OAuth
Route::get('/', fn () => redirect()->route('login'));

Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
Route::get('auth/facebook', [SocialAuthController::class, 'redirectToFacebook']);
Route::get('auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);

// 📊 Dashboard
Route::get('/dashboard', fn () => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

// 🔍 Dev-only testing view (optional)
Route::get('/test-filters', fn () => view('test-filters'));



require __DIR__.'/auth.php';

