<!-- Delete Confirmation Modal Component -->
<div id="delete-confirmation-modal" tabindex="-1"
     class="hidden fixed inset-0 z-50 flex justify-center items-center w-full p-4 bg-black/50 backdrop-blur-sm">
  <div class="relative w-full max-w-sm max-h-[90vh]">
    <div class="bg-white rounded-xl shadow-lg border border-gray-300 p-6">
      <h2 class="text-lg font-semibold text-[#323B76] mb-4">Confirm Deletion</h2>
      <p class="text-sm text-gray-600 mb-6">
        Are you sure you want to delete <span class="font-semibold" id="item-name-to-delete">this item</span>?
      </p>
      <div class="flex justify-end gap-3">
        <button type="button" id="cancel-delete-btn"
                class="text-[#323B76] bg-white border border-[#323B76] hover:bg-gray-50 font-medium rounded-lg text-sm px-4 py-2.5">
          Cancel
        </button>
        <button type="button" id="confirm-delete-btn"
                class="text-white bg-red-600 hover:bg-red-700 font-medium rounded-lg text-sm px-4 py-2.5">
          Yes, Delete
        </button>
      </div>
    </div>
  </div>
</div>
