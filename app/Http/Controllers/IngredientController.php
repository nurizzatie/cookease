<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class IngredientController extends Controller
{
    public function getIngredients()
    {
        $ingredients = DB::table('ingredients')->pluck('name');
        return response()->json($ingredients);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'ingredients' => 'required|string',
            'filters' => 'array',
            'cooking_time' => 'nullable|string',
            'budget' => 'nullable|string',
        ]);

        // Log raw incoming data for debugging
    \Log::info('Incoming ingredients raw:', ['raw' => $validated['ingredients']]);
 // Decode JSON array from Tagify input
 $ingredientsInput = collect(json_decode($validated['ingredients'], true))
 ->pluck('value')
 ->toArray();

\Log::info('Processed ingredients array:', ['array' => $ingredientsInput]);


    $allowedIngredients = DB::table('ingredients')->pluck('name')->map(fn($n) => strtolower($n))->toArray();

        $cleanedIngredients = [];
        foreach ($ingredientsInput as $ingredient) {
            $ingredient = trim(strtolower($ingredient));
            if (!in_array($ingredient, $allowedIngredients)) {
                return back()->with('message', 'Sorry, the ingredient "' . $ingredient . '" is not recognized.');
            }
            $cleanedIngredients[] = $ingredient;
        }

        $ingredients = implode(', ', $cleanedIngredients);
        $filters = $validated['filters'] ?? [];
        $cookingTime = $validated['cooking_time'] ?? '';
        $budget = $validated['budget'] ?? '';

        $apiKey = config('services.groq.key');

        if (!$apiKey) {
            Log::error('Groq API key is missing.');
            return back()->with('message', 'AI service is currently unavailable. Please contact the admin.');
        }

        // Build dynamic prompt
        $filterText = implode(', ', $filters);
        $prompt = "Generate 3 Malaysian recipes using: $ingredients";
        if ($filterText) $prompt .= ", preferences: $filterText";
        if ($cookingTime) $prompt .= ", cooking time: $cookingTime";
        if ($budget) $prompt .= ", budget: $budget";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'meta-llama/llama-4-scout-17b-16e-instruct',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $json = $response->json();

            if (isset($json['error'])) {
                Log::error('Groq API error: ' . $json['error']['message']);
                return back()->with('message', 'Failed to generate recipe: ' . $json['error']['message']);
            }

            $aiContent = $json['choices'][0]['message']['content'] ?? 'No recipes found.';
            $recipes = explode("\n\n", $aiContent);

            return view('generate-results', compact('ingredients', 'recipes'));

        } catch (\Exception $e) {
            Log::error('Groq API exception: ' . $e->getMessage());
            return back()->with('message', 'An error occurred while contacting the AI service.');
        }
    }
}
