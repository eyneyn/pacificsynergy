{{-- Alert Message Component --}}

{{-- Show error message for line_delete --}}
    @if ($errors->has('line_delete'))
        <div class="bg-red-100 border border-red-400 text-red-700 p-2 relative mb-4 text-sm rounded" role="alert">
            <strong class="font-bold">Delete Failed:</strong>
            <span class="block sm:inline">{{ $errors->first('line_delete') }}</span>
        </div>
    @endif

{{-- Show success message --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 p-2 relative mb-4 text-sm rounded" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif