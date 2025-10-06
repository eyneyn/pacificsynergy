@extends('layouts.app')

@section('title', 'Set Your Password')

@section('content')
<div class="container mx-auto px-4 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8 border border-gray-200">
        
        {{-- Heading --}}
        <h1 class="text-2xl font-bold text-[#23527c] mb-2 text-center">Welcome!</h1>
        <p class="text-sm text-gray-600 mb-6 text-center">
            Please set your password to continue.
        </p>

        {{-- Status / Error messages --}}
        @if (session('status'))
            <p class="text-green-600 text-sm mb-4">{{ session('status') }}</p>
        @endif

        @if ($errors->any())
            <div class="mb-4">
                <ul class="list-disc list-inside text-red-500 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('password.set') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ request('email') }}">

            {{-- New Password --}}
            <div class="mb-4">
                <label for="password" class="block mb-1 text-sm font-medium text-[#23527c]">New Password <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <input id="password" type="password" name="password" required
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:border-[#23527c] focus:ring-1 focus:ring-[#23527c]"
                        placeholder="Enter new password">
                    <button type="button" onclick="togglePassword()" 
                        class="px-3 py-2 text-sm text-white bg-[#323B76] hover:bg-[#444d90] rounded">
                        Show
                    </button>
                </div>
            </div>

            {{-- Confirm Password --}}
            <div class="mb-6">
                <label for="password_confirmation" class="block mb-1 text-sm font-medium text-[#23527c]">Confirm Password <span class="text-red-500">*</span></label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:outline-none focus:border-[#23527c] focus:ring-1 focus:ring-[#23527c]"
                    placeholder="Confirm your password">
            </div>

            {{-- Password Rules --}}
            <div class="mb-6 text-sm text-gray-600">
                <h3 class="font-semibold mb-1">Password requirements:</h3>
                <ul class="list-disc list-inside space-y-1">
                    <li>At least 6 characters long</li>
                    <li>Must include a lowercase letter</li>
                    <li>Must include a number</li>
                    <li>Must include a special character (., !, @, #, ?)</li>
                </ul>
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full py-2 bg-[#5bb75b] border border-[#43a143] text-white text-sm font-medium rounded hover:bg-[#42a542]">
                Save Password
            </button>
        </form>
    </div>
</div>

{{-- JS: Toggle password --}}
<script>
    function togglePassword() {
        const input = document.getElementById("password");
        const btn = event.target;
        if (input.type === "password") {
            input.type = "text";
            btn.textContent = "Hide";
        } else {
            input.type = "password";
            btn.textContent = "Show";
        }
    }
</script>
@endsection
