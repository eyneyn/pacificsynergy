<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="!bg-white !text-gray-900">
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

    <link href="https://unpkg.com/cropperjs/dist/cropper.min.css" rel="stylesheet"/>


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

    <!-- External Scripts -->
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://unpkg.com/cropperjs/dist/cropper.min.js"></script>

    {{-- <!-- Anti-Inspect Script -->
    <script>
    // Disable right-click
    document.addEventListener("contextmenu", function(e) {
        e.preventDefault();
    });

    // Disable common shortcuts (F12, Ctrl+Shift+I/J/C, Ctrl+U)
    document.addEventListener("keydown", function(e) {
        if (e.keyCode === 123) e.preventDefault(); // F12
        if (e.ctrlKey && e.shiftKey && [73, 74, 67].includes(e.keyCode)) e.preventDefault(); // Ctrl+Shift+I/J/C
        if (e.ctrlKey && e.keyCode === 85) e.preventDefault(); // Ctrl+U
    });

    // Detect DevTools open (by checking window size changes)
    (function() {
        const threshold = 160;
        setInterval(function() {
            if (window.outerHeight - window.innerHeight > threshold || 
                window.outerWidth - window.innerWidth > threshold) {
                
                window.location.href = "{{ route('login') }}";
            }
        }, 1000);
    })();
    </script> --}}
    <script>
        // 🔔 Load notifications dropdown
        function loadDropdown(filter) {
            safeFetch(`{{ route('notifications.dropdown') }}?filter=${filter}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res?.text())
            .then(html => {
                if (html) {
                    document.getElementById('dropdownNotification').innerHTML = html;
                }
            })
            .catch(err => console.error(err));
        }

        // ✅ Global fetch wrapper to catch session expiration (419 Page Expired)
        async function safeFetch(url, options = {}) {
            try {
                const res = await fetch(url, options);

                if (res.status === 419) { 
                    console.warn("⚠️ Session expired, redirecting to login...");
                    window.location.href = "{{ route('login') }}";
                    return null;
                }
                return res;
            } catch (err) {
                console.error("❌ Fetch failed:", err);
                return null;
            }
        }

        // 👇 Auto-ping server to keep session alive
        (function () {
            const sessionLifetime = {{ config('session.lifetime') ?? 120 }}; // minutes
            const refreshInterval = (sessionLifetime - 5) * 60 * 1000; // 5 min before expiry

            function refreshSession() {
                safeFetch("{{ url('/heartbeat') }}", {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(() => {
                    console.log('🔄 Session refreshed');
                });
            }

            setInterval(refreshSession, refreshInterval);
        })();
    </script>
</body>
</html>
