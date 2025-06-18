<section x-data="{ editing: false }">
    <header>
        <h2 class="text-lg font-medium">
            {{ __('BMI Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('View and update your Body Mass Index (BMI) details.') }}
        </p>
    </header>

    <!-- Display Mode -->
    <div x-show="!editing" class="mt-6 space-y-2">
        <p><strong>Age:</strong> {{ $bmi->age }}</p>
        <p><strong>Gender:</strong> {{ ucfirst($bmi->gender) }}</p>
        <p><strong>Height:</strong> {{ $bmi->height }} cm</p>
        <p><strong>Weight:</strong> {{ $bmi->weight }} kg</p>
        <p><strong>BMI:</strong> {{ $bmi->bmi }}</p>

        <div class="mt-4">
            <x-primary-button @click="editing = true">{{ __('Update BMI Information') }}</x-primary-button>
        </div>
    </div>

    <!-- Edit Mode -->
    <form x-show="editing" x-cloak method="POST" action="{{ route('bmi.update') }}" class="mt-6 space-y-6">
        @csrf

        <div>
            <x-input-label for="age" :value="__('Age')" />
            <x-text-input id="age" name="age" type="number" class="mt-1 block w-full" :value="$bmi->age" required />
            <x-input-error :messages="$errors->get('age')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="height" :value="__('Height (cm)')" />
            <x-text-input id="height" name="height" type="number" class="mt-1 block w-full" :value="$bmi->height" required />
            <x-input-error :messages="$errors->get('height')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="weight" :value="__('Weight (kg)')" />
            <x-text-input id="weight" name="weight" type="number" class="mt-1 block w-full" :value="$bmi->weight" required />
            <x-input-error :messages="$errors->get('weight')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            <button type="button" @click="editing = false" class="text-sm text-gray-500 hover:underline">
                {{ __('Cancel') }}
            </button>
        </div>
    </form>
</section>
