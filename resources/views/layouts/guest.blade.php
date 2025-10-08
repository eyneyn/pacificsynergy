<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>
                @hasSection('title')
                    @yield('title') | {{ optional($settings)->company_name ?? 'Company Name' }}
                @else
                    {{ optional($settings)->company_name ?? 'Company Name' }}
                @endif
        </title>

        {{-- Tab icon (favicon) --}}
        <link rel="icon" 
            href="{{ optional($settings)->logo ? asset('storage/' . $settings->logo) : asset('img/default-logo.png') }}" 
            type="image/png">

        <link rel="shortcut icon" 
            href="{{ optional($settings)->logo ? asset('storage/' . $settings->logo) : asset('img/default-logo.png') }}" 
            type="image/png">

        <link rel="apple-touch-icon" 
            href="{{ optional($settings)->logo ? asset('storage/' . $settings->logo) : asset('img/default-logo.png') }}">


        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans" style="font-family: 'Inter', sans-serif;">
        
    <!-- Dynamic background -->
    <div class="min-h-screen flex items-center justify-center bg-cover bg-center"
        style="background-image: linear-gradient(rgba(9,69,89,0.962), rgba(5,58,86,0.671)),
                url('{{ $settings && $settings->background_image ? asset("storage/" . $settings->background_image) : asset("img/bg-default.png") }}')">            
            
            <div class="w-full max-w-md bg-[#f9fafb] backdrop-blur-md border border-blue shadow-md rounded-xl p-8 text-[#2d326b]">

           <img src="{{ optional($settings)->logo 
                    ? asset('storage/' . $settings->logo) 
                    : asset('img/default-logo.png') }}"
                    alt="{{ optional($settings)->company_name ?? 'Company Logo' }}"
                    class="mx-auto h-20 mb-4 object-contain" />

            <h1 class="text-center text-lg font-light">
                {{ optional($settings)->company_name ?? 'Company Name' }}
            </h1>
            
        {{ $slot }}
    </body>
</html>
