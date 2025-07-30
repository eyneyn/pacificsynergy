<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">

        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center  px-3 py-2 text-sm font-medium text-[#2d326b] bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center  px-3 py-2 text-sm font-medium text-[#2d326b] bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center  px-3 py-2 ml-3 text-sm font-medium text-[#2d326b] bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="relative inline-flex items-center  px-3 py-2 ml-3 text-sm font-medium text-[#2d326b] bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">

            <div>
                <span class="relative z-0 inline-flex rtl:flex-row-reverse shadow-sm rounded-md">
{{-- Previous Page Link --}}
@if ($paginator->onFirstPage())
    <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
        <span class="relative inline-flex items-center  px-3 py-2 text-sm text-gray-400 bg-white border border-gray-300 rounded-l-md cursor-not-allowed">
            Previous
        </span>
    </span>
@else
    <a href="{{ $paginator->previousPageUrl() }}"
       rel="prev"
       class="relative inline-flex items-center  px-3 py-2 text-sm text-[#2d326b] bg-white border border-gray-300 rounded-l-md leading-5 hover:bg-[#2d326b] hover:text-white focus:z-10 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150"
       aria-label="{{ __('pagination.previous') }}">
        Previous
    </a>
@endif

{{-- Pagination Elements --}}
@foreach ($elements as $element)
    {{-- "Three Dots" Separator --}}
    @if (is_string($element))
        <span aria-disabled="true">
            <span class="relative inline-flex items-center  px-3 py-2 text-sm font-medium text-[#2d326b] bg-white border border-gray-300 cursor-default leading-5">
                {{ $element }}
            </span>
        </span>
    @endif

    {{-- Array Of Links --}}
    @if (is_array($element))
        @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
                <span aria-current="page">
                    <span class="relative inline-flex items-center  px-3 py-2 text-sm font-medium text-white bg-[#2d326b] border border-gray-300 cursor-default leading-5">
                        {{ $page }}
                    </span>
                </span>
            @else
                <a href="{{ $url }}"
                   class="relative inline-flex items-center  px-3 py-2 text-sm font-medium text-[#2d326b] bg-white border border-gray-300 leading-5 hover:bg-[#2d326b] hover:text-white transition ease-in-out duration-150"
                   aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                    {{ $page }}
                </a>
            @endif
        @endforeach
    @endif
@endforeach


{{-- Next Page Link --}}
@if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}"
       rel="next"
       class="relative inline-flex items-center  px-3 py-2 -ml-px text-sm text-[#2d326b] bg-white border border-gray-300 rounded-r-md leading-5 hover:bg-[#2d326b] hover:text-white focus:z-10 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150"
       aria-label="{{ __('pagination.next') }}">
        Next
    </a>
@else
    <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
        <span class="relative inline-flex items-center  px-3 py-2 -ml-px text-sm text-gray-400 bg-white border border-gray-300 rounded-r-md cursor-not-allowed">
            Next
        </span>
    </span>
@endif
                </span>
            </div>
        </div>
    </nav>
    
