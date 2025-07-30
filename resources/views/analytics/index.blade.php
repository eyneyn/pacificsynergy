@extends('layouts.app')
@section('content')

<div class="mx-32">
    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h2 class="text-xl font-bold text-[#3c49a3]">Analytics and Report</h2>
    </div>

    <div class="border-b border-gray-200 mb-6"></div>

    
    <!-- Cards Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        @foreach([
            ['title' => 'Line Efficiency', 'desc' => 'Manage production downtime analysis.', 'route' => route('analytics.line.index')],
            ['title' => 'Material Monitoring', 'desc' => 'Manage material utilization report.', 'route' => route('analytics.material.index')],        ] as $item)
            <a href="{{ $item['route'] }}" class="block">
                <div class="bg-white border border-gray-200 hover:border-[#c9e8fe] hover:bg-[#e2f2ff] shadow-[-1px_6px_5px_rgba(0,0,0.1,0.1)] hover:shadow-lg transition-all duration-200 p-6 cursor-pointer h-40 flex flex-col justify-between">
                    <div class="flex items-start gap-3">
                        {{-- Icon based on title --}}
                        @if($item['title'] === 'Line Efficiency')
                            {{-- Line Icon --}}
                            <svg class="w-6 h-6" fill="#3c49a3" viewBox="0 0 292.074 292.073" xmlns="http://www.w3.org/2000/svg"><path d="M190.166,182.858h-23.509c-4.863,0-8.814,3.945-8.814,8.814c0,4.876,3.951,8.815,8.814,8.815h23.509 c4.864,0,8.821-3.939,8.821-8.815C198.987,186.798,195.03,182.858,190.166,182.858z"></path><path d="M235.028,182.858h-24.235c-4.864,0-8.815,3.945-8.815,8.814c0,4.876,3.951,8.815,8.815,8.815h24.235 c4.863,0,8.803-3.939,8.803-8.815C243.831,186.798,239.891,182.858,235.028,182.858z"></path><path d="M60.415,106.727h77.5c4.871,0,8.815-3.942,8.815-8.812c0-4.875-3.944-8.817-8.815-8.817h-77.5 c-4.87,0-8.818,3.942-8.818,8.817C51.596,102.784,55.544,106.727,60.415,106.727z"></path><path d="M283.247,265.34h-11.518V116.655c0-4.87-3.957-8.818-8.821-8.818h-19.077V17.909c0-4.87-3.951-8.818-8.814-8.818h-24.235 c-4.864,0-8.815,3.948-8.815,8.818v89.928h-7.752V17.909c0-4.87-3.957-8.818-8.821-8.818h-24.229 c-4.863,0-8.809,3.948-8.809,8.818v89.928H29.166c-4.87,0-8.824,3.948-8.824,8.818V265.34H8.818c-4.875,0-8.818,3.945-8.818,8.827 c0,4.87,3.942,8.815,8.818,8.815h20.348h31.249h77.5h125h20.344c4.864,0,8.815-3.945,8.815-8.815 C292.067,269.279,288.123,265.34,283.247,265.34z M69.223,265.34v-64.857h59.862v64.857H69.223z"></path></svg>
                        @elseif($item['title'] === 'Material Monitoring')
                            {{-- Standard Icon --}}
                            <svg class="w-6 h-6" fill="#3c49a3" viewBox="0 0 52 52" xmlns="http://www.w3.org/2000/svg"><path d="M38.77,25.61c1.42,0,2.54,1.54,3.79,2.07s3.19.24,4.13,1.24.71,2.84,1.24,4.14S50,35.42,50,36.84s-1.54,2.54-2.07,3.78-.23,3.19-1.24,4.14-2.83.7-4.13,1.24-2.37,2.06-3.79,2.06S36.23,46.53,35,46s-3.19-.24-4.13-1.24-.71-2.84-1.24-4.14-2.07-2.36-2.07-3.78,1.53-2.54,2.07-3.78.23-3.19,1.24-4.14,2.83-.71,4.13-1.24S37.36,25.61,38.77,25.61ZM26.71,41a4.82,4.82,0,0,1,.38.7c.5,1.22.47,2.83.89,4.08H3.39A1.5,1.5,0,0,1,2,44.15H2V42.56A1.51,1.51,0,0,1,3.39,41H26.71ZM43,33.18a.55.55,0,0,0-.81,0h0l-4.38,5-2-2a.55.55,0,0,0-.81,0h0l-.82.77a.52.52,0,0,0,0,.77h0l2.8,2.8a1.13,1.13,0,0,0,.82.35,1.05,1.05,0,0,0,.82-.35l5.19-5.77a.62.62,0,0,0,0-.77h0ZM6.65,12.3A1.38,1.38,0,0,1,8,13.73H8V36a1.38,1.38,0,0,1-1.32,1.43H3.33A1.39,1.39,0,0,1,2,36H2V13.73A1.39,1.39,0,0,1,3.33,12.3H6.65Zm19,0a1.43,1.43,0,0,1,1.43,1.43h0V32c-.68,1.57-2.63,3-2.63,4.81a2.48,2.48,0,0,0,.06.54H21.35A1.43,1.43,0,0,1,19.92,36h0V13.73a1.43,1.43,0,0,1,1.43-1.43h4.3Zm-9.71,0a1.52,1.52,0,0,1,1.59,1.43h0V36a1.52,1.52,0,0,1-1.59,1.43h-1.6A1.52,1.52,0,0,1,12.75,36h0V13.73a1.52,1.52,0,0,1,1.59-1.43h1.6Zm17.91,0a1.52,1.52,0,0,1,1.6,1.43h0V24.21a7,7,0,0,1-1.5.94,19.63,19.63,0,0,1-3.28.69V13.73a1.52,1.52,0,0,1,1.59-1.43h1.59Zm8.63,0a1.39,1.39,0,0,1,1.33,1.43h0v11.5l-.21-.08c-1.58-.67-3-2.63-4.83-2.63a2.79,2.79,0,0,0-.94.17v-9a1.39,1.39,0,0,1,1.33-1.43h3.32Zm-.07-8.36a1.51,1.51,0,0,1,1.4,1.59h0V7.12a1.51,1.51,0,0,1-1.4,1.59h-39A1.5,1.5,0,0,1,2,7.12H2V5.53A1.5,1.5,0,0,1,3.39,3.94h39Z"></path></svg>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-[#3c49a3] mb-1 truncate">{{ $item['title'] }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed line-clamp-2">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

{{-- <!-- Single Table with All Analytics Options -->
<div class="bg-white rounded-xl shadow border border-gray-200 p-6 shadow-md hover:shadow-lg transition duration-300">
    <div>
        <h3 class="text-lg font-semibold text-[#2d326b]">Production Lines Analytics</h3>
        <p class="text-sm text-gray-600 mb-4">Access  line efficiency and material monitoring analytics for each production line.</p>
    </div>
     --}}
        {{-- <table class="w-full text-sm text-left rtl:text-right border border-[#E5E7EB] border-collapse">
            <thead class="text-xs text-white uppercase bg-[#35408e]">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold tracking-wide border border-[#d9d9d9]">
                        Production Line
                    </th>
                    <th class="px-6 py-3 text-center font-semibold tracking-wide border border-[#d9d9d9]">
                        Line Efficiency
                    </th>
                    <th class="px-6 py-3 text-center font-semibold tracking-wide border border-[#d9d9d9]">
                        Material Monitoring
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse ($activeLines as $index => $line)
                    <tr class="hover:bg-blue-50 transition duration-150 {{ $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' }}">
                        <!-- Production Line Column -->
                        <td class="px-6 py-3 border border-[#d9d9d9]">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 bg-[#2d326b] text-white rounded-full flex items-center justify-center font-bold text-sm">
                                    {{ $line->line_number }}
                                </div>
                                <div>
                                    <div class="font-medium text-[#2d326b]">Line {{ $line->line_number }}</div>
                                    <div class="text-xs text-gray-500 flex items-center gap-1">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        Active
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Line Efficiency Column -->
                        <td class="px-6 py-3 text-center border border-[#d9d9d9]">
                            <a href="{{ route('analytics.line.index', ['line' => $line->line_number]) }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md shadow-sm transition duration-200 transform hover:scale-105">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                                </svg>
                                View Efficiency
                            </a>
                        </td>

                        <!-- Material Monitoring Column -->
                        <td class="px-6 py-3 text-center border border-[#d9d9d9]">
                            <a href="{{ route('analytics.material.index', ['line' => $line->line_number]) }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm transition duration-200 transform hover:scale-105">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                View Materials
                            </a>
                        </td>
                        

                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-1">No Active Production Lines</h4>
                                    <p class="text-gray-500">No production lines are currently available for analytics.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table> --}}

</div>

@endsection