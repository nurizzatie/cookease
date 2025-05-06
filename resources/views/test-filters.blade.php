<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Generate') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto p-6 lg:p-8">

        <!-- White card: Title + Form -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h1 class="text-3xl font-bold mb-4">Malaysian Ingredients, Infinite Recipes</h1>
            <p class="text-gray-600 mb-6">
                Share your ingredients, set your taste, and let CookEase suggest the perfect Malaysian recipe.
            </p>

            @if (session('message'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('message') }}
                </div>
            @endif

            <form class="mb-6" method="POST" action="{{ route('generate.process') }}">
                @csrf

                <div class="mb-4" x-data="{ showFilters: false }">
                    <label for="ingredients" class="block text-gray-700 font-medium mb-2">
                        Your Ingredients
                    </label>

                    <div class="relative">
                        <!-- Tagify Input Field -->
                        <input type="text" name="ingredients" id="ingredients"
                            placeholder="e.g., chicken, lemongrass, coconut milk"
                            class="w-full pl-4 pr-20 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400"
                            required>

                        <!-- Right-side Buttons -->
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 space-x-2">
                            <!-- Filter Toggle Button -->
                            <button type="button" @click="showFilters = !showFilters" class="text-gray-500 hover:text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 12.414V19a1 1 0 01-1.447.894l-4-2A1 1 0 019 17v-4.586L3.293 6.707A1 1 0 013 6V4z" />
                                </svg>
                            </button>

                            <!-- Submit (Search) Button -->
                            <button type="submit" class="text-gray-500 hover:text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35M16.65 16.65A7.5 7.5 0 1116.65 2a7.5 7.5 0 010 14.65z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Collapsible Filter Panel -->
                    <div x-show="showFilters" x-transition class="mt-4 p-4 bg-gray-50 border rounded-lg space-y-4">
                        <!-- Preferences -->
                        <div>
                            <label class="font-semibold">Preferences</label>
                            <div class="flex flex-wrap gap-2 mt-1">
                                <label><input type="checkbox" name="filters[]" value="halal"> Halal</label>
                                <label><input type="checkbox" name="filters[]" value="vegetarian"> Vegetarian</label>
                                <label><input type="checkbox" name="filters[]" value="lowcarb"> Low Carb</label>
                                <label><input type="checkbox" name="filters[]" value="highProtein"> High Protein</label>
                                <label><input type="checkbox" name="filters[]" value="glutenFree"> Gluten-Free</label>
                                <label><input type="checkbox" name="filters[]" value="dairyFree"> Dairy-Free</label>
                            </div>
                        </div>

                        <!-- Cooking Time -->
                        <div>
                            <label class="font-semibold">Cooking Time</label>
                            <div class="flex flex-wrap gap-2 mt-1">
                                <label><input type="radio" name="cooking_time" value="under15"> Under 15 min</label>
                                <label><input type="radio" name="cooking_time" value="under30"> Under 30 min</label>
                                <label><input type="radio" name="cooking_time" value="30to60"> 30-60 min</label>
                            </div>
                        </div>

                        <!-- Budget Level -->
                        <div>
                            <label class="font-semibold">Budget Level</label>
                            <div class="flex flex-wrap gap-2 mt-1">
                                <label><input type="radio" name="budget" value="any"> Any</label>
                                <label><input type="radio" name="budget" value="budgetFriendly"> Budget-Friendly</label>
                                <label><input type="radio" name="budget" value="premium"> Premium</label>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    Generate Recipe
                </button>
            </form>

            <!-- Yellow box: Example Prompts -->
            <div class="mt-8 p-6 bg-yellow-100 rounded-lg shadow">
                <div class="flex items-center mb-4">
                    <span class="text-2xl mr-2">👨‍🍳</span>
                    <h3 class="text-xl font-bold text-gray-800">Example Prompts</h3>
                </div>
                <ul class="list-disc list-inside text-gray-700 space-y-1">
                    <li>Chicken, bell peppers, and onions</li>
                    <li>I have tomatoes, onion, and pasta</li>
                    <li>Quick healthy dish with tofu and spinach</li>
                    <li>Easy dinner with only 3 ingredients: rice, egg, soy sauce</li>
                    <li>Rice, anchovies, and sambal</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Tagify CSS + JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.querySelector('input[name=ingredients]');
    var tagify = new Tagify(input, {
        whitelist: [],  // We’ll load this dynamically
        dropdown: {
            enabled: 1, // show suggestions after 1 character
            maxItems: 10
        }
    });

    // Fetch ingredient list from backend API
    fetch('/api/ingredients')
        .then(res => res.json())
        .then(function(ingredientList){
            tagify.settings.whitelist = ingredientList;
        });
});
</script>
