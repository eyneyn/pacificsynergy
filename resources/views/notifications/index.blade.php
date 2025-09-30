@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-8 bg-[#fcfcfc] border border-gray-200 shadow shadow-md rounded-md">

    <!-- Header -->
    <div class="px-3 py-3 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-[#23527c]">Notifications</h2>

        <!-- Filter Tabs -->
        <div class="flex space-x-4 mt-2">
            <a href="{{ route('notifications.index', ['filter' => 'all']) }}"
               class="text-sm font-medium pb-1 {{ ($filter ?? 'all') === 'all' 
                   ? 'text-white bg-[#23527c] px-3 py-1 rounded-full' 
                   : 'text-gray-500 hover:text-[#23527c]' }}">
                All
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
               class="text-sm font-medium pb-1 {{ ($filter ?? 'all') === 'unread' 
                   ? 'text-white bg-[#23527c] px-3 py-1 rounded-full' 
                   : 'text-gray-500 hover:text-[#23527c]' }}">
                Unread
            </a>
        </div>
    </div>

    <!-- Notifications Grouped -->
    <div class="divide-y divide-gray-200">

    {{-- ✅ New --}}
    @if($newNotifications->count())
        <div class="px-6 py-2 text-sm font-semibold text-[#23527c]">New</div>
        @foreach($newNotifications as $note)
            <form action="{{ route('notifications.read', $note->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <button type="submit"
                    class="w-full text-left block px-6 py-4 transition hover:bg-[#e2f2ff] {{ $note->is_read ? '' : 'bg-[#f0f8ff]' }}">
                    <p class="text-gray-800 text-sm mb-1">{!! $note->message !!}</p>
                    <p class="text-xs text-blue-600">{{ $note->created_at->diffForHumans() }}</p>
                </button>
            </form>
        @endforeach
    @endif

        {{-- ✅ Today --}}
        @if($todayNotifications->count())
            <div class="px-6 py-2 text-sm font-semibold text-[#23527c]">Today</div>
            @foreach($todayNotifications as $note)
                <form action="{{ route('notifications.read', $note->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="submit"
                        class="w-full text-left block px-6 py-4 transition hover:bg-[#e2f2ff] {{ $note->is_read ? '' : 'bg-[#f0f8ff]' }}">
                        <p class="text-gray-800 text-sm mb-1">{!! $note->message !!}</p>
                        <p class="text-xs text-blue-600">{{ $note->created_at->diffForHumans() }}</p>
                    </button>
                </form>
            @endforeach
        @endif

        {{-- ✅ Earlier --}}
        @if($earlierNotifications->count())
            <div class="px-6 py-2 text-sm font-semibold text-[#23527c]">Earlier</div>

            @foreach($earlierNotifications as $index => $note)
                <form action="{{ route('notifications.read', $note->id) }}" method="POST"
                    class="{{ $index >= 5 ? 'hidden earlier-hidden' : '' }}">
                    @csrf
                    @method('PATCH')

                    <button type="submit"
                        class="w-full text-left block px-6 py-4 transition hover:bg-[#e2f2ff] {{ $note->is_read ? '' : 'bg-[#f0f8ff]' }}">
                        <p class="text-gray-800 text-sm mb-1">{!! $note->message !!}</p>
                        <p class="text-xs text-blue-600">{{ $note->created_at->diffForHumans() }}</p>
                    </button>
                </form>
            @endforeach

            {{-- Show button only if more than 5 --}}
            @if($earlierNotifications->count() > 5)
                <div class="px-6 py-3 text-center">
                    <button id="showEarlierBtn" type="button"
                        class="text-sm font-medium text-[#23527c] hover:underline cursor-pointer">
                        See previous notifications
                    </button>
                </div>
            @endif
        @endif

        {{-- ✅ Empty state --}}
        @if(!$newNotifications->count() && !$todayNotifications->count() && !$earlierNotifications->count())
            <div class="px-6 py-4 text-sm text-gray-500">No notifications found.</div>
        @endif
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const btn = document.getElementById("showEarlierBtn");
        if (btn) {
            btn.addEventListener("click", function () {
                document.querySelectorAll(".earlier-hidden").forEach(el => {
                    el.classList.remove("hidden");
                });
                btn.parentElement.remove(); // remove the button after showing
            });
        }
    });
</script>


@endsection
