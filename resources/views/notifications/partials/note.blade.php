<form action="{{ route('notifications.read', $note->id) }}" method="POST" class="mb-0">
    @csrf
    @method('PATCH')

    <button type="submit"
        formaction="{{ route('notifications.read', $note->id) }}"
        formmethod="POST"
        class="w-full text-left flex items-start px-4 py-3 transition {{ $note->is_read ? 'bg-white hover:bg-[#f0f8ff]' : 'bg-[#f0f8ff]' }}"
        onclick="window.location.href='{{ $note->url ?? '#' }}'">
        
        <div class="flex-1 text-sm text-gray-700">
            <p class="mb-1">
                @if($note->type === 'analytics_warning')
                    <span class="text-red-600 font-bold">⚠ WARNING:</span>
                    {!! preg_replace(
                        [
                            "/([A-Z0-9\s]+(?:\d{3,})?)/", // match SKU-like
                            "/Line\s+\d+/",              // match "Line X"
                            "/Preform|Caps|Label|LDPE/", // match categories
                            "/\d+(\.\d+)?%/"             // match percentage
                        ],
                        [
                            "<span class='text-[#23527c] font-bold'>$1</span>",
                            "<span class='text-[#23527c] font-bold'>$0</span>",
                            "<span class='text-[#23527c] font-bold'>$0</span>",
                            "<span class='text-[#23527c] font-bold'>$0</span>",
                        ],
                        e(Str::after($note->message, 'WARNING:'))
                    ) !!}
                @else
                    {!! $note->message !!}
                @endif
            </p>
            <p class="text-xs text-blue-600">{{ $note->created_at->diffForHumans() }}</p>
        </div>

        @if(!$note->is_read)
            <span class="ml-2 w-2 h-2 bg-blue-500 rounded-full"></span>
        @endif
    </button>
</form>
