<?php

namespace App\Filament\Resources\Brands\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BrandsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_path')
                    ->label('')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Marque')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('deposit_percentage')
                    ->label('Acompte')
                    ->numeric()
                    ->sortable()
                    ->suffix('%')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('vehicles_count')
                    ->label('Véhicules')
                    ->counts('vehicles'),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
