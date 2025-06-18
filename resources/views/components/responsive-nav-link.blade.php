@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-yellow-400 dark:border-yellow-600 text-start text-base font-medium text-yellow-700 dark:text-yellow-300 bg-yellow-50 dark:bg-yellow-900/50 focus:outline-none focus:text-yellow-800 dark:focus:text-yellow-200 focus:bg-yellow-100 dark:focus:bg-yellow-900 focus:border-yellow-700 dark:focus:border-yellow-300 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-yellow-800 dark:hover:text-yellow-200 hover:bg-yellow-50 dark:hover:bg-yellow-700 hover:border-yellow-300 dark:hover:border-yellow-600 focus:outline-none focus:text-yellow-800 dark:focus:text-yellow-200 focus:bg-yellow-50 dark:focus:bg-yellow-700 focus:border-yellow-300 dark:focus:border-yellow-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
