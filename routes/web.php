<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\BMIController;
use Illuminate\Support\Facades\Route;
use App\Models\Ingredient;
use App\Models\Bmi;

// Google and Facebook OAuth routes
Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

Route::get('auth/facebook', [SocialAuthController::class, 'redirectToFacebook']);
Route::get('auth/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/generate', function () {
    return view('generate');
})->name('generate');

Route::post('/generate', [IngredientController::class, 'process'])->name('generate.process');

Route::get('/api/ingredients', [IngredientController::class, 'getIngredients']);

Route::get('/test-filters', function () {
    return view('test-filters');
});

Route::get('/recipe-detail/{index}', function ($index) {
    $recipes = session('generated_recipes', []);
    if (!isset($recipes[$index])) {
        abort(404);
    }
    return view('recipe-detail', ['recipe' => $recipes[$index]]);
})->name('recipe.detail');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/bmi', [ProfileController::class, 'modify'])->name('profile.bmi.edit');
    Route::get('/bmi/form', [BMIController::class, 'showForm'])->name('bmi.form');
    Route::post('/bmi/store', [BMIController::class, 'store'])->name('profile.bmi.update');
    Route::post('/bmi/update', [BmiController::class, 'update'])->name('bmi.update');
});

require __DIR__.'/auth.php';
