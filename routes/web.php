<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IngredientController;

use Illuminate\Support\Facades\Route;

use App\Models\Ingredient;

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


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
