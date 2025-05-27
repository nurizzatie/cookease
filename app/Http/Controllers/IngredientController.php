<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class IngredientController extends Controller
{
    public function getIngredients()
    {
        // Get all ingredient names from database
        $ingredients = DB::table('ingredients')->pluck('name');
        return response()->json($ingredients);
    }

    public function process(Request $request)
    {
        // ✅ Validate incoming request
        $validated = $request->validate([
            'ingredients'   => 'required|string',
            'filters'       => 'array',
            'cooking_time'  => 'nullable|string',
            'budget'        => 'nullable|string',
        ]);

        // ✅ Log raw input from Tagify for debugging
        Log::info('Incoming ingredients raw:', ['raw' => $validated['ingredients']]);

        // ✅ Decode JSON input and remove emojis (if any)
        $ingredientsInput = collect(json_decode($validated['ingredients'], true))
            ->pluck('value')
            ->map(function ($item) {
                // Remove leading emojis + space (if present)
                return preg_replace('/^[^\w\s]+ /u', '', $item);
            })
            ->toArray();

        Log::info('Cleaned ingredients array:', ['array' => $ingredientsInput]);

        // ✅ Load allowed ingredient names from database (lowercased)
      $allowedIngredients = DB::table('ingredients')->pluck('name')
        ->map(fn($n) => strtolower($n))
        ->toArray();

    $cleanedIngredients = [];

    foreach ($ingredientsInput as $ingredient) {
        $ingredient = trim(strtolower($ingredient));

    if (!in_array($ingredient, $allowedIngredients)) {
        // Insert new ingredient into DB
        DB::table('ingredients')->insert([
            'name'       => $ingredient,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info("✅ New ingredient added to DB: $ingredient");

        // Add to allowed list so no duplicate rejection
        $allowedIngredients[] = $ingredient;
    }

    $cleanedIngredients[] = $ingredient;
}


        // ✅ Prepare data for API call
        $ingredients = implode(', ', $cleanedIngredients);
        $filters     = $validated['filters'] ?? [];
        $cookingTime = $validated['cooking_time'] ?? '';
        $budget      = $validated['budget'] ?? '';

        $apiKey = config('services.groq.key');

        if (!$apiKey) {
            Log::error('Groq API key is missing.');
            return back()->with('message', 'AI service is currently unavailable. Please contact the admin.');
        }

        // ✅ Build AI prompt
        $user = auth()->user();
        $bmiRecord = $user->bmi; // assuming one-to-one
        $bmiValue = $bmiRecord ? $bmiRecord->getBmiAttribute() : null;

        $bmiCategory = 'normal';

        if ($bmiValue) {
            if ($bmiValue < 18.5) $bmiCategory = 'underweight';
            elseif ($bmiValue >= 25) $bmiCategory = 'overweight';
            elseif ($bmiValue >= 30) $bmiCategory = 'obese';
        }

        $filterText = implode(', ', $filters);

        $extraInfo = '';
        if ($filterText)   $extraInfo .= " Take into account these preferences: $filterText.";
        if ($cookingTime)  $extraInfo .= " Try to keep the cooking time around $cookingTime.";
        if ($budget)       $extraInfo .= " Ensure the recipes fit a $budget budget.";
        if ($bmiValue)     $extraInfo .= " The user has a BMI of $bmiValue ($bmiCategory), so recommend recipes that support a healthy diet for this condition.";

        $prompt = <<<PROMPT
Generate 12 Malaysian recipes using the following ingredients: $ingredients.$extraInfo

Each recipe must be in **JSON format** and include:
- name (string)
- description (string)
- duration (string, e.g. "30 minutes")
- servings (number)
- difficulty (easy/medium/hard)
- calories (estimated total per recipe in kcal)
- image (use any placeholder or leave blank — image will be fetched separately)
- ingredients (array of strings, give detail ingredient's measurement)
- groceryLists (array of strings, ingredient without measurement for grocery shopping list)
- instructions (string, give detail instruction)

Return **only a JSON array** of recipe objects. Do not include any explanation or introduction text.
PROMPT;


        try {
            // ✅ Make API call to Groq
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'    => 'meta-llama/llama-4-scout-17b-16e-instruct',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $json = $response->json();

            if (isset($json['error'])) {
                Log::error('Groq API error: ' . $json['error']['message']);
                return back()->with('message', 'Failed to generate recipe: ' . $json['error']['message']);
            }

            // ✅ Process AI response
            $aiContent = $json['choices'][0]['message']['content'] ?? null;

            Log::info('Groq AI Response:', ['content' => $aiContent]);

            try {
                // Strip Markdown-style ```json block if present
                $aiContent = trim($aiContent);
                if (str_starts_with($aiContent, '```json')) {
                    $aiContent = preg_replace('/^```json\s*/', '', $aiContent); // remove starting ```json
                    $aiContent = preg_replace('/```$/', '', $aiContent);        // remove ending ```
                    $aiContent = trim($aiContent);
                }

                // Try decoding cleaned JSON
                $recipes = json_decode($aiContent, true);
                if (!is_array($recipes)) {
                    throw new \Exception('Decoded result is not an array.');
                }
            } catch (\Exception $e) {
                Log::error('Failed to decode AI JSON: ' . $e->getMessage());
                return back()->with('message', 'AI did not return usable recipe data.');
            }

            // ✅ Add real image to each recipe from Pixabay
            foreach ($recipes as &$recipe) {
                $recipe['image'] = $this->getImageFromPixabay($recipe['name']);
            }

            session(['generated_recipes' => $recipes]);
            return view('generate-results', compact('ingredients', 'recipes'));

        } catch (\Exception $e) {
            Log::error('Groq API exception: ' . $e->getMessage());
            return back()->with('message', 'An error occurred while contacting the AI service.');
        }
    }

    protected function getImageFromPixabay($query)
    {
        $response = Http::get('https://pixabay.com/api/', [
            'key'        => config('services.pixabay.key'),
            'q'          => $query,
            'image_type' => 'photo',
            'category'   => 'food',
            'safesearch' => true,
            'per_page'   => 3,
        ]);

        if ($response->successful() && isset($response['hits'][0]['webformatURL'])) {
            return $response['hits'][0]['webformatURL'];
        }

        return 'https://via.placeholder.com/300x200'; // fallback image
    }
}
