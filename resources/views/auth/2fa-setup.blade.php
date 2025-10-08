<x-guest-layout>

        <!-- Title -->
        <h2 class="text-center text-xl font-semibold mt-2 mb-2">Scan QR Code</h2>
        <p class="text-gray-600 text-center text-sm mb-4">
            Open Google Authenticator, scan this code, then enter the 6-digit OTP below.
        </p>

        <!-- QR Code -->
        <div class="flex justify-center mb-6">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" 
                 alt="QR Code" 
                 class="w-48 h-48 shadow-md rounded-lg border border-gray-200 p-2 bg-white" />
        </div>

        <!-- OTP Form -->
        <form method="POST" action="{{ route('2fa.verify') }}" class="w-full max-w-sm">
            @csrf
            <div class="mb-4">
                <input type="text" name="otp" placeholder="Enter 6-digit OTP"
                       class="w-full rounded-full bg-transparent border border-[#3c49a3] px-5 py-3 pr-12 text-gray-800 placeholder-gray-500 focus:ring-1 focus:ring-[#23527c] focus:outline-none" required>
                
            <x-input-error :messages="$errors->get('otp')" class="mt-2 text-center" />

            </div>
            <button type="submit"
                class="mt-5 w-1/2 mx-auto block py-3 bg-[#3c49a3] text-white rounded-full font-semibold shadow-md hover:bg-[#2d326b] hover:text-white transition-all transform hover:scale-105 active:scale-95">
                Verify
            </button>

</x-guest-layout>
