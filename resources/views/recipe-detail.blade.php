<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $recipe['name'] }}
        </h2>
    </x-slot>

    @if (session('message'))
        <div 
            x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show" 
            class="max-w-4xl mx-auto mt-6">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    <div class="py-10 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
        <div class="bg-white shadow rounded-xl p-6">
            <img src="{{ $recipe['image'] }}" alt="recipe image" class="w-full h-64 object-cover rounded mb-6">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm text-gray-600 mb-8">
                <p><strong>⏱ Duration:</strong> {{ $recipe['duration'] }}</p>
                <p><strong>🍽 Servings:</strong> {{ $recipe['servings'] }}</p>
                <p><strong>📊 Difficulty:</strong> {{ ucfirst($recipe['difficulty']) }}</p>
                <p><strong>🔥 Calories:</strong> {{ $recipe['calories'] ?? 'N/A' }} kcal</p>
            </div>

            <h3 class="text-lg font-semibold mb-2">📝 Description</h3>
            <p class="text-gray-700 mb-4">{{ $recipe['description'] }}</p>

            <h3 class="text-lg font-semibold mb-2">🥗 Ingredients</h3>
            <ul class="list-disc pl-5 text-gray-700 mb-4">
                @foreach ($recipe['ingredients'] as $ingredient)
                    <li>{{ is_array($ingredient) ? implode(' ', $ingredient) : $ingredient }}</li>
                @endforeach
            </ul>

            <h3 class="text-lg font-semibold">👨‍🍳 Instructions</h3>
            <p class="text-gray-700 whitespace-pre-line">
                {{ is_array($recipe['instructions']) ? implode("\n", $recipe['instructions']) : $recipe['instructions'] }}
            </p>

            <div class="mt-8 flex justify-end">
                <div class="flex space-x-2">
                    <!-- Hidden textarea for copying -->
                    <textarea id="groceryListText" class="hidden"></textarea>

                    <!-- Copy to Clipboard Confirmation -->
                    <div id="copyMessage" class="hidden text-green-600 mt-2 font-medium">Copied to clipboard! ✅</div>
                    
                    <!-- 🛒 Dropdown Grocery List Actions -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-sm bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                            🛒 Generate Shopping List
                        </button>

                        <div x-show="open" @click.away="open = false" class="absolute z-10 mt-2 w-56 bg-white rounded shadow">
                            <button onclick="printGroceryList()" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">🖨️ Print</button>
                            <button onclick="copyGroceryList()" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">📋 Copy to Clipboard</button>
                            <a href="https://wa.me/?text={{ urlencode('🛒 CookEase Shopping List:' . "\n- " . implode("\n- ", $recipe['groceryLists'])) }}" 
                               target="_blank" 
                               class="block px-4 py-2 text-sm hover:bg-gray-100">📤 Share on WhatsApp</a>
                        </div>
                    </div>

                    <!-- ❤️ Save / Unsave -->
                    @if ($isSession)
                        <form method="POST" action="{{ route('recipe.save') }}">
                            @csrf
                            <input type="hidden" name="name" value="{{ $recipe['name'] }}">
                            <input type="hidden" name="description" value="{{ $recipe['description'] }}">
                            <input type="hidden" name="duration" value="{{ $recipe['duration'] }}">
                            <input type="hidden" name="servings" value="{{ $recipe['servings'] }}">
                            <input type="hidden" name="difficulty" value="{{ $recipe['difficulty'] }}">
                            <input type="hidden" name="calories" value="{{ $recipe['calories'] }}">
                            <input type="hidden" name="image" value="{{ $recipe['image'] }}">
                            <input type="hidden" name="instructions" value="{{ is_array($recipe['instructions']) ? implode("\n", $recipe['instructions']) : $recipe['instructions'] }}">
                            <input type="hidden" name="ingredients" value="{{ json_encode($recipe['ingredients']) }}">
                            <input type="hidden" name="groceryLists" value="{{ json_encode($recipe['groceryLists']) }}">
                            <button type="submit" class="text-sm bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">🤍 Save</button>
                        </form>
                    @else
                        @if ($isFavorited && isset($recipeId))
                            <form method="POST" action="{{ route('recipe.unsave', $recipeId) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">💔 Unsave</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('recipe.save') }}">
                                @csrf
                                <input type="hidden" name="name" value="{{ $recipe['name'] }}">
                                <input type="hidden" name="description" value="{{ $recipe['description'] }}">
                                <input type="hidden" name="duration" value="{{ $recipe['duration'] }}">
                                <input type="hidden" name="servings" value="{{ $recipe['servings'] }}">
                                <input type="hidden" name="difficulty" value="{{ $recipe['difficulty'] }}">
                                <input type="hidden" name="calories" value="{{ $recipe['calories'] }}">
                                <input type="hidden" name="image" value="{{ $recipe['image'] }}">
                                <input type="hidden" name="instructions" value="{{ $recipe['instructions'] }}">
                                <input type="hidden" name="ingredients" value="{{ json_encode($recipe['ingredients']) }}">
                                <input type="hidden" name="groceryLists" value="{{ json_encode($recipe['groceryLists']) }}">
                                <button type="submit" class="text-sm bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">🤍 Save</button>
                            </form>
                        @endif
                    @endif

                    <!-- 🗓️ Add to Meal Plan -->
                    <button class="text-sm bg-red-700 text-white px-4 py-2 rounded hover:bg-red-800">
                        📅 Add to Plan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔒 Hidden Grocery List for Print -->
    <div id="printableGroceryList" class="hidden print:block p-6 max-w-md mx-auto bg-white">
        <h2 class="text-xl font-bold mb-4">🛒 Grocery Shopping List</h2>
        <ul class="list-disc pl-6 text-gray-800 space-y-1">
            @foreach ($recipe['groceryLists'] as $item)
                <li>{{ ucfirst($item) }}</li>
            @endforeach
        </ul>
        <p class="mt-6 text-sm text-gray-500">Generated by CookEase - {{ now()->toDateString() }}</p>
    </div>

    <script>
        const groceryItems = @json($recipe['groceryLists']);
        const groceryText = "CookEase Shopping List:\n\n" + groceryItems.map(item => "- " + item).join("\n");

        function printGroceryList() {
            const printable = document.getElementById('printableGroceryList');
            printable.classList.remove('hidden');
            window.print();
            setTimeout(() => printable.classList.add('hidden'), 100);
        }

        function copyGroceryList() {
            const textarea = document.getElementById('groceryListText');
            textarea.value = groceryText;
            textarea.classList.remove('hidden');
            textarea.select();
            document.execCommand('copy');
            textarea.classList.add('hidden');

            const message = document.getElementById('copyMessage');
            message.classList.remove('hidden');
            setTimeout(() => message.classList.add('hidden'), 2000);
        }
    </script>
</x-app-layout>
