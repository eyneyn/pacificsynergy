{{-- ===========================
  Tabs navigation (Alpine.js)
  ============================ --}}
<div class="w-full mb-6">
  <div class="flex flex-wrap bg-white border border-gray-200 shadow-sm overflow-hidden">
    <button @click="activeTab = 'overview'"
            :class="activeTab === 'overview' ? 'bg-[#5a9fd4] text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
            class="flex items-center px-3 py-2 text-sm font-medium transition-colors duration-200 border-r border-gray-200 min-w-0 flex-1 md:flex-initial">
      <x-icon-clipboard class="w-4 h-4 mr-2" />
      <span class="truncate">Overview</span>
    </button>

    <button @click="activeTab = 'opl'"
            :class="activeTab === 'opl' ? 'bg-[#5bb75b] text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
            class="flex items-center px-3 py-2 text-sm font-medium transition-colors duration-200 border-r border-gray-200 min-w-0 flex-1 md:flex-initial">
      <x-icon-chart class="w-4 h-4 mr-2" />
      <span class="truncate">OPL Analysis</span>
    </button>

    <button @click="activeTab = 'epl'"
            :class="activeTab === 'epl' ? 'bg-[#b75bb3] text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
            class="flex items-center px-3 py-2 text-sm font-medium transition-colors duration-200 border-r border-gray-200 min-w-0 flex-1 md:flex-initial">
      <x-icon-chart class="w-4 h-4 mr-2" />
      <span class="truncate">EPL Analysis</span>
    </button>

    <button @click="activeTab = 'production'"
            :class="activeTab === 'production' ? 'bg-[#b75b82] text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
            class="flex items-center px-3 py-2 text-sm font-medium transition-colors duration-200 min-w-0 flex-1 md:flex-initial">
      <x-icon-production-table class="w-4 h-4 mr-2" />
      <span class="truncate">Production Report</span>
    </button>
  </div>
</div>
