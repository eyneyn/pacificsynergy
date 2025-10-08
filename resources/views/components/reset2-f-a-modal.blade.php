<div>
    <!-- Do what you can, with what you have, where you are. - Theodore Roosevelt -->
</div>@props(['userId'])

{{-- Reset 2FA Button (opens modal) --}}
<button type="button"
    onclick="document.getElementById('reset-2fa-modal-{{ $userId }}').classList.remove('hidden')"
    class="px-3 py-2 text-sm font-medium text-white bg-[#e74c3c] border border-[#c0392b] hover:bg-[#d64541]">
    Reset 2FA
</button>

<!-- Modal -->
<div id="reset-2fa-modal-{{ $userId }}" tabindex="-1"
     class="hidden fixed inset-0 z-50 flex justify-center items-center w-full p-4 bg-black/50 backdrop-blur-sm">
  <div class="relative w-full max-w-sm max-h-[90vh]">
    <div class="bg-white shadow-lg border border-gray-300 p-6">
      <h2 class="text-lg font-semibold text-[#23527c] mb-4">Reset Two-Factor Authentication</h2>
      <p class="text-sm text-gray-600 mb-6">
        Are you sure you want to reset this user’s 2FA?<br>
        They will receive a new QR code via email.
      </p>
      <div class="flex justify-end gap-3">
        <!-- Cancel -->
        <button type="button"
                onclick="document.getElementById('reset-2fa-modal-{{ $userId }}').classList.add('hidden')"
                class="text-[#23527c] bg-white border border-[#23527c] hover:bg-gray-50 font-medium text-sm px-4 py-2.5">
          Cancel
        </button>
        <!-- Confirm -->
        <form method="POST" action="{{ route('employees.reset2fa', $userId) }}">
            @csrf
            <button type="submit"
                    class="text-white bg-[#e74c3c] hover:bg-[#d64541] font-medium text-sm px-4 py-2.5">
              Yes, Reset 2FA
            </button>
        </form>
      </div>
    </div>
  </div>
</div>
