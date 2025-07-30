@props([
    'field',
    'label',
    'currentSort' => null,
    'currentDirection' => 'asc',
    'route' => null,
])

@php
    $sortParams = request()->except(['sort', 'direction']);
    $sortParams['sort'] = $field;
    $sortParams['direction'] = ($currentSort === $field && $currentDirection === 'asc') ? 'desc' : 'asc';
@endphp

<a href="{{ route($route, $sortParams) }}"
   class="relative flex items-center w-full text-white no-underline hover:text-gray-200 transition-colors duration-200">
    
    <!-- Centered Label -->
    <span class="absolute left-1/2 transform -translate-x-1/2">{{ $label }}</span>

    <!-- Right Aligned Icon -->
    <span class="ml-auto">
        @if($currentSort === $field)
            @if($currentDirection === 'asc')
                {{-- ASC icon --}}
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 200 200">
                    <path d="M152.72,35.75a9.67,9.67,0,0,0-14,0c-3,2-3.5,5-3.5,8v113.5a10,10,0,0,0,20,0V66.75l7.5,7.5a9.67,9.67,0,0,0,14,0c4-3.5,4-10,.5-14Z"></path>
                    <path d="M110.22,142.75h-80a10,10,0,0,0,0,20h80a10,10,0,1,0,0-20Z"></path>
                    <path d="M110.22,62.75h-80a10,10,0,0,0,0,20h80a10,10,0,0,0,0-20Z"></path>
                    <path d="M30.22,122.75h70a10,10,0,0,0,0-20h-70a10,10,0,0,0,0,20Z"></path>
                </svg>
            @else
                {{-- DESC icon --}}
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 200 200">
                    <path d="M110.22,117.75h-80a10,10,0,0,0,0,20h80a10,10,0,0,0,0-20Z"></path>
                    <path d="M177.22,125.75a9.67,9.67,0,0,0-14,0l-8,7.5V42.75a10,10,0,0,0-20,0v113.5a8.29,8.29,0,0,0,3,8,9.67,9.67,0,0,0,14,0l24.5-24.5a10.13,10.13,0,0,0,.5-14Z"></path>
                    <path d="M110.22,37.75h-80a10,10,0,0,0,0,20h80a10,10,0,0,0,0-20Z"></path>
                    <path d="M30.22,97.75h70a10,10,0,0,0,0-20h-70a10,10,0,0,0,0,20Z"></path>
                </svg>
            @endif
        @else
            {{-- Neutral icon --}}
            <svg class="w-4 h-4 opacity-30" fill="currentColor" viewBox="0 0 907.62 907.619">
                <path d="M591.672,907.618c28.995,0,52.5-23.505,52.5-52.5V179.839l42.191,41.688c10.232,10.11,23.567,15.155,36.898,15.155
                c13.541,0,27.078-5.207,37.347-15.601c20.379-20.625,20.18-53.865-0.445-74.244L626.892,15.155C617.062,5.442,603.803,0,589.993,0
                c-0.104,0-0.211,0-0.314,0.001c-13.923,0.084-27.244,5.694-37.03,15.6l-129.913,131.48c-20.379,20.625-20.18,53.865,0.445,74.244
                c20.626,20.381,53.866,20.181,74.245-0.445l41.747-42.25v676.489C539.172,884.113,562.677,907.618,591.672,907.618z"></path>
                <path d="M315.948,0c-28.995,0-52.5,23.505-52.5,52.5v676.489l-41.747-42.25c-20.379-20.625-53.62-20.825-74.245-0.445
                c-20.625,20.379-20.825,53.619-0.445,74.244l129.912,131.479c9.787,9.905,23.106,15.518,37.029,15.601
                c0.105,0.001,0.21,0.001,0.315,0.001c13.81,0,27.07-5.442,36.899-15.155L484.44,760.78c20.625-20.379,20.824-53.619,0.445-74.244
                c-20.379-20.626-53.62-20.825-74.245-0.445l-42.192,41.688V52.5C368.448,23.505,344.943,0,315.948,0z"></path>
            </svg>
        @endif
    </span>
</a>
