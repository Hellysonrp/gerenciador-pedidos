@props(['column', 'title'])

@php
    $currentSort = request('sort');
    $currentOrder = request('order');

    $newSort = null;
    $newOrder = null;

    if ($currentSort === $column) {
        if ($currentOrder === 'asc') {
            $newSort = $column;
            $newOrder = 'desc';
        } else {
            // Third click clears sorting
            $newSort = null;
            $newOrder = null;
        }
    } else {
        // New column, start with ascending
        $newSort = $column;
        $newOrder = 'asc';
    }

    $sortIcon = match (true) {
        $currentSort === $column && $currentOrder === 'asc' => '↑',
        $currentSort === $column && $currentOrder === 'desc' => '↓',
        default => '',
    };
@endphp

<th {{ $attributes->merge(['class' => 'px-6 py-3 text-left text-sm font-medium text-gray-500']) }}>
    <a href="{{ request()->fullUrlWithQuery([
        'sort' => $newSort,
        'order' => $newOrder,
        'page' => null,
    ]) }}"
        class="flex items-center gap-1 hover:text-gray-700">
        {{ $title }}
        @if ($sortIcon)
            <span class="text-xs">{{ $sortIcon }}</span>
        @endif
    </a>
</th>
