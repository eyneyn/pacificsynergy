@props(['name', 'options' => [], 'placeholder' => 'Select an option', 'value' => null, 'required' => false])

@php
    $selectedValue = old($name, $value);
    $selectedLabel = $options[$selectedValue] ?? $placeholder;
@endphp

<div
    x-data="{
        open: false,
        selected: '{{ $selectedValue }}',
        label: '{{ $selectedLabel }}',
        selectOption(value, label) {
            this.selected = value;
            this.label = label;
            this.open = false;
        }
    }"
    x-ref="{{ $name }}Dropdown"
    class="relative w-full"
>
    <!-- Styled Trigger Button -->
    <button
        type="button"
        @click="open = !open"
        class="w-full text-sm rounded border border-gray-300 bg-white px-3 py-[0.375rem] text-left flex justify-between items-center hover:shadow-lg hover:border-[#2d326b] focus:border-[#2d326b] focus:ring focus:ring-[#2d326b] focus:ring-0 transition"
    >
        <span 
            class="truncate"
            :class="label === '{{ $placeholder }}' ? 'text-gray-400' : 'text-[#2d326b]'" 
            x-text="label">
        </span>
        <svg class="w-4 h-4 ml-2 text-gray-500 transform transition-transform"
             :class="{ 'rotate-180': open }"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown Options -->
    <div 
        x-show="open"
        @click.away="open = false"
        x-transition
        class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
    >
        @foreach ($options as $value => $label)
            <div 
                @click="selectOption('{{ $value }}', '{{ $label }}')"
                class="px-4 py-2 text-sm cursor-pointer transition"
                :class="{ 
                    'bg-[#C7D2FE]': selected === '{{ $value }}', 
                    'hover:bg-[#C7D2FE]': true 
                }"
            >
                {{ $label }}
            </div>
        @endforeach
    </div>

    <!-- Hidden Input with dynamic required -->
    <input type="hidden" name="{{ $name }}" :value="selected" @if($required) required @endif>

    <!-- Validation Error -->
    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
