<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Generate Recipe') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto p-6 lg:p-8">

        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4">
                <h1 class="text-black text-6xl font-semibold font-['Inter'] mb-4">
                    Malaysian Ingredients, Infinite Recipes.
                </h1>

                @if (session('message'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                        {{ session('message') }}
                    </div>
                @endif

                <form class="w-full mb-6" method="POST" action="{{ route('generate.process') }}">
                    @csrf

                    <div class="mb-4" x-data="ingredientSearch()">
                        <label for="ingredients" class="block text-gray-700 font-medium mb-2">
                            Share your ingredients, set your taste, and let CookEase suggest the
                            perfect Malaysian recipe.
                        </label>

                        <div class="relative">
                            <input
                                type="text"
                                x-model="query"
                                @input="filterSuggestions"
                                name="ingredients"
                                id="ingredients"
                                placeholder="e.g., chicken, lemongrass, coconut milk"
                                class="w-full pl-4 pr-20 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400"
                                required
                            >

                            <div
                                x-show="suggestions.length > 0"
                                @click.outside="suggestions = []"
                                class="absolute z-10 bg-white w-full border mt-1 rounded-md shadow-md"
                            >
                                <ul>
                                    <template x-for="item in suggestions" :key="item">
                                        <li
                                            @click="selectSuggestion(item)"
                                            class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                                            x-text="item"
                                        ></li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        <!-- Recent Tags -->
                        <div class="mt-4">
                            <p class="text-sm text-gray-600 mb-1">Recent searches:</p>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="tag in recent" :key="tag">
                                    <span
                                        @click="query = tag"
                                        class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm cursor-pointer"
                                        x-text="tag"
                                    ></span>
                                </template>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div x-data="{ showFilters: false }">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 space-x-2">
                                <button type="button" @click="showFilters = !showFilters"
                                    class="text-gray-500 hover:text-green-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 12.414V19a1 1 0 01-1.447.894l-4-2A1 1 0 019 17v-4.586L3.293 6.707A1 1 0 013 6V4z" />
                                    </svg>
                                </button>
                            </div>
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
                    </div>

                    <div class="w-full flex justify-end mt-4">
                        <button type="submit" class="block px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                            Generate Recipe
                        </button>
                    </div>
                </form>

                <script>
                    function ingredientSearch() {
                        return {
                            query: '',
                            suggestions: [],
                            recent: @json($recentIngredients),
            frequent: @json($frequentNames), // 💥 GANTI sini, NOT pluck inside Blade
            ...


                            filterSuggestions() {
                                const all = [...new Set([...this.recent, ...this.frequent])];
                                this.suggestions = this.query
                                    ? all.filter(i => i.toLowerCase().includes(this.query.toLowerCase()))
                                    : [];
                            },

                            selectSuggestion(item) {
                                this.query = item;
                                this.suggestions = [];
                            }
                        }
                    }
                </script>

                <div class="mt-8 p-6 bg-yellow-100 rounded-lg shadow transition-transform hover:scale-105">
                    <div class="flex items-center mb-4">
                        <span class="text-2xl mr-2">🍲</span>
                        <h3 class="text-xl font-bold text-gray-800">Quick Tips</h3>
                    </div>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li class="cursor-pointer hover:underline" onclick="applyTip('chicken, rice, spinach')">
                            Type things like <span class="font-semibold">chicken</span>, <span class="font-semibold">rice</span>, or <span class="font-semibold">spinach</span> and explore Malaysian flavors!
                        </li>
                        <li class="cursor-pointer hover:underline" onclick="applyTip('tofu, spinach, garlic')">
                            Looking for vegetarian? Tofu + spinach + garlic works great!
                        </li>
                        <li class="cursor-pointer hover:underline" onclick="applyTip('chili, sambal')">
                            Want spicy? Add chili or sambal for a kick!
                        </li>
                    </ul>
                </div>

                <script>
                    function applyTip(text) {
                        document.querySelector('input[name=ingredients]').value = text;
                    }
                </script>

            </div>

            <div class="p-4">
                <div class="grid grid-cols-2 gap-4 grid-flow-dense">
                    <img src="{{ asset('images/dish5.jpg') }}" class="col-span-2 rounded-xl shadow-md hover:scale-105 transition">
                    <img src="{{ asset('images/dish2.jpg') }}" class="rounded-xl shadow-md hover:scale-105 transition">
                    <img src="{{ asset('images/dish3.jpg') }}" class="rounded-xl shadow-md hover:scale-105 transition">
                    <img src="{{ asset('images/dish4.jpg') }}" class="rounded-xl shadow-md hover:scale-105 transition">
                    <img src="{{ asset('images/dish6.jpg') }}" class="rounded-xl shadow-md hover:scale-105 transition">
                </div>
            </div>
        </div>

</x-app-layout>

<script src="https://unpkg.com/alpinejs" defer></script>
