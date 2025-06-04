@extends('layouts.app')

@section('content')
{{-- Production Configuration Page --}}

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <h2 class="text-2xl font-bold text-[#2d326b]">Production Configuration</h2>
</div>

<!-- Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    @foreach([
        ['title' => 'Line', 'desc' => 'Maintain product formulas and references.', 'route' => route('configuration.line.index')],
        ['title' => 'Defect', 'desc' => 'Manage defect types and severity configurations.', 'route' => route('configuration.defect.index')],
        ['title' => 'Maintenance', 'desc' => 'Set department codes and responsibilities.', 'route' => route('configuration.maintenance.index')],
        ['title' => 'Standard', 'desc' => 'Maintain product formulas and references.', 'route' => route('configuration.standard.index')],
    ] as $item)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 p-6">
            <h3 class="text-lg font-semibold text-[#323B76] mb-2">{{ $item['title'] }}</h3>
            <p class="text-sm text-gray-600 mb-4">{{ $item['desc'] }}</p>
            <div class="flex justify-between items-center">
                <a href="{{ $item['route'] }}"
                   class="text-sm text-white bg-[#323B76] hover:bg-[#2d326b] px-4 py-2 rounded-md font-medium transition-all">
                    Manage
                </a>
            </div>
        </div>
    @endforeach
</div>

<x-delete-modal />

@endsection
