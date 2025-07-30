    {{-- Report Table --}}
    <table class="w-full mt-4 text-sm text-left rtl:text-right border border-[#E5E7EB] border-collapse">
        <thead class="text-xs text-white uppercase bg-[#35408e]">
            <tr>
                {{-- Sortable Table Headers --}}
                <th class="px-6 py-2 border border-[#d9d9d9]">
                    <a href="{{ route('report.index', ['sort' => 'production_date', 'direction' => ($currentSort === 'production_date' ? $currentDirection : 'asc')]) }}"
                        class="flex items-center gap-1 text-white no-underline">
                        Production Date
                        {{-- Sort Icon --}}
                        <svg class="w-4 h-4 {{ $currentSort === 'production_date' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                            <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                        </svg>
                    </a>
                </th>
                <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                    <a href="{{ route('report.index', ['sort' => 'sku', 'direction' => ($currentSort === 'sku' ? $currentDirection : 'asc')]) }}"
                        class="flex justify-center items-center gap-1 text-white no-underline">
                        SKU
                        <svg class="w-4 h-4 {{ $currentSort === 'sku' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                            <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                        </svg>
                    </a>
                </th>
                <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                    <a href="{{ route('report.index', ['sort' => 'line', 'direction' => ($currentSort === 'line' ? $currentDirection : 'asc')]) }}"
                        class="flex justify-center items-center gap-1 text-white no-underline">
                        Line
                        <svg class="w-4 h-4 {{ $currentSort === 'line' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                            <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                        </svg>
                    </a>
                </th>
                <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                    <a href="{{ route('report.index', ['sort' => 'total_outputCase', 'direction' => ($currentSort === 'total_outputCase' ? $currentDirection : 'asc')]) }}"
                        class="flex justify-center items-center gap-1 text-white no-underline">
                        Total Output Case
                        <svg class="w-4 h-4 {{ $currentSort === 'total_outputCase' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                            <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                        </svg>
                    </a>
                </th>
                <th class="px-6 py-2 border border-[#d9d9d9] text-center">
                    <a href="{{ route('report.index', ['sort' => 'created_at', 'direction' => ($currentSort === 'created_at' ? $currentDirection : 'asc')]) }}"
                        class="flex justify-center items-center gap-1 text-white no-underline">
                        Submitted Date and Time
                        <svg class="w-4 h-4 {{ $currentSort === 'created_at' ? 'opacity-100' : 'opacity-50' }}" fill="currentColor" viewBox="0 0 512 512">
                            <path d="M304 96h48v320h48l-72 80-72-80h48V96zM64 192h128v32H64v-32zm32 64h96v32H96v-32zm32 64h64v32h-64v-32zm32 64h32v32h-32v-32z"/>
                        </svg>
                    </a>
                </th>
                <th class="px-6 py-2 border border-[#d9d9d9] text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            {{-- Table Rows --}}
            @forelse ($reports as $report)
                <tr onclick="window.location='{{ route('report.view', $report->id) }}'" class="bg-white hover:bg-gray-50 cursor-pointer">
                    <td class="px-6 py-2 border border-[#d9d9d9] text-[#2d326b]">{{ $report->production_date ? \Carbon\Carbon::parse($report->production_date)->format('F d, Y') : '-' }}</td>
                    <td class="px-6 py-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $report->sku }}</td>
                    <td class="px-6 py-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $report->line }}</td>
                    <td class="px-6 py-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $report->total_outputCase }}</td>
                    <td class="px-6 py-2 border border-[#d9d9d9] text-gray-600 text-center">{{ $report->created_at ? $report->created_at->format('F j, Y \a\t h:i A') : '-' }}</td>
                    <td class="px-6 py-2 border border-[#d9d9d9] text-gray-600 text-center">
                        @php
                            // Get latest status if exists
                            $status = $report->statuses->first()?->status;
                        @endphp
                        @if ($status)
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium
                                @if($status === 'Submitted') bg-yellow-100 text-yellow-800
                                @elseif($status === 'Reviewed') bg-blue-100 text-blue-800
                                @elseif($status === 'Validated') bg-green-100 text-green-800
                                @endif">
                                {{ $status }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs italic">N/A</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-2 border border-[#E5E7EB] text-center text-[#35408e]">No report entries found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $reports->links('pagination::tailwind') }}
    </div>