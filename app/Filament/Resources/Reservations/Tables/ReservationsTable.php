<?php

namespace App\Filament\Resources\Reservations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.model')
                    ->label('Véhicule')
                    ->formatStateUsing(fn ($record) => "{$record->vehicle->year} {$record->vehicle->brand->name} {$record->vehicle->model}")
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->label('Client')
                    ->description(fn ($record) => $record->customer_email)
                    ->searchable(),
                TextColumn::make('customer_phone')
                    ->label('Téléphone')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'en_attente' => 'En attente',
                        'acompte_paye' => 'Acompte payé',
                        'annulee' => 'Annulée',
                        'terminee' => 'Terminée',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'en_attente' => 'warning',
                        'acompte_paye' => 'success',
                        'annulee' => 'danger',
                        'terminee' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('deposit_amount_usd')
                    ->label('Acompte')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Reçue le')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'en_attente' => 'En attente',
                        'acompte_paye' => 'Acompte payé',
                        'annulee' => 'Annulée',
                        'terminee' => 'Terminée',
                    ]),
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
