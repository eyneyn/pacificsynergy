<x-guest-layout>
    <h2 class="text-center text-xl font-semibold mt-2">Enter OTP</h2>

    <form method="POST" action="{{ route('2fa.verify') }}" class="space-y-4">
        @csrf
        <div>
            <input type="text" name="otp" placeholder="Enter 6-digit code"
                   class="w-full border rounded p-3" required autofocus>
        </div>

        <button type="submit"
                class="w-full py-3 bg-[#3c49a3] text-white rounded font-semibold hover:bg-[#2d326b]">
            Verify
        </button>
    </form>
</x-guest-layout>
