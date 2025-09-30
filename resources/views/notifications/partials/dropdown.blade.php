<div id="dropdownNotification"
    class="z-40 w-full max-w-md bg-white border border-gray-300 shadow-3xl flex flex-col"
    aria-labelledby="dropdownNotificationButton"
    style="max-height: 90vh;">

    <!-- Header with tabs -->
    <div class="px-3 py-3 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-[#23527c]">Notifications</h2>
        @php $activeFilter = $filter ?? 'all'; @endphp
        <div class="flex space-x-4 mt-2">
            <button onclick="loadDropdown('all')"
                class="tab-btn font-medium text-sm pb-1 {{ $activeFilter === 'all' ? 'text-white bg-[#23527c] px-3 py-1 rounded-full' : 'text-gray-500 hover:text-[#23527c]' }}">
                All
            </button>
            <button onclick="loadDropdown('unread')"
                class="tab-btn font-medium text-sm pb-1 {{ $activeFilter === 'unread' ? 'text-white bg-[#23527c] px-3 py-1 rounded-full' : 'text-gray-500 hover:text-[#23527c]' }}">
                Unread
            </button>
        </div>
    </div>

    <!-- Scrollable notification list -->
    <div class="flex-1 overflow-y-auto">

        {{-- ✅ New --}}
        @isset($newNotifications)
            @if($newNotifications->count())
                <div class="px-3 py-2 text-sm font-semibold text-[#23527c]">New</div>
                <div class="divide-y divide-gray-200">
                    @foreach($newNotifications as $note)
                        @include('notifications.partials.note', ['note' => $note])
                    @endforeach
                </div>
            @endif
        @endisset

        {{-- ✅ Today --}}
        @isset($todayNotifications)
            @if($todayNotifications->count())
                <div class="px-3 py-2 text-sm font-semibold text-[#23527c]">Today</div>
                <div class="divide-y divide-gray-200">
                    @foreach($todayNotifications as $note)
                        @include('notifications.partials.note', ['note' => $note])
                    @endforeach
                </div>
            @endif
        @endisset

        {{-- ✅ Earlier --}}
        @isset($earlierNotifications)
            @if($earlierNotifications->count())
                <div class="px-3 py-2 text-sm font-semibold text-[#23527c]">Earlier</div>
                <div class="divide-y divide-gray-200">
                    @foreach($earlierNotifications as $note)
                        @include('notifications.partials.note', ['note' => $note])
                    @endforeach
                </div>
            @endif
        @endisset

        {{-- ✅ Empty state --}}
        @if(
            (isset($newNotifications) && !$newNotifications->count()) &&
            (isset($todayNotifications) && !$todayNotifications->count()) &&
            (isset($earlierNotifications) && !$earlierNotifications->count())
        )
            <div class="px-4 py-4 text-sm text-gray-500">
                No notifications yet.
            </div>
        @endif
    </div>

    <!-- Footer -->
    <a href="{{ route('notifications.index') }}"
        class="block px-4 py-3 text-center font-medium text-sm text-[#23527c] hover:bg-[#f0f8ff] transition border-t border-gray-200 bg-white">
        See previous notifications
    </a>
</div>
