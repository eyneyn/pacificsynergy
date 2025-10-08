<x-guest-layout>
    <h2 class="text-center text-xl font-semibold mt-2">Enter OTP</h2>

    <form method="POST" action="{{ route('2fa.verify') }}" class="space-y-4">
        @csrf
        <div>
            <input type="text" name="otp" placeholder="Enter 6-digit code"
                   class="w-full rounded-full bg-transparent border border-[#3c49a3] px-5 py-3 pr-12 text-gray-800 placeholder-gray-500 focus:ring-1 focus:ring-[#23527c] focus:outline-none"
                   required autofocus>

            {{-- 🔴 Display error message --}}
            <x-input-error :messages="$errors->get('otp')" class="mt-2 text-center" />
        </div>

        <button type="submit"
                class="mt-5 w-1/2 mx-auto block py-3 bg-[#3c49a3] text-white rounded-full font-semibold shadow-md hover:bg-[#2d326b] hover:text-white transition-all transform hover:scale-105 active:scale-95">
            Verify
        </button>
    </form>
</x-guest-layout>
