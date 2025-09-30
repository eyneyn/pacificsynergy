@props(['name', 'options' => [], 'role'])

@php
    $selectedValue = old($name, $role);
    $selectedLabel = $options[$selectedValue] ?? 'Select Role';
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
    class="relative w-full"
>
    <!-- Trigger Button -->
    <button
        type="button"
        @click="open = !open"
        class="w-full text-sm border border-gray-300 bg-white px-3 py-[0.375rem] text-left flex justify-between items-center hover:shadow-lg hover:border-[#2d326b] focus:border-[#2d326b] focus:ring focus:ring-[#2d326b] focus:ring-0 transition"
    >
        <span 
            class="truncate" 
            :class="label === 'Select Role' ? 'text-gray-400' : 'text-[#2d326b]'" 
            x-text="label">
        </span>
        <svg class="w-4 h-4 ml-2 text-gray-500 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown Options -->
    <div
        x-show="open"
        @click.away="open = false"
        x-transition
        class="absolute z-10 w-full w-full text-sm border border-gray-300 bg-white px-3 py-1 pr-8 focus:border-[#2d326b] focus:ring focus:ring-[#2d326b] focus:ring-0 transition focus:border-blue-500 focus:shadow-lg focus:outline-none max-h-60 overflow-auto"
    >
        @foreach ($options as $value => $label)
            <div
                @click="selectOption('{{ $value }}', '{{ $label }}')"
                class="px-4 py-2 text-sm cursor-pointer transition"
                :class="{ 'bg-[#C7D2FE]': selected === '{{ $value }}', 'hover:bg-[#C7D2FE]': true }"
            >
                {{ $label }}
            </div>
        @endforeach
    </div>

    <!-- Hidden Input -->
    <input type="hidden" name="{{ $name }}" :value="selected" required>

    <!-- Error Message -->
    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
