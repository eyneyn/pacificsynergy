{{-- Tabs bar --}}
<div class="inline-flex border border-gray-200 shadow-sm overflow-hidden">
    {{-- Overview --}}
    <button @click="activeTab = 'overview'"
            :class="activeTab === 'overview' 
                ? 'bg-[#5a9fd4] text-white border-r-2 border-[#5a9fd4]' 
                : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
            class="flex items-center px-3 py-2 text-sm font-medium transition-colors duration-200 border-r border-gray-200">
        <x-icon-clipboard class="w-4 h-4 mr-2" />
        <span class="truncate">Overview</span>
    </button>


    {{-- Production --}}
    <button @click="activeTab = 'production'"
            :class="activeTab === 'production' 
                ? 'bg-[#5bb75b] text-white border-[#5bb75b]' 
                : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
            class="flex items-center px-3 py-2 text-sm font-medium transition-colors duration-200">
        <x-icon-production-table class="w-4 h-4 mr-2" />
        <span class="truncate">Production Report</span>
    </button>
</div>
