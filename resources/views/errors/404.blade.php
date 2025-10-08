@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-white px-4">
    <div class="text-center">

        <!-- Illustration -->
        <div class="flex justify-center mb-10">
            <img src="{{ asset('img/no-access.png') }}" 
                 alt="Page Not Found Illustration" 
                 class="h-[250px] object-contain mx-auto">
        </div>

        <!-- Title -->
        <h1 class="text-6xl font-bold text-[#23527c] mb-2">404</h1>
        <h2 class="text-2xl font-semibold text-[#23527c] mb-4">Page Not Found</h2>

        <!-- Message -->
        <p class="text-gray-500 mb-2">
            The page you are looking for might have been removed, 
            had its name changed, or is temporarily unavailable.
        </p>
        <p class="text-gray-500">
            <a href="{{ route('admin.dashboard') }}" 
               class="font-semibold text-[#23527c] hover:underline">
                Go back to Dashboard
            </a>
        </p>
    </div>
</div>
@endsection
