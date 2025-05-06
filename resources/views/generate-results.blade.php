<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Generated Recipes') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded-lg shadow">
            <h1 class="text-2xl font-bold mb-4">Recipes for: <span class="text-green-600">{{ $ingredients }}</span></h1>

            @if(count($recipes) > 0)
                <ul class="space-y-4">
                    @foreach($recipes as $recipe)
                        <li class="p-4 bg-white rounded shadow">
                            <pre class="whitespace-pre-wrap">{{ $recipe }}</pre>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No recipes found. Try adjusting your ingredients!</p>
            @endif

            <div class="mt-6">
                <a href="{{ route('generate') }}" class="inline-block px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                    Back to Generate
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
