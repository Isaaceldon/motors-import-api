<?php

namespace App\Filament\Resources\ContactRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->description(fn ($record) => $record->email)
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('Sujet')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('message')
                    ->label('Message')
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'nouvelle' => 'Nouvelle',
                        'traitee' => 'Traitée',
                        'archivee' => 'Archivée',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'nouvelle' => 'danger',
                        'traitee' => 'success',
                        'archivee' => 'gray',
                        default => 'gray',
                    }),
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
                        'nouvelle' => 'Nouvelle',
                        'traitee' => 'Traitée',
                        'archivee' => 'Archivée',
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
