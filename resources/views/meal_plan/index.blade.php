<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Meal Plans') }}
        </h2>
    </x-slot>

    <div class="text-center max-w-4xl mx-auto py-8 px-4">

    <div class="flex justify-start mb-6 border-b pb-4">
        
        <form method="GET" action="{{ route('meal-plan.index') }}" class="mb-6">
            <label for="date" class="text-sm font-medium text-gray-700 mr-2">📅 Date:</label>
            <select name="date" id="date" onchange="this.form.submit()"
                class="border-gray-300  px-3 py-1 text-sm shadow-sm">
                @foreach ($availableDates as $availableDate)
                    <option value="{{ $availableDate }}" {{ $date == $availableDate ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::parse($availableDate)->format('l, d M Y') }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

        @if ($plans->isNotEmpty())

            <div class="text-center mb-6 text-gray-700 text-lg">
                🍽 Your menu for <strong>{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</strong>
            </div>

            @php
                $categories = ['breakfast' => '🍳 Breakfast', 'lunch' => '🍛 Lunch', 'dinner' => '🍽 Dinner', 'snack' => '🍪 Snacks'];
            @endphp

            @foreach ($categories as $key => $label)
               @php $mealsByType = $plans[$key] ?? collect(); @endphp


                @if ($mealsByType->isNotEmpty())
                    <div class="text-left mb-4 mt-10">
                        <h3 class="text-xl font-semibold text-gray-800">{{ $label }}</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach ($mealsByType as $meal)
                            @php $recipe = $meal->recipe; @endphp

                            <div class="rounded overflow-hidden shadow-lg flex flex-col bg-white">
                                <a href="{{ route('recipe.saved.detail', ['id' => $recipe->id]) }}">
                                    <div class="relative h-48 w-full overflow-hidden">
                                        <img src="{{ $recipe->image }}" alt="recipe image" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black bg-opacity-20 hover:bg-opacity-5 transition duration-300"></div>
                                    </div>
                                </a>
                                <div class="px-6 py-4 mb-auto">
                                    <a href="{{ route('recipe.saved.detail', ['id' => $recipe->id]) }}"
                                       class="font-medium text-lg inline-block hover:text-indigo-600 transition mb-2">
                                        {{ $recipe->name }}
                                    </a>
                                    <p class="text-gray-500 text-sm">{{ $recipe->description }}</p>
                                </div>
                                <div class="px-6 py-3 flex flex-wrap gap-3 justify-between bg-gray-100 text-xs text-gray-900">
                                    <span>📅 {{ $meal->date }}</span>
                                    <span>🍽 {{ ucfirst($meal->meal_type) }}</span>
                                    <span>🔥 {{ $recipe->calories ?? 'N/A' }} kcal</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach

        @else
            <div class="h-[60vh] flex items-center justify-center">
                <div class="text-gray-600 px-4 py-6 rounded text-center max-w-xl">
                    <h2 class="text-xl mb-3">😴 No meals planned for this date.</h2>
                    <p class="text-base mb-5">Let’s fix that by generating something delicious.</p>
                    <a href="{{ route('generate') }}"
                        class="inline-block bg-red-500 hover:bg-red-600 text-white text-sm px-6 py-2 rounded shadow transition-transform hover:scale-105">
                        🍳 Generate Recipe
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
