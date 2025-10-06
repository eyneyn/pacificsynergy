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
                       class="w-full rounded-full border border-[#3c49a3] px-5 py-3 text-center text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-[#23527c] focus:outline-none" required>
            </div>
            <button type="submit"
                class="w-full py-3 bg-[#3c49a3] text-white rounded-full font-semibold shadow hover:bg-[#2d326b] hover:scale-105 transform transition">
                Verify
            </button>

</x-guest-layout>
