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
<<<<<<< HEAD
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

                <div>
                    <button onclick="handleGroceryList()"
                        class="text-sm bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                        🛒 Generate Shopping List
                    </button>

                    <!-- Hidden textarea for copying -->
                    <textarea id="groceryListText" class="hidden"></textarea>

                    <!-- Copy to Clipboard Confirmation -->
                    <div id="copyMessage" class="hidden text-green-600 mt-2 font-medium">Copied to clipboard! ✅</div>

                    <button class="text-sm bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 mx-2">
                        🤍 Save
                    </button>
                    <button type="button" onclick="document.getElementById('addToPlanModal').classList.remove('hidden')"
                        class="text-sm bg-red-700 text-white px-4 py-2 rounded hover:bg-red-800 my-2">
                        📅 Add to Plan
                    </button>


                    @php
                        $ingredients = implode("%0A- ", $recipe['groceryLists']);
                        $waMessage = "🛒 CookEase Shopping List:%0A- " . $ingredients;
                    @endphp

                    <a href="https://wa.me/?text={{ $waMessage }}" target="_blank"
                        class="inline-block mt-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        📤 Share on WhatsApp
                    </a>
                </div>
            </div>


            <!-- Hidden print section -->
            <div id="printableGroceryList" class="hidden print:block p-6 max-w-md mx-auto bg-white">
                <h2 class="text-xl font-bold mb-4">🛒 Grocery Shopping List</h2>
                <ul class="list-disc pl-6 text-gray-800 space-y-1">
                    @foreach ($recipe['groceryLists'] as $item)
                        <li>{{ ucfirst($item) }}</li>
                    @endforeach
                </ul>
                <p class="mt-6 text-sm text-gray-500">Generated by CookEase - {{ now()->toDateString() }}</p>
            </div>


            <!-- Add To Plan Popup -->
            <!-- Modal -->
            <div id="addToPlanModal"
                class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
                <div class="bg-white p-6 rounded shadow-lg w-full max-w-md relative">

                    <!-- Close button -->
                    <button onclick="document.getElementById('addToPlanModal').classList.add('hidden')"
                        class="absolute top-2 right-2 text-gray-400 hover:text-red-600 text-xl">&times;</button>

                    <h2 class="text-lg font-semibold text-gray-800 mb-4">📅 Add This Recipe to Your Meal Plan</h2>

                    <form id="addToPlanForm" method="POST" action="{{ route('meal-plan.storeGenerated') }}">


                        @csrf
                        <input type="hidden" name="name" value="{{ $recipe['name'] }}">
                        <input type="hidden" name="description" value="{{ $recipe['description'] }}">
                        <input type="hidden" name="instructions" value="{{ $recipe['instructions'] }}">
                        @foreach ($recipe['ingredients'] ?? [] as $ingredient)
                            <input type="hidden" name="ingredients[]" value="{{ $ingredient }}">
                        @endforeach
                        <input type="hidden" name="duration" value="{{ $recipe['duration'] }}">
                        <input type="hidden" name="difficulty" value="{{ $recipe['difficulty'] }}">
                        <input type="hidden" name="servings" value="{{ $recipe['servings'] }}">
                        <input type="hidden" name="calories" value="{{ $recipe['calories'] }}">
                        <input type="hidden" name="image" value="{{ $recipe['image'] }}">



                        <label class="block mb-3">
                            <span class="text-sm text-gray-700">Date</span>
                            <input type="date" name="date" required
                                class="w-full mt-1 border rounded px-3 py-2 text-sm text-gray-800">
                        </label>

                        <label class="block mb-4">
                            <span class="text-sm text-gray-700">Meal Type</span>
                            <select name="meal_type" required
                                class="w-full mt-1 border rounded px-3 py-2 text-sm text-gray-800">
                                <option value="">Select Meal Type</option>
                                <option value="breakfast">Breakfast</option>
                                <option value="lunch">Lunch</option>
                                <option value="dinner">Dinner</option>
                                <option value="snack">Snacks</option>
                            </select>
                        </label>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                                Add to Plan
                            </button>
                        </div>
                    </form>


                </div>
            </div>

            <!-- Success Message -->
            <div id="successMessage"
                class="fixed bottom-6 right-6 bg-green-100 border border-green-300 text-green-800 px-6 py-4 rounded-lg shadow-lg z-50 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold">🎉 Meal Plan added successfully!</p>
                        <p class="text-sm text-gray-700 mt-1">You can now view your updated meal plan.</p>
                    </div>
                    <button onclick="document.getElementById('successMessage').classList.add('hidden')"
                        class="text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
                </div>
            </div>

            <!-- Error Message -->
<div id="errorMessage" class="fixed bottom-6 right-6 bg-red-100 border border-red-300 text-red-800 px-6 py-4 rounded-lg shadow-lg z-50 hidden">
    <div class="flex items-center justify-between">
        <div>
            <p class="font-semibold">😓 Failed to Add Meal Plan</p>
            <p id="errorDetail" class="text-sm text-gray-700 mt-1">Something went wrong.</p>
        </div>
        <button onclick="document.getElementById('errorMessage').classList.add('hidden')" class="text-red-500 hover:text-red-700 text-lg font-bold leading-none">&times;</button>
>>>>>>> bff0526 (updated meal plan)
    </div>

<<<<<<< HEAD
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


            <!-- Script -->
            <script>
                function handleGroceryList() {
                    const items = @json($recipe['groceryLists']);
                    const text = "🛒 CookEase Shopping List:\n\n" + items.map(item => "- " + item).join("\n");

                    // Copy to clipboard
                    const textarea = document.getElementById('groceryListText');
                    textarea.value = text;
                    textarea.classList.remove('hidden');
                    textarea.select();
                    document.execCommand('copy');
                    textarea.classList.add('hidden');

                    // Show copied message
                    const message = document.getElementById('copyMessage');
                    message.classList.remove('hidden');
                    setTimeout(() => message.classList.add('hidden'), 2000);

                    // Trigger print (optional)
                    window.print();
                }

document.getElementById('addToPlanForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    fetch("{{ route('meal-plan.storeGenerated') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
        },
        body: formData
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) {
            // Show pretty error toast
            document.getElementById('errorDetail').innerText = data.message || 'Something went wrong.';
            document.getElementById('errorMessage').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('errorMessage').classList.add('hidden');
            }, 5000);
            throw new Error(data.message);
        }

        // Show pretty success toast
        document.getElementById('addToPlanModal').classList.add('hidden');
        document.getElementById('successMessage').classList.remove('hidden');
        setTimeout(() => {
            document.getElementById('successMessage').classList.add('hidden');
        }, 4000);
    })
    .catch(error => {
        console.error(error);
    });
});

            </script>

        </div>

</x-app-layout>

