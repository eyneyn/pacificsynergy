<x-guest-layout>
            <h2 class="text-center text-xl font-semibold mt-2">Login</h2>

            <x-input-error :messages="$errors->get('email')"/>


            {{-- <x-auth-session-status class="mt-4 mb-4 text-sm text-white text-center" :status="session('status')" /> --}}

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div class="relative">
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        placeholder="Email" required autofocus
                        class="w-full rounded-full bg-transparent border border-[#3c49a3] px-5 py-3 pr-12 text-gray-800 placeholder-gray-500 focus:ring-1 focus:ring-[#23527c] focus:outline-none" />
                </div>

                <!-- Password -->
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="Password" required
                        class="w-full rounded-full bg-transparent border border-[#3c49a3] px-5 py-3 pr-12 text-gray-800 placeholder-gray-500 focus:ring-1 focus:ring-[#23527c] focus:outline-none" />
                </div>

                <div class="text-center mt-2">
                    <a href="{{ route('password.request') }}" class="text-ml text-[#242c67] hover:text-[#3c49a3] transition duration-200">
                        Forgot Password?
                    </a>
                </div>

                <!-- Login Button -->
                <button type="submit"
                    class="mt-5 w-1/2 mx-auto block py-3 bg-[#3c49a3] text-white rounded-full font-semibold shadow-md hover:bg-[#2d326b] hover:text-white transition-all transform hover:scale-105 active:scale-95">
                    Login
                </button>
            </form>

</x-guest-layout>
