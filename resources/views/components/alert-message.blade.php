@if(session('success') || session('error'))
    <div 
        x-data="{ open: true }"
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
        <div class="relative w-full max-w-sm mx-4">
            <div class="bg-white rounded-lg shadow-2xl p-8 relative">
                <!-- Close Button -->
                <button @click="open = false" 
                        class="absolute top-4 left-4 text-gray-400 hover:text-gray-600 text-2xl font-light leading-none focus:outline-none transition-colors"
                        aria-label="Close">&times;</button>

                <!-- Success Modal -->
                @if(session('success'))
                    <div class="flex flex-col items-center text-center">
                        <!-- Success Icon Circle -->
                        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-6">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12ZM16.0303 8.96967C16.3232 9.26256 16.3232 9.73744 16.0303 10.0303L11.0303 15.0303C10.7374 15.3232 10.2626 15.3232 9.96967 15.0303L7.96967 13.0303C7.67678 12.7374 7.67678 12.2626 7.96967 11.9697C8.26256 11.6768 8.73744 11.6768 9.03033 11.9697L10.5 13.4393L12.7348 11.2045L14.9697 8.96967C15.2626 8.67678 15.7374 8.67678 16.0303 8.96967Z" fill="#42a542"></path> </g></svg>
                        </div>
                        
                        <h2 class="text-xl font-bold text-[#2d326b]">Success!</h2>
                        <p class="text-sm text-gray-500 mb-8">{{ session('success') }}</p>
                        
                        <button @click="open = false"
                                class="w-1/2 mx-auto max-w-xs text-[#42a542] bg-white border border-[#42a542] 
                                    hover:bg-[#42a542] hover:text-white 
                                    font-medium text-sm px-6 py-2.5 rounded-full transition-colors">
                            CONTINUE
                        </button>
                    </div>
                @endif

                <!-- Error Modal -->
                @if(session('error'))
                    <div class="flex flex-col items-center text-center">
                        <!-- Error Icon Circle -->
                        <div class="w-20 h-20 bg-red-500 rounded-full flex items-center justify-center mb-6">
                            <svg viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>cross-circle</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage"> <g id="Icon-Set-Filled" sketch:type="MSLayerGroup" transform="translate(-570.000000, -1089.000000)" fill="#ff2e26"> <path d="M591.657,1109.24 C592.048,1109.63 592.048,1110.27 591.657,1110.66 C591.267,1111.05 590.633,1111.05 590.242,1110.66 L586.006,1106.42 L581.74,1110.69 C581.346,1111.08 580.708,1111.08 580.314,1110.69 C579.921,1110.29 579.921,1109.65 580.314,1109.26 L584.58,1104.99 L580.344,1100.76 C579.953,1100.37 579.953,1099.73 580.344,1099.34 C580.733,1098.95 581.367,1098.95 581.758,1099.34 L585.994,1103.58 L590.292,1099.28 C590.686,1098.89 591.323,1098.89 591.717,1099.28 C592.11,1099.68 592.11,1100.31 591.717,1100.71 L587.42,1105.01 L591.657,1109.24 L591.657,1109.24 Z M586,1089 C577.163,1089 570,1096.16 570,1105 C570,1113.84 577.163,1121 586,1121 C594.837,1121 602,1113.84 602,1105 C602,1096.16 594.837,1089 586,1089 L586,1089 Z" id="cross-circle" sketch:type="MSShapeGroup"> </path> </g> </g> </g></svg>
                        </div>
                        
                        <h3 class="text-xl font-bold text-[#2d326b]">Error!</h3>
                        <p class="text-sm text-gray-500 mb-8">{{ session('error') }}</p>
                        
                        <button @click="open = false"
                                class="w-1/2 mx-auto max-w-xs text-[#ff2e26] bg-white border border-red-500 
                                    hover:bg-red-500 hover:text-white 
                                    font-medium text-sm px-6 py-2.5 rounded-full transition-colors">
                            TRY AGAIN
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif