<x-filament-panels::page>
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">On Hand Inventory Allocations</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Track all inventory allocations and assignments to order numbers.
            </p>
        </div>

        {{-- Search Bar --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by product name, order number, or size..."
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white sm:text-sm"
                    />
                </div>
            </div>
        </div>
        
        @php
            $allocations = $this->getAllocations();
        @endphp

        @if($allocations->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-gray-600 dark:text-gray-400 text-center py-8">
                    @if(!empty($this->search))
                        No allocations found matching your search.
                    @else
                        No inventory allocations found. Assign inventory to orders to see them here.
                    @endif
                </p>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th 
                                    wire:click="sortBy('order_number')"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                >
                                    <div class="flex items-center gap-2">
                                        Order Number
                                        @if($this->sortColumn === 'order_number')
                                            @if($this->sortDirection === 'asc')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @endif
                                    </div>
                                </th>
                                <th 
                                    wire:click="sortBy('product_name')"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                >
                                    <div class="flex items-center gap-2">
                                        Product Name
                                        @if($this->sortColumn === 'product_name')
                                            @if($this->sortDirection === 'asc')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @endif
                                    </div>
                                </th>
                                <th 
                                    wire:click="sortBy('product_size')"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                >
                                    <div class="flex items-center gap-2">
                                        Size
                                        @if($this->sortColumn === 'product_size')
                                            @if($this->sortDirection === 'asc')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @endif
                                    </div>
                                </th>
                                <th 
                                    wire:click="sortBy('quantity')"
                                    class="px-6 py-3 text-right text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                >
                                    <div class="flex items-center justify-end gap-2">
                                        Quantity
                                        @if($this->sortColumn === 'quantity')
                                            @if($this->sortDirection === 'asc')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @endif
                                    </div>
                                </th>
                                <th 
                                    wire:click="sortBy('user.name')"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                >
                                    <div class="flex items-center gap-2">
                                        Assigned By
                                        @if($this->sortColumn === 'user.name')
                                            @if($this->sortDirection === 'asc')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @endif
                                    </div>
                                </th>
                                <th 
                                    wire:click="sortBy('created_at')"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:text-white uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                >
                                    <div class="flex items-center gap-2">
                                        Date Assigned
                                        @if($this->sortColumn === 'created_at')
                                            @if($this->sortDirection === 'asc')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            @endif
                                        @endif
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($allocations as $allocation)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $allocation->order_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $allocation->product_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $allocation->product_size ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white text-right">
                                        {{ number_format($allocation->quantity) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $allocation->user->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $allocation->created_at->format('M d, Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($allocations->hasPages())
                    <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                @if($allocations->onFirstPage())
                                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 opacity-50 cursor-not-allowed">
                                        Previous
                                    </span>
                                @else
                                    <button wire:click="previousPage" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-50">
                                        Previous
                                    </button>
                                @endif
                                
                                @if($allocations->hasMorePages())
                                    <button wire:click="nextPage" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 hover:bg-gray-50">
                                        Next
                                    </button>
                                @else
                                    <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 opacity-50 cursor-not-allowed">
                                        Next
                                    </span>
                                @endif
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Showing <span class="font-medium">{{ $allocations->firstItem() }}</span> to <span class="font-medium">{{ $allocations->lastItem() }}</span> of <span class="font-medium">{{ $allocations->total() }}</span> results
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-filament-panels::page>

