@props(['name', 'options' => [], 'placeholder' => 'Select an option', 'value' => null, 'selected' => null, 'required' => false])

@php
    $selectedValue = old($name, $selected ?? $value);
    $selectedLabel = $options[$selectedValue] ?? '';
@endphp

<div
    x-data="{
        open: false,
        search: '{{ $selectedLabel }}',
        selected: '{{ $selectedValue }}',
        originalSearch: '{{ $selectedLabel }}',
        options: @js($options),
        focusedIndex: -1,
        isTyping: false,
        filteredOptions() {
            // If not typing (just opened dropdown), show all options
            if (!this.isTyping) {
                return Object.entries(this.options);
            }
            // If typing and search is empty, show all options
            if (!this.search || this.search.trim() === '') {
                return Object.entries(this.options);
            }
            // Filter options based on search
            return Object.entries(this.options).filter(([value, label]) =>
                label.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        selectOption(value, label) {
            this.selected = value;
            this.search = label;
            this.originalSearch = label;
            this.open = false;
            this.isTyping = false;
            this.focusedIndex = -1;
        },
        openDropdown() {
            this.open = true;
            this.isTyping = false;
            this.focusedIndex = -1;
        },
        startTyping() {
            this.isTyping = true;
        },
        resetSearch() {
            this.search = this.originalSearch;
            this.isTyping = false;
        },
        clearAndShowAll() {
            this.search = '';
            this.isTyping = true;
            this.open = true;
        },
        moveFocus(dir) {
            const count = this.filteredOptions().length;
            if (count === 0) return;
            this.focusedIndex = (this.focusedIndex + dir + count) % count;
            this.scrollIntoView();
        },
        scrollIntoView() {
            this.$nextTick(() => {
                const options = this.$refs.dropdownOptions?.children;
                if (options?.[this.focusedIndex]) {
                    options[this.focusedIndex].scrollIntoView({ block: 'nearest' });
                }
            });
        },
        selectFocused() {
            const entry = this.filteredOptions()[this.focusedIndex];
            if (entry) this.selectOption(entry[0], entry[1]);
        }
    }"
    x-ref="{{ $name }}Dropdown"
    class="relative w-full"
>
    <div @click.away="open = false; resetSearch()">
        <!-- Input with dropdown behavior -->
        <div class="relative">
            <input
                type="text"
                x-model="search"
                @focus="openDropdown()"
                @click="openDropdown()"
                @input="startTyping()"
                @keydown.arrow-down.prevent="moveFocus(1)"
                @keydown.arrow-up.prevent="moveFocus(-1)"
                @keydown.enter.prevent="selectFocused()"
                @keydown.escape="open = false; resetSearch()"
                @keydown.delete="clearAndShowAll()"
                @keydown.backspace="startTyping()"
                placeholder="{{ $placeholder }}"
                class="w-full text-sm border border-gray-300 bg-white px-3 py-[0.375rem] pr-8 focus:border-[#2d326b] focus:ring focus:ring-[#2d326b] focus:ring-0 transition focus:border-blue-500 focus:shadow-lg focus:outline-none"
                :class="{ 'text-gray-400': search === '' }"
                autocomplete="off"
            >
            
            <!-- Dropdown arrow and clear button -->
            <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                <!-- Clear button (when has value and typing) -->
                <button 
                    type="button"
                    x-show="search && isTyping"
                    @click="clearAndShowAll()"
                    class="mr-1 p-1 text-gray-400 hover:text-gray-600"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <!-- Dropdown arrow -->
                <div class="pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Dropdown Options -->
        <div
            x-show="open"
            x-transition
            class="absolute z-10 w-full mt-1 bg-white border border-gray-300 shadow-lg max-h-60 overflow-auto"
            x-ref="dropdownOptions"
        >
            <template x-for="([value, label], index) in filteredOptions()" :key="value">
                <div
                    @click="selectOption(value, label)"
                    :class="{
                        'bg-[#C7D2FE] text-[#2d326b]': selected === value,
                        'bg-[#C7D2FE]': focusedIndex === index && selected !== value,
                        'hover:bg-[#C7D2FE]': selected !== value
                    }"
                    class="px-4 py-2 text-sm cursor-pointer transition-colors"
                    x-text="label"
                ></div>
            </template>

            <div x-show="filteredOptions().length === 0" class="px-4 py-2 text-sm text-gray-500">
                No results found. <span class="text-blue-600 cursor-pointer" @click="clearAndShowAll()">Show all options</span>
            </div>
        </div>
    </div>

    <!-- Hidden Input for form submission -->
    <input type="hidden" name="{{ $name }}" :value="selected" @if($required) required @endif>

    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>