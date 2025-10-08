<x-guest-layout>
    <h2 class="text-center text-xl font-semibold mt-2">Password Setup</h2>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" 
                :value="old('email', $request->email)" required autofocus autocomplete="username" />
            @error('email')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <div id="password-strength" class="text-xs mt-1"></div>
            @error('password')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation" required autocomplete="new-password" />
            @error('password_confirmation')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password requirements -->
        <div class="mt-4">
            <h2 class="mb-2 text-sm font-semibold text-blue-950">Password requirements:</h2>
            <ul class="space-y-1 text-gray-500 list-disc list-inside text-sm">
                <li>Must be between 6 and 20 characters long</li>
                <li>Must include at least one lowercase letter</li>
                <li>Must include at least one number</li>
                <li>Must include at least one special character (e.g., ., !, @, #, ?)</li>
            </ul>
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>

    {{-- Validation Scripts --}}
    <script>
        // Password validation
        const newPass = document.getElementById("password");
        const confirmPass = document.getElementById("password_confirmation");
        const strengthDisplay = document.getElementById("password-strength");

        if (newPass) {
            newPass.setAttribute("pattern", "(?=.*[a-z])(?=.*[0-9])(?=.*[.,@$!%*?&]).{6,20}");
            newPass.oninvalid = function(e) {
                e.target.setCustomValidity("Password must be 6–20 characters and contain at least one lowercase letter, one number, and one special character.");
            };
            newPass.oninput = function(e) {
                e.target.setCustomValidity("");

                // Strength Meter
                const value = newPass.value;
                let strength = 0;
                if (value.length >= 6) strength++;
                if (/[a-z]/.test(value)) strength++;
                if (/[0-9]/.test(value)) strength++;
                if (/[.,@$!%*?&]/.test(value)) strength++;
                if (value.length >= 12) strength++;

                let message = "";
                let color = "";
                switch (strength) {
                    case 0:
                    case 1:
                        message = "Weak";
                        color = "text-red-600";
                        break;
                    case 2:
                    case 3:
                        message = "Medium";
                        color = "text-yellow-600";
                        break;
                    case 4:
                    case 5:
                        message = "Strong";
                        color = "text-green-600";
                        break;
                }
                strengthDisplay.textContent = `Strength: ${message}`;
                strengthDisplay.className = `text-xs mt-1 font-semibold ${color}`;
            };
        }

        // Confirm Password Validation
        if (confirmPass && newPass) {
            confirmPass.oninput = function(e) {
                if (confirmPass.value !== newPass.value) {
                    confirmPass.setCustomValidity("Passwords do not match.");
                } else {
                    confirmPass.setCustomValidity("");
                }
            };
            confirmPass.oninvalid = function(e) {
                if (!confirmPass.value) {
                    confirmPass.setCustomValidity("Please confirm your new password.");
                } else {
                    confirmPass.setCustomValidity("Passwords do not match.");
                }
            };
        }
    </script>
</x-guest-layout>
