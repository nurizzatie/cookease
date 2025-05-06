<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GenerateController extends Controller
{
    public function process(Request $request)
    {
        $ingredients = $request->input('ingredients');

        // For now, just return back with a message
        return back()->with('message', "Received ingredients: $ingredients");
    }
}
