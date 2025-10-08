<!-- ✅ Alpine Validate Modal -->
<div x-show="showValidate"
     style="display: none;"
     class="fixed inset-0 z-50 flex justify-center items-center w-full p-4 bg-black/50 backdrop-blur-sm">

  <div class="relative w-full max-w-sm max-h-[90vh] bg-white rounded-lg shadow-lg border border-gray-300 p-6">
    
    <h2 class="text-lg font-semibold text-[#23527c] mb-4">Confirm Validation</h2>
    <p class="text-sm text-gray-600 mb-6">
      Are you sure you want to validate this Production Report?
    </p>
    <div class="flex justify-end gap-3">
      <button type="button"
              @click="showValidate = false"
              class="text-[#23527c] bg-white border border-[#23527c] hover:bg-gray-50 font-medium text-sm px-4 py-2.5">
        Cancel
      </button>
      <form method="POST" action="{{ route('report.validate', $report->id) }}">
          @csrf
          @method('PATCH')
          <button type="submit"
                  class="text-white bg-[#323B76] hover:bg-[#444d90] font-medium text-sm px-4 py-2.5">
            Yes, Validate
          </button>
      </form>
    </div>
  </div>
</div>
