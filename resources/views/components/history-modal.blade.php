<div 
    x-show="showHistory" 
    style="display: none;"
    class="fixed inset-0 z-50 flex justify-center items-center w-full p-4 bg-black/50 backdrop-blur-sm">
    
    <div class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-6 overflow-y-auto max-h-[80vh]">
        
        <div class="flex justify-between mb-4">
            <h2 class="text-xl font-bold text-[#2d326b]">Change History</h2>
            <button @click="showHistory = false" class="text-gray-500 hover:text-red-600 text-xl">&times;</button>
        </div>

        <div class="space-y-4">
            @php
                $combinedLogs = collect();

                foreach ($report->statuses as $status) {
                    $combinedLogs->push([
                        'type' => 'status',
                        'created_at' => $status->created_at,
                        'user' => $status->user,
                        'status' => $status->status,
                        'remarks' => $status->remarks,
                    ]);
                }

                foreach ($report->histories as $history) {
                    $combinedLogs->push([
                        'type' => 'history',
                        'created_at' => $history->updated_at,
                        'editor' => $history->user,
                        'version' => $history->version ?? null,
                        'summary' => $history->summary,
                        'old_data' => $history->old_data,
                        'new_data' => $history->new_data,
                    ]);
                }

                $combinedLogs = $combinedLogs->sortByDesc('created_at');
            @endphp

            <ol class="relative border-s border-gray-200">
                @forelse ($combinedLogs as $log)
                    <li class="mb-10 ms-4">
                        <div class="absolute w-6 h-6 bg-white border border-gray-300 rounded-full -start-3 flex items-center justify-center z-10">
                            @if ($log['type'] === 'status')
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                            @else
                                @if ($log['version'])
                                    <span class="text-[10px] font-bold text-white bg-gray-400 px-1.5 py-0.5 rounded-full">v{{ $log['version'] }}</span>
                                @else
                                    <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                @endif
                            @endif
                        </div>

                        <div class="bg-gray-50 p-4 rounded-md border border-gray-100 shadow-sm">
                            @if ($log['type'] === 'status')
                                <h3 class="text-sm font-medium text-gray-800 mb-1">
                                    Production report has been successfully <span class="font-semibold text-[#2d326b]">{{ $log['status'] }}</span>
                                </h3>
                                <p class="text-[11px] text-gray-400">
                                    {{ \Carbon\Carbon::parse($log['created_at'])->format('F j, Y · g:i A') }}
                                </p>
                                <p class="text-xs text-gray-500 mb-2">
                                    @if ($log['status'] === 'Submitted')
                                        Submitted by:
                                    @elseif ($log['status'] === 'Validated')
                                        Validated by:
                                    @else
                                        Status changed by:
                                    @endif
                                    {{ $log['user']->first_name ?? '' }} {{ $log['user']->last_name ?? '' }}
                                </p>

                                    {{-- ✅ Show remarks if voided --}}
                                    @if ($log['status'] === 'Voided' && !empty($log['remarks']))
                                        <p class="text-xs text-red-600 mt-1">
                                            <strong>Remarks:</strong> {{ $log['remarks'] }}
                                        </p>
                                    @endif
                            @else
                                <h3 class="text-sm font-medium text-gray-800 mb-1">
                                    {{ $log['summary'] ?? 'Updated record' }}
                                </h3>
                                <p class="text-[11px] text-gray-400">
                                    {{ \Carbon\Carbon::parse($log['created_at'])->format('F j, Y · g:i A') }}
                                </p>
                                <p class="text-xs text-gray-500 mb-2">
                                    Updated by: {{ $log['editor']->first_name ?? '' }} {{ $log['editor']->last_name ?? '' }}
                                </p>

                                @php
                                    $oldData = $log['old_data'];
                                    $newData = $log['new_data'];
                                @endphp

                                @if ($oldData && $newData)
                                    <div class="space-y-2 text-xs">
                                        {{-- Field-level Changes --}}
                                    @foreach ($oldData['fields'] ?? [] as $field => $oldValue)
                                        @php 
                                            $newValue = $newData['fields'][$field] ?? null; 
                                        @endphp

                                        @if ($oldValue !== $newValue)
                                            <div>
                                                @if ($field === 'sku_id' || $field === 'sku')
                                                    <strong class="text-[#2d326b]">SKU:</strong>
                                                    <span class="text-red-500 ml-1">
                                                        {{ \App\Models\Standard::find($oldValue)?->description ?? '-' }}
                                                    </span>
                                                    <span class="mx-1 text-gray-500">→</span>
                                                    <span class="text-green-600">
                                                        {{ \App\Models\Standard::find($newValue)?->description ?? '-' }}
                                                    </span>
                                                @else
                                                    <strong class="text-[#2d326b]">{{ ucwords(str_replace('_', ' ', $field)) }}:</strong>
                                                    <span class="text-red-500 ml-1">{{ $oldValue }}</span>
                                                    <span class="mx-1 text-gray-500">→</span>
                                                    <span class="text-green-600">{{ $newValue }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach

                                        {{-- Issues Comparison --}}
                                        @php
                                            $oldIssues = $oldData['issues'] ?? [];
                                            $newIssues = $newData['issues'] ?? [];
                                        @endphp

                                        @if ($oldIssues !== $newIssues)
                                            <div class="pt-2">
                                                <strong class="text-[#2d326b]">Issues Updated:</strong>
                                                <ul class="ml-4 list-disc text-gray-700">
                                                    @foreach ($oldIssues as $index => $oldIssue)
                                                        @php $newIssue = $newIssues[$index] ?? [] @endphp
                                                        @if ($oldIssue !== $newIssue)
                                                            <li>
                                                                <span class="text-red-500">
                                                                    {{ $oldIssue['maintenance'] ?? '' }} - {{ $oldIssue['remarks'] ?? '' }} ({{ $oldIssue['minutes'] ?? 0 }} mins)
                                                                </span>
                                                                <span class="mx-1 text-gray-500">→</span>
                                                                <span class="text-green-600">
                                                                    {{ $newIssue['maintenance'] ?? '' }} - {{ $newIssue['remarks'] ?? '' }} ({{ $newIssue['minutes'] ?? 0 }} mins)
                                                                </span>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                    @foreach (array_slice($newIssues, count($oldIssues)) as $newIssue)
                                                        <li class="text-green-600">
                                                            Added: {{ $newIssue['maintenance'] ?? '' }} - {{ $newIssue['remarks'] ?? '' }} ({{ $newIssue['minutes'] ?? 0 }} mins)
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

{{-- QC Rejects Comparison --}}
@php
    $oldRejects = collect($oldData['qc_rejects'] ?? []);
    $newRejects = collect($newData['qc_rejects'] ?? []);

    $hasRejectChanges = false;

    foreach ($oldRejects as $index => $oldReject) {
        $newReject = $newRejects->get($index, []);
        if (collect($oldReject)->diffAssoc($newReject)->isNotEmpty() || collect($newReject)->diffAssoc($oldReject)->isNotEmpty()) {
            $hasRejectChanges = true;
            break;
        }
    }

    if ($newRejects->count() > $oldRejects->count()) {
        $hasRejectChanges = true;
    }
@endphp

@if ($hasRejectChanges)
    <div class="pt-2">
        <strong class="text-[#2d326b]">Line QC Rejects Updated:</strong>
        <ul class="ml-4 list-disc text-gray-700">
            @foreach ($oldRejects as $index => $oldReject)
                @php $newReject = $newRejects->get($index, []); @endphp
                @if (collect($oldReject)->diffAssoc($newReject)->isNotEmpty() || collect($newReject)->diffAssoc($oldReject)->isNotEmpty())
                    <li>
                        <span class="text-red-500">
                            {{ $oldReject['category'] ?? '' }} - {{ $oldReject['defect'] ?? '' }} ({{ $oldReject['quantity'] ?? 0 }} pcs)
                        </span>
                        <span class="mx-1 text-gray-500">→</span>
                        <span class="text-green-600">
                            {{ $newReject['category'] ?? '' }} - {{ $newReject['defect'] ?? '' }} ({{ $newReject['quantity'] ?? 0 }} pcs)
                        </span>
                    </li>
                @endif
            @endforeach
            @foreach ($newRejects->slice($oldRejects->count()) as $newReject)
                <li class="text-green-600">
                    Added: {{ $newReject['category'] ?? '' }} - {{ $newReject['defect'] ?? '' }} ({{ $newReject['quantity'] ?? 0 }} pcs)
                </li>
            @endforeach
        </ul>
    </div>
@endif



                                    </div>
                                @else
                                    <p class="text-gray-400 italic text-xs">No field-level changes recorded.</p>
                                @endif
                            @endif
                        </div>
                    </li>
                @empty
                    <p class="text-sm text-gray-500">No change history available.</p>
                @endforelse
            </ol>
        </div>
    </div>
</div>
