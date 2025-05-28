<section x-data="{ editingGoal: false }">
    <header>
        <h2 class="text-lg font-medium">
            {{ __('Health Goal') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('View and update your health goal based on your objectives.') }}
        </p>
    </header>

    <!-- Display Mode -->
    <div x-show="!editingGoal" class="mt-6 space-y-2">
        <p><strong>Current Goal:</strong> 
            @if($healthGoal)
                {{ ucfirst(str_replace('_', ' ', $healthGoal)) }}
            @else
                <em>No goal set</em>
            @endif
        </p>

        <p><strong>Recommended Calorie Target:</strong>
            @if($calorieTarget)
                {{ $calorieTarget }} kcal/day
            @else
                <em>Not available (BMI required)</em>
            @endif
        </p>

        <div class="mt-4">
            <x-primary-button @click="editingGoal = true">{{ __('Update Health Goal') }}</x-primary-button>
        </div>
    </div>

    <!-- Edit Mode -->
    <form x-show="editingGoal" x-cloak method="POST" action="{{ route('health_goals.store') }}" class="mt-6 space-y-6">
        @csrf
        

        <div>
            <x-input-label for="goal" :value="__('Select Goal')" />
            <select name="goal" id="goal" class="mt-1 block w-full rounded-md shadow-sm">
                <option value="lose_weight" {{ $healthGoal === 'lose_weight' ? 'selected' : '' }}>Lose Weight</option>
                <option value="gain_weight" {{ $healthGoal === 'gain_weight' ? 'selected' : '' }}>Gain Weight</option>
                <option value="maintain_weight" {{ $healthGoal === 'maintain_weight' ? 'selected' : '' }}>Maintain Weight</option>
            </select>
            <x-input-error :messages="$errors->get('goal')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            <button type="button" @click="editingGoal = false" class="text-sm text-gray-500 hover:underline">
                {{ __('Cancel') }}
            </button>

            @if (session('success'))
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-gray-600 dark:text-gray-400">
                    {{ session('success') }}
                </p>
            @endif
        </div>
    </form>
</section>
