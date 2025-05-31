<?php

namespace App\Http\Controllers;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'recipe_id' => 'required|exists:recipes,id',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|max:1000',
    ]);

    Review::create([
        'user_id' => Auth::id(),
        'recipe_id' => $request->recipe_id,
        'rating' => $request->rating,
        'comment' => $request->comment,
    ]);

    return back()->with('message', 'Review submitted!');
}
}
