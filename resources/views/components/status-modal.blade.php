<div 
    x-data="{ 
        show: @json(session('success') || session('error')), 
        type: @json(session('success') ? 'success' : (session('error') ? 'error' : null)) 
    }"
    x-show="show"
    x-transition
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
>
    <div class="bg-white rounded-lg shadow-lg w-[340px] p-6 relative text-center">
        <!-- Close Button -->
        <button @click="show = false" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>

        <!-- Success -->
        <template x-if="type === 'success'">
            <div>
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-green-100">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-green-600">Success!</h2>
                <p class="mt-2 text-gray-600">Everything is working normally.</p>
                <button @click="show = false"
                        class="mt-4 px-6 py-2 border border-green-500 text-green-600 rounded hover:bg-green-500 hover:text-white transition">
                    CONTINUE
                </button>
            </div>
        </template>

        <!-- Error -->
        <template x-if="type === 'error'">
            <div>
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-red-100">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-red-600">Error!</h2>
                <p class="mt-2 text-gray-600">Oops! Something went wrong!</p>
                <button @click="show = false"
                        class="mt-4 px-6 py-2 border border-red-500 text-red-600 rounded hover:bg-red-500 hover:text-white transition">
                    TRY AGAIN
                </button>
            </div>
        </template>
    </div>
</div>
