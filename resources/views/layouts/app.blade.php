<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="!bg-white !text-gray-900">
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
    <body class="font-sans" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
            @include('layouts.navigation')

            <!-- Main Content -->
            <div id="main-content" class="transition-all duration-300 p-8 ml-0">
                <div>
                    @yield('content')
                </div>
            </div>
        </div>

        <script src="//unpkg.com/alpinejs" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    </body>
</html>
