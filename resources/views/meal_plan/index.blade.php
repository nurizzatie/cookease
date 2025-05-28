<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Meal Plans') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8 px-4">

        <!-- @if (session('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('message') }}
            </div>
        @endif -->

        <!-- 
        <hr class="my-6">

        @foreach($plans as $date => $dailyMeals)
            <h3 class="text-lg font-bold mt-4 mb-2">{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</h3>
            <ul class="list-disc list-inside">
                @foreach($dailyMeals as $meal)
                    <li>
                        <strong>{{ ucfirst($meal->meal_type) }}:</strong>
                        {{ $meal->recipe->title }}
                        <form action="{{ route('meal-plan.destroy', $meal->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 text-sm ml-2" onclick="return confirm('Remove this meal?')">Remove</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endforeach

    </div>
</x-app-layout>
