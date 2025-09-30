<div class="border border-[#E5E7EB] bg-[#e2f2ff] shadow-sm p-5 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h4 class="text-sm font-semibold text-[#23527c]">{{ $title }}</h4>
        <div class="space-x-4">
            <button type="button"
                @click="Object.keys(@js($permissions)).forEach(p => form.permissions[p] = true)"
                class="text-xs text-[#323B76] hover:underline">Enable all</button>
            <button type="button"
                @click="Object.keys(@js($permissions)).forEach(p => form.permissions[p] = false)"
                class="text-xs text-gray-400 hover:underline">Disable all</button>
        </div>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($permissions as $key => $item)
            @php
                // Normalize: support "key => 'Label'" OR "key => ['label' => 'Label', 'desc' => '...']"
                $permLabel = is_array($item) ? ($item['label'] ?? $key) : $item;
                $permDesc  = is_array($item) ? ($item['desc']  ?? '')   : '';
            @endphp

            <div class="flex items-start justify-between border border-gray-300 bg-white p-4 h-full">
                <div class="pr-2">
                    <p class="text-sm font-medium text-[#23527c]">{{ $permLabel }}</p>
                    @if($permDesc !== '')
                        <p class="text-xs text-gray-500 mt-1">{{ $permDesc }}</p>
                    @endif
                </div>

                <!-- Hidden input for backend -->
                <input type="hidden"
                       :name="'permissions[' + '{{ $key }}' + ']'"
                       :value="form.permissions['{{ $key }}'] ? 1 : 0">

                <!-- Toggle -->
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox"
                           x-model="form.permissions['{{ $key }}']"
                           class="sr-only peer">
                    <div class="relative w-11 h-6 bg-gray-200 rounded-full
                                peer-focus:outline-none
                                peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white
                                after:rounded-full after:h-5 after:w-5 after:transition-all
                                peer-checked:bg-[#323B76]">
                    </div>
                </label>
            </div>
        @endforeach
    </div>
</div>
