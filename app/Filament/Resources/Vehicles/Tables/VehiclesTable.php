<?php

namespace App\Filament\Resources\Vehicles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class VehiclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover')
                    ->label('')
                    ->state(fn ($record) => $record->photos->first()?->url)
                    ->square(),
                TextColumn::make('brand.name')
                    ->label('Marque')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('model')
                    ->label('Modèle')
                    ->searchable()
                    ->description(fn ($record) => $record->version),
                TextColumn::make('year')
                    ->label('Année')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('condition')
                    ->label('État')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state) => $state === 'neuf' ? 'info' : 'gray'),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'disponible' => 'Disponible',
                        'reserve' => 'Réservé',
                        'vendu' => 'Vendu',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'disponible' => 'success',
                        'reserve' => 'warning',
                        'vendu' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('price_usd')
                    ->label('Prix')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('mileage')
                    ->label('Kilométrage')
                    ->numeric()
                    ->sortable()
                    ->suffix(' km'),
                IconColumn::make('is_featured')
                    ->label('En avant')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'disponible' => 'Disponible',
                        'reserve' => 'Réservé',
                        'vendu' => 'Vendu',
                    ]),
                SelectFilter::make('condition')
                    ->label('État')
                    ->options([
                        'neuf' => 'Neuf',
                        'occasion' => 'Occasion',
                    ]),
                SelectFilter::make('brand_id')
                    ->label('Marque')
                    ->relationship('brand', 'name'),
                TernaryFilter::make('is_featured')
                    ->label('En avant'),
            ])
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
