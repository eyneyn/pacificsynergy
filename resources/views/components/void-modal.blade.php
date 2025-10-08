<div 
    x-show="showVoid" 
    style="display: none;"
    class="fixed inset-0 z-50 flex justify-center items-center w-full p-4 bg-black/50 backdrop-blur-sm">
    
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">

        <h2 class="text-lg font-bold text-[#2d326b] mb-4">
            Void Production Report
        </h2>

        <p class="text-sm text-gray-600 mb-4">
            Are you sure you want to void this report? Once voided, it cannot be restored.  
            Please provide a remarks below.
        </p>

        <form method="POST" action="{{ route('report.void', $report->id) }}">
            @csrf
            <div class="mb-4">
                <label for="remarks" class="block text-sm font-medium text-gray-700">
                    Remarks <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="remarks" 
                    name="remarks" 
                    required
                    rows="3"
                    class="mt-1 block w-full rounded-md border border-gray-300 focus:border-blue-500 focus:shadow-lg focus:outline-none sm:text-sm"
                ></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button 
                    type="button"
                    @click="showVoid = false"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm"
                >
                    Confirm Void
                </button>
            </div>
        </form>
    </div>
</div>
