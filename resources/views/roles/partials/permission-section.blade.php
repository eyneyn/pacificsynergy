<div class="bg-gray-100 border border-gray-200 rounded-xl p-5 shadow-sm mb-6">
    <div class="flex justify-between items-center mb-4">
        <h4 class="text-sm font-semibold text-[#2d326b]">{{ $title }}</h4>
        <button type="button"
            @click="Object.keys(@js($permissions)).forEach(p => form.permissions[p] = true)"
            class="text-xs text-[#323B76] hover:underline">Enable all</button>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($permissions as $key => $label)
            <div class="flex items-start justify-between border border-gray-200 bg-white rounded-lg p-4">
                <div class="pr-2">
                    <p class="text-sm font-medium text-[#2d326b]">{{ $label }}</p>
                </div>

       <!-- Hidden input for backend -->
        <input type="hidden" :name="'permissions[' + '{{ $key }}' + ']'" :value="form.permissions['{{ $key }}'] ? 1 : 0">

                <!-- Toggle -->
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" x-model="form.permissions['{{ $key }}']"  class="sr-only peer">
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full 
                                peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full 
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white 
                                after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2D3A8C]">
                    </div>
                </label>
            </div>
        @endforeach
    </div>
</div>
