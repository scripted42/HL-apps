<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Product;
use App\Helpers\DiscountCalculator;
use App\Helpers\BonusCalculator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Carbon\Carbon;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Transactions';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        // Left block: General Information
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Section::make('General Information')
                                    ->schema([
                                        TextInput::make('nomor_bon')
                                            ->label('Nomor Bon')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->default(fn () => 'BON-' . date('Ymd-His'))
                                            ->placeholder('e.g. BON-0001'),

                                        Select::make('customer_id')
                                            ->relationship('customer', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateAllLines($get, $set)),

                                        DatePicker::make('tanggal')
                                            ->default(now())
                                            ->required(),

                                        Select::make('status')
                                            ->options([
                                                'Piutang' => 'Piutang (Outstanding)',
                                                'Lunas' => 'Lunas (Settled)',
                                            ])
                                            ->default('Piutang')
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                                if ($state === 'Lunas') {
                                                    $set('tanggal_pelunasan', now()->toDateString());
                                                } else {
                                                    $set('tanggal_pelunasan', null);
                                                }
                                                self::updateAllLines($get, $set);
                                            }),

                                        DatePicker::make('tanggal_pelunasan')
                                            ->label('Tanggal Pelunasan')
                                            ->visible(fn (Get $get) => $get('status') === 'Lunas')
                                            ->required(fn (Get $get) => $get('status') === 'Lunas')
                                            ->default(now()),
                                    ])->columns(1),
                            ])->columnSpan(1),

                        // Middle/Right block: Items & Totals
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Section::make('Bonus & Shipping Parameters')
                                    ->schema([
                                        Toggle::make('is_bonus')
                                            ->label('Transaksi Bonus (Free Items)')
                                            ->default(false)
                                            ->live()
                                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateAllLines($get, $set)),

                                        TextInput::make('bonuses_claimed')
                                            ->label('Bonuses Claimed (Jumlah Diklaim)')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->visible(fn (Get $get) => (bool) $get('is_bonus'))
                                            ->required(fn (Get $get) => (bool) $get('is_bonus')),

                                        TextInput::make('ongkir')
                                            ->label('Ongkos Kirim (Shipping)')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->live()
                                            ->minValue(0),
                                    ])->columns(2),

                                // Customer Bonus progress display on select
                                Placeholder::make('customer_bonus_progress')
                                    ->label('Bonus Eligibility / Progress')
                                    ->visible(fn (Get $get) => filled($get('customer_id')))
                                    ->content(function (Get $get) {
                                        $customerId = $get('customer_id');
                                        $customer = Customer::find($customerId);
                                        if (!$customer) return '';
                                        
                                        $stats = BonusCalculator::getStats($customer);
                                        $available = $stats['bonuses_available'];
                                        $progress = $stats['progress_percentage'];
                                        $carryOver = $stats['carry_over_omzet'];
                                        $threshold = $stats['threshold'];
                                        
                                        $badgeColor = $available > 0 
                                            ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' 
                                            : 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300';
                                        
                                        return new HtmlString("
                                            <div class='p-4 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg space-y-3'>
                                                <div class='flex justify-between items-center'>
                                                    <span class='text-sm text-slate-500'>Bonus Tersedia (Available):</span>
                                                    <span class='px-2.5 py-0.5 text-xs font-bold rounded-full {$badgeColor}'>
                                                        {$available} Bonus
                                                    </span>
                                                </div>
                                                <div class='space-y-1.5'>
                                                    <div class='flex justify-between text-xs text-slate-400'>
                                                    <span>Progress ke Bonus Eligibility Berikutnya:</span>
                                                        <span>Rp " . number_format($carryOver, 0, ',', '.') . " / Rp " . number_format($threshold, 0, ',', '.') . "</span>
                                                    </div>
                                                    <div class='w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden'>
                                                        <div class='bg-indigo-600 h-2.5 rounded-full transition-all duration-500' style='width: {$progress}%'></div>
                                                    </div>
                                                </div>
                                            </div>
                                        ");
                                    }),
                            ])->columnSpan(2),
                    ]),

                Forms\Components\Section::make('Transaction Items')
                    ->description('Add products to the invoice. Prices are automatically computed with cascading customer discounts.')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state, $statePath) {
                                        $parts = explode('.', $statePath);
                                        self::updateLineTotals($get, $set, "items.{$parts[1]}");
                                    }),

                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1)
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state, $statePath) {
                                        $parts = explode('.', $statePath);
                                        self::updateLineTotals($get, $set, "items.{$parts[1]}");
                                    }),

                                // Readonly / Computed Fields
                                TextInput::make('harga_base')
                                    ->label('Harga Base')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly(),

                                TextInput::make('discounted_unit_price')
                                    ->label('Harga Diskon')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly(),

                                TextInput::make('line_omzet')
                                    ->label('Omzet')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly(),

                                // Hidden snapshots which are saved to the database
                                Forms\Components\Hidden::make('product_name'),
                                Forms\Components\Hidden::make('product_type'),
                                Forms\Components\Hidden::make('harga_modal'),
                                Forms\Components\Hidden::make('discount_steps'),
                                Forms\Components\Hidden::make('line_laba'),
                            ])
                            ->columns(5)
                            ->default([])
                            ->reorderable(false)
                            ->addActionLabel('Tambah Item')
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateAllLines($get, $set)),
                    ]),

                Forms\Components\Section::make('Summary')
                    ->schema([
                        Placeholder::make('totals_summary')
                            ->label('')
                            ->content(function (Get $get) {
                                $items = $get('items') ?: [];
                                $isBonus = (bool) $get('is_bonus');
                                
                                $totalOmzet = 0;
                                foreach ($items as $item) {
                                    $totalOmzet += (float) ($item['line_omzet'] ?? 0);
                                }
                                
                                $ongkir = (float) ($get('ongkir') ?? 0);
                                $totalOwed = $totalOmzet + $ongkir;
                                
                                return new HtmlString("
                                    <div class='p-5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl max-w-md ml-auto space-y-3 shadow-sm'>
                                        <div class='flex justify-between text-sm text-slate-500'>
                                            <span>Total Omzet:</span>
                                            <span class='font-semibold text-slate-800 dark:text-slate-200'>Rp " . number_format($totalOmzet, 2, ',', '.') . "</span>
                                        </div>
                                        <div class='flex justify-between text-sm text-slate-500'>
                                            <span>Ongkos Kirim (Ongkir):</span>
                                            <span class='font-semibold text-slate-800 dark:text-slate-200'>Rp " . number_format($ongkir, 2, ',', '.') . "</span>
                                        </div>
                                        <hr class='border-slate-200 dark:border-slate-800'>
                                        <div class='flex justify-between text-lg font-bold text-slate-800 dark:text-slate-100'>
                                            <span>" . ($isBonus ? 'Total Biaya (Bonus)' : 'Total Piutang (Amount Owed)') . ":</span>
                                            <span class='text-indigo-600 dark:text-indigo-400'>Rp " . number_format($totalOwed, 2, ',', '.') . "</span>
                                        </div>
                                    </div>
                                ");
                            })
                    ]),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Textarea::make('deskripsi')
                            ->label('Deskripsi/Catatan')
                            ->rows(3)
                            ->placeholder('e.g. Catatan pengiriman atau instruksi khusus'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_bon')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Piutang' => 'warning',
                        'Lunas' => 'success',
                    })
                    ->sortable(),

                IconColumn::make('is_bonus')
                    ->label('Bonus?')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('total_owed')
                    ->label('Total Tagihan (Bon)')
                    ->money('idr')
                    ->getStateUsing(fn (Transaction $record) => $record->total_owed),

                TextColumn::make('tanggal_pelunasan')
                    ->label('Tgl Pelunasan')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('Pelanggan')
                    ->relationship('customer', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Piutang' => 'Piutang (Outstanding)',
                        'Lunas' => 'Lunas (Settled)',
                    ]),

                Tables\Filters\TernaryFilter::make('is_bonus')
                    ->label('Filter: Transaksi Bonus'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->url(fn (Transaction $record): string => route('admin.transactions.pdf', $record))
                    ->openUrlInNewTab(),
                
                // Pelunasan single Bon action
                Tables\Actions\Action::make('mark_lunas')
                    ->label('Sudah Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Transaction $record) => $record->status === 'Piutang')
                    ->form([
                        DatePicker::make('tanggal_pelunasan')
                            ->label('Tanggal Pelunasan')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (Transaction $record, array $data) {
                        $record->update([
                            'status' => 'Lunas',
                            'tanggal_pelunasan' => $data['tanggal_pelunasan'],
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Update dynamic values for a single line item in the repeater.
     */
    public static function updateLineTotals(Get $get, Set $set, string $statePath)
    {
        $productId = $get("{$statePath}.product_id");
        $quantity = (int) $get("{$statePath}.quantity") ?: 1;
        $customerId = $get('customer_id');
        $isBonus = (bool) $get('is_bonus');

        if (!$productId || !$customerId) {
            return;
        }

        $product = Product::find($productId);
        $customer = Customer::find($customerId);

        if (!$product || !$customer) {
            return;
        }

        $discountSteps = $product->type === 'LM' 
            ? ($customer->discount_lm ?: []) 
            : ($customer->discount_br ?: []);

        $hargaBase = (float) $product->harga_base;
        $hargaModal = (float) $product->harga_modal;

        if ($isBonus) {
            $discountedUnitPrice = 0.00;
            $lineOmzet = 0.00;
            $lineLaba = 0.00;
            $discountSteps = [];
        } else {
            $discountedUnitPrice = DiscountCalculator::calculate($hargaBase, $discountSteps);
            $lineOmzet = $discountedUnitPrice * $quantity;
            $lineLaba = ($discountedUnitPrice - $hargaModal) * $quantity;
        }

        $set("{$statePath}.product_name", $product->name);
        $set("{$statePath}.product_type", $product->type);
        $set("{$statePath}.harga_base", $hargaBase);
        $set("{$statePath}.harga_modal", $hargaModal);
        $set("{$statePath}.discount_steps", $discountSteps);
        $set("{$statePath}.discounted_unit_price", $discountedUnitPrice);
        $set("{$statePath}.line_omzet", $lineOmzet);
        $set("{$statePath}.line_laba", $lineLaba);
    }

    /**
     * Iterate through all repeater lines and recalculate them.
     */
    public static function updateAllLines(Get $get, Set $set)
    {
        $items = $get('items') ?: [];
        foreach ($items as $index => $item) {
            self::updateLineTotals($get, $set, "items.{$index}");
        }
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
