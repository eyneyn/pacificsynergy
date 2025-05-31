<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans" style="font-family: 'Inter', sans-serif;">
        <div class="min-h-screen flex items-center justify-center bg-cover bg-center"
        style="background-image: linear-gradient(rgba(9, 69, 89, 0.962), rgba(5, 58, 86, 0.671)), url('{{ asset('img/cover.png') }}')">
        <div class="w-full max-w-md bg-[#f9fafb] backdrop-blur-md border border-blue shadow-md rounded-xl p-8 text-[#2d326b]">

            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="mx-auto h-20 mb-4" />
            <h1 class="text-center text-lg font-light">Pacific Synergy Food and Beverage Corp.</h1>
            
        {{ $slot }}
    </body>
</html>
