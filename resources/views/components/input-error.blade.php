@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-base text-[#FF2C2C] space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
