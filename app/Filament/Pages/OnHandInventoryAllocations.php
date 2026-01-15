<?php

namespace App\Filament\Pages;

use App\Models\InventoryAllocation;
use Filament\Pages\Page;
use Livewire\WithPagination;

class OnHandInventoryAllocations extends Page
{
    use WithPagination;
    
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    
    protected static ?string $navigationLabel = 'On Hand Inventory Allocations';
    
    protected static ?string $navigationGroup = 'Inventory';
    
    protected static ?int $navigationSort = 6;
    
    protected static string $view = 'filament.pages.on-hand-inventory-allocations';
    
    public ?string $search = '';
    
    public ?string $sortColumn = 'created_at';
    
    public ?string $sortDirection = 'desc';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'sortColumn' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];
    
    protected $paginationTheme = 'tailwind';
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check();
    }

    public function mount(): void
    {
        $this->search = request()->query('search', '');
        $this->sortColumn = request()->query('sortColumn', 'created_at');
        $this->sortDirection = request()->query('sortDirection', 'desc');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function getAllocations(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = InventoryAllocation::query()
            ->with(['garment', 'user']);

        // Apply search filter
        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%")
                  ->orWhere('product_size', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        if ($this->sortColumn === 'user.name') {
            $query->join('users', 'inventory_allocations.user_id', '=', 'users.id')
                  ->orderBy('users.name', $this->sortDirection)
                  ->select('inventory_allocations.*');
        } else {
            $query->orderBy($this->sortColumn, $this->sortDirection);
        }

        return $query->paginate(25);
    }

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }
}
