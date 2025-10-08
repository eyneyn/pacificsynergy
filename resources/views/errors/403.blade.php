@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
<div class="flex items-center justify-center bg-white px-4">
    <div class="text-center">

        <!-- Illustration -->
        <div class="flex justify-center mb-10">
            <img src="{{ asset('img/no-access.png') }}" 
                 alt="Access Denied Illustration" 
                 class="h-[250px] object-contain mx-auto">
        </div>

        <!-- Title -->
        <h1 class="text-6xl font-bold text-[#23527c] mb-2">403</h1>
        <h2 class="text-2xl font-semibold text-[#23527c] mb-4">Access Denied</h2>

        <!-- Message -->
        <p class="text-gray-500 mb-2">
            Sorry, but you don’t have permission to access this page.
        </p>
        <p class="text-gray-500">
            You can go back to 
            <a href="{{ url()->previous() }}" class="font-semibold text-[#23527c] hover:underline">
                previous page
            </a>
        </p>
    </div>
</div>
@endsection
