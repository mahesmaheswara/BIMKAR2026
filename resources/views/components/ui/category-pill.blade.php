@props([
    'label',
    'active' => false,
])

<span
    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
        {{ $active ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
    {{ $label }}
</span>