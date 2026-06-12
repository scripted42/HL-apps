<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Master Data';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Profile')
                    ->description('General information and credit terms.')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. CV. Jaya Abadi'),

                        TextInput::make('bonus_threshold')
                            ->label('Bonus Eligibility Threshold')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(10000000.00)
                            ->required()
                            ->minValue(0),
                    ])->columns(2),

                Forms\Components\Section::make('Cascading Discounts')
                    ->description('Define ordered steps of percentage discounts applied sequentially (not summed). Drag to reorder steps.')
                    ->schema([
                        Repeater::make('discount_lm')
                            ->label('Cascading Discount LM (%)')
                            ->simple(
                                TextInput::make('discount')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                            )
                            ->default([])
                            ->reorderable()
                            ->addActionLabel('Tambah Diskon LM'),

                        Repeater::make('discount_br')
                            ->label('Cascading Discount BR (%)')
                            ->simple(
                                TextInput::make('discount')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->suffix('%')
                            )
                            ->default([])
                            ->reorderable()
                            ->addActionLabel('Tambah Diskon BR'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('discount_lm')
                    ->label('Discount LM')
                    ->getStateUsing(function (Customer $record): string {
                        $steps = $record->discount_lm;
                        if (empty($steps)) return '-';
                        return implode(' ➔ ', array_map(fn($v) => "$v%", $steps));
                    })
                    ->badge()
                    ->color('info'),

                TextColumn::make('discount_br')
                    ->label('Discount BR')
                    ->getStateUsing(function (Customer $record): string {
                        $steps = $record->discount_br;
                        if (empty($steps)) return '-';
                        return implode(' ➔ ', array_map(fn($v) => "$v%", $steps));
                    })
                    ->badge()
                    ->color('success'),

                TextColumn::make('bonus_threshold')
                    ->label('Bonus Eligibility Threshold')
                    ->money('idr')
                    ->sortable(),

                TextColumn::make('deleted_at')
                    ->label('Deleted At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('detail')
                    ->label('Detail & Pelunasan')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('indigo')
                    ->url(fn (Customer $record): string => static::getUrl('detail', ['record' => $record])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'detail' => Pages\CustomerDetail::route('/{record}/detail'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
