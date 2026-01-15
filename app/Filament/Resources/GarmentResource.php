<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GarmentResource\Pages;
use App\Filament\Resources\GarmentResource\RelationManagers;
use App\Models\Garment;
use App\Models\InventoryAllocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GarmentResource extends Resource
{
    protected static ?string $model = Garment::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    
    protected static ?string $navigationLabel = 'On Hand Products';
    
    protected static ?string $navigationGroup = 'Inventory';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $modelLabel = 'On Hand Inventory';
    
    protected static ?string $pluralModelLabel = 'On Hand Inventory';
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasPermission('garments.view');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('On Hand Inventory Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Product Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., T-Shirt, Hoodie, Polo'),
                        Forms\Components\TextInput::make('size')
                            ->label('Product Size')
                            ->maxLength(50)
                            ->placeholder('e.g., Small, Medium, Large, XL'),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),
                        Forms\Components\TextInput::make('code')
                            ->label('Product Code')
                            ->maxLength(50)
                            ->placeholder('e.g., TS-001, HD-001')
                            ->unique(ignoreRecord: true)
                            ->helperText('Short code or abbreviation for this product'),
                        Forms\Components\TextInput::make('supplier_url')
                            ->label('Supplier URL')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://example.com/product')
                            ->helperText('Link to the supplier\'s product page')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('shelf_location')
                            ->label('Shelf Location')
                            ->maxLength(255)
                            ->placeholder('e.g., Warehouse A - Shelf 1')
                            ->helperText('Enter the shelf location in the warehouse for this product')
                            ->columnSpanFull(),
                        Forms\Components\Hidden::make('variants')
                            ->default([]),
                        Forms\Components\Hidden::make('measurements')
                            ->default([]),
                        Forms\Components\Hidden::make('cubic_dimensions')
                            ->default([]),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable()
                    ->url(fn (Garment $record): string => GarmentResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false)
                    ->color('primary'),
                Tables\Columns\TextColumn::make('size')
                    ->label('Size')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shelf_location')
                    ->label('Shelf')
                    ->searchable()
                    ->sortable()
                    ->default('Not Assigned'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Products'),
            ])
            ->defaultSort('name', 'asc')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('assign_to_order')
                        ->label('Assign to Order')
                        ->icon('heroicon-o-tag')
                        ->form(function ($records) {
                            return [
                                Forms\Components\TextInput::make('order_number')
                                    ->label('Order Number')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter order number')
                                    ->helperText('Enter the order number to assign selected products to'),
                                Forms\Components\Repeater::make('products')
                                    ->label('Products & Quantities')
                                    ->schema([
                                        Forms\Components\Hidden::make('product_id')
                                            ->required()
                                            ->dehydrated(),
                                        Forms\Components\Group::make([
                                            Forms\Components\TextInput::make('product_name')
                                                ->label('Product')
                                                ->disabled()
                                                ->dehydrated(false),
                                            Forms\Components\TextInput::make('product_size')
                                                ->label('Size')
                                                ->disabled()
                                                ->dehydrated(false),
                                        ])->columns(2),
                                        Forms\Components\Group::make([
                                            Forms\Components\TextInput::make('available_quantity')
                                                ->label('Available Quantity')
                                                ->disabled()
                                                ->numeric()
                                                ->dehydrated(),
                                            Forms\Components\TextInput::make('assign_quantity')
                                                ->label('Quantity to Assign')
                                                ->required()
                                                ->numeric()
                                                ->minValue(1)
                                                ->default(1)
                                                ->live()
                                                ->rules([
                                                    function ($get) {
                                                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                            $assignQty = (int)$value;
                                                            
                                                            if ($assignQty <= 0) {
                                                                $fail("Quantity must be at least 1.");
                                                                return;
                                                            }
                                                            
                                                            // Access sibling field in repeater - they're in the same item
                                                            $available = (int)($get('available_quantity') ?? 0);
                                                            
                                                            if ($available <= 0) {
                                                                $fail("Available quantity is invalid.");
                                                                return;
                                                            }
                                                            
                                                            if ($assignQty > $available) {
                                                                $fail("Quantity cannot exceed available quantity ({$available}).");
                                                                return;
                                                            }
                                                        };
                                                    },
                                                ]),
                                        ])->columns(2),
                                    ])
                                    ->defaultItems($records->count())
                                    ->itemLabel(fn (array $state): ?string => $state['product_name'] ?? null)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false),
                            ];
                        })
                        ->mountUsing(function ($records, $form) {
                            $productsData = $records->map(function ($record) {
                                return [
                                    'product_id' => $record->id,
                                    'product_name' => $record->name,
                                    'product_size' => $record->size ?? '—',
                                    'available_quantity' => $record->quantity ?? 0,
                                    'assign_quantity' => 1,
                                ];
                            })->toArray();
                            
                            $form->fill([
                                'products' => $productsData,
                            ]);
                        })
                        ->action(function ($records, array $data) {
                            $orderNumber = trim($data['order_number'] ?? '');
                            
                            if (empty($orderNumber)) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Order Number Required')
                                    ->body('Please enter an order number.')
                                    ->warning()
                                    ->send();
                                return;
                            }
                            
                            $products = $data['products'] ?? [];
                            
                            if (empty($products) || !is_array($products)) {
                                \Filament\Notifications\Notification::make()
                                    ->title('No Products Selected')
                                    ->body('Please select products to assign.')
                                    ->warning()
                                    ->send();
                                return;
                            }
                            
                            $updated = 0;
                            $errors = [];
                            
                            \DB::beginTransaction();
                            
                            try {
                                foreach ($products as $index => $productData) {
                                    // Ensure we have an array
                                    if (!is_array($productData)) {
                                        $errors[] = "Invalid product data format at index {$index}";
                                        continue;
                                    }
                                    
                                    $productId = $productData['product_id'] ?? null;
                                    $assignQuantity = (int)($productData['assign_quantity'] ?? 0);
                                    $productName = $productData['product_name'] ?? 'Unknown Product';
                                    
                                    // Convert product_id to integer if it's a string
                                    if (is_string($productId)) {
                                        $productId = (int)$productId;
                                    }
                                    
                                    if (empty($productId) || $productId <= 0) {
                                        $errors[] = "Invalid product ID for '{$productName}': Product ID is missing or invalid.";
                                        continue;
                                    }
                                    
                                    if ($assignQuantity <= 0) {
                                        $errors[] = "Invalid quantity for '{$productName}': Quantity must be greater than 0.";
                                        continue;
                                    }
                                    
                                    $garment = Garment::lockForUpdate()->find($productId);
                                    
                                    if (!$garment) {
                                        $errors[] = "Product not found: ID {$productId}";
                                        continue;
                                    }
                                    
                                    // Check available quantity
                                    if ($assignQuantity > $garment->quantity) {
                                        $errors[] = "Cannot assign {$assignQuantity} from {$garment->name}. Only {$garment->quantity} available.";
                                        continue;
                                    }
                                    
                                    // Reduce the quantity
                                    $garment->quantity -= $assignQuantity;
                                    $garment->save();
                                    
                                    // Log assignment to order number
                                    InventoryAllocation::create([
                                        'garment_id' => $garment->id,
                                        'product_name' => $garment->name,
                                        'product_size' => $garment->size,
                                        'quantity' => $assignQuantity,
                                        'order_number' => $orderNumber,
                                        'user_id' => auth()->id(),
                                    ]);
                                    
                                    $updated++;
                                }
                                
                                \DB::commit();
                                
                                if ($updated > 0) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Products Assigned')
                                        ->body("Successfully assigned {$updated} product(s) to order {$orderNumber}. Inventory quantities have been updated.")
                                        ->success()
                                        ->send();
                                }
                                
                                if (!empty($errors)) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Some Errors Occurred')
                                        ->body(implode("\n", $errors))
                                        ->warning()
                                        ->send();
                                }
                            } catch (\Exception $e) {
                                \DB::rollBack();
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Error')
                                    ->body('An error occurred while assigning products: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                                
                                \Log::error('Error assigning inventory', [
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                    'data' => $data,
                                ]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('On Hand Inventory Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Product Name'),
                        Infolists\Components\TextEntry::make('size')
                            ->label('Product Size'),
                        Infolists\Components\TextEntry::make('quantity')
                            ->label('Quantity')
                            ->numeric(),
                        Infolists\Components\TextEntry::make('code')
                            ->label('Product Code'),
                        Infolists\Components\TextEntry::make('supplier_url')
                            ->label('Supplier URL')
                            ->url(fn ($record) => $record->supplier_url)
                            ->openUrlInNewTab()
                            ->copyable(),
                        Infolists\Components\TextEntry::make('shelf_location')
                            ->label('Shelf Location')
                            ->default('Not Assigned'),
                    ])->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGarments::route('/'),
            'create' => Pages\CreateGarment::route('/create'),
            'view' => Pages\ViewGarment::route('/{record}'),
            'edit' => Pages\EditGarment::route('/{record}/edit'),
        ];
    }
}
