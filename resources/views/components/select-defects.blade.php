@props(['name', 'options' => [], 'placeholder' => 'Select an option', 'value' => null, 'required' => false])

@php
    $selectedValue = old($name, $value);
    $selectedLabel = $options[$selectedValue] ?? '';
@endphp

<div
    x-data="{
        open: false,
        search: '{{ $selectedLabel }}',
        selected: '{{ $selectedValue }}',
        options: @js($options),
        focusedIndex: -1,
        filteredOptions() {
            if (!this.search) return Object.entries(this.options);
            return Object.entries(this.options).filter(([value, label]) =>
                label.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        selectOption(value, label) {
            this.selected = value;
            this.search = label;
            this.open = false;
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
    <div @click.away="open = false">
        <!-- Input Field -->
        <input
            type="text"
            x-model="search"
            @focus="open = true"
            @mousedown="open = true"
            @keydown.arrow-down.prevent="moveFocus(1)"
            @keydown.arrow-up.prevent="moveFocus(-1)"
            @keydown.enter.prevent="selectFocused()"
            @keydown.escape="open = false"
            placeholder="{{ $placeholder }}"
            class="w-full text-sm border border-gray-300 bg-white px-3 py-[0.375rem] focus:border-[#2d326b] focus:ring focus:ring-[#2d326b] focus:ring-0 transition"
            :class="{ 'text-gray-400': search === '' }"
        >

        <!-- Dropdown Options Above Input -->
        <div
            x-show="open"
            x-transition
            class="absolute z-10 w-full bottom-full mb-1 bg-white border border-gray-300 shadow-lg max-h-60 overflow-auto"
            x-ref="dropdownOptions"
        >
            <template x-for="([value, label], index) in filteredOptions()" :key="value">
                <div
                    @click="selectOption(value, label)"
                    :class="{
                        'bg-[#C7D2FE]': selected === value || focusedIndex === index,
                        'hover:bg-[#C7D2FE]': true
                    }"
                    class="px-4 py-2 text-sm cursor-pointer transition"
                    x-text="label"
                ></div>
            </template>

            <div x-show="filteredOptions().length === 0" class="px-4 py-2 text-sm text-gray-500">
                No results found.
            </div>
        </div>
    </div>

    <!-- Hidden Input for Form -->
    <input type="hidden" name="{{ $name }}" :value="selected" @if($required) required @endif>

    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
