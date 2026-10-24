<?php

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Véhicule & client')
                    ->columns(2)
                    ->schema([
                        Select::make('vehicle_id')
                            ->label('Véhicule')
                            ->relationship('vehicle', 'model')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->year} {$record->brand->name} {$record->model}")
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                        Select::make('user_id')
                            ->label('Compte client (si connecté)')
                            ->relationship('user', 'name')
                            ->disabled(),
                        TextInput::make('customer_name')
                            ->label('Nom du client')
                            ->required(),
                        TextInput::make('customer_email')
                            ->label('E-mail')
                            ->email()
                            ->required(),
                        TextInput::make('customer_phone')
                            ->label('Téléphone')
                            ->tel()
                            ->required(),
                        Textarea::make('additional_info')
                            ->label('Informations complémentaires')
                            ->columnSpanFull(),
                    ]),

                Section::make('Suivi de la réservation')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Statut')
                            ->options([
                                'en_attente' => 'En attente',
                                'acompte_paye' => 'Acompte payé',
                                'annulee' => 'Annulée',
                                'terminee' => 'Terminée',
                            ])
                            ->required()
                            ->native(false),
                        DateTimePicker::make('cancelled_at')
                            ->label('Annulée le'),
                        Placeholder::make('deposit_amount_usd')
                            ->label('Acompte (USD)')
                            ->content(fn ($record) => $record ? '$'.number_format($record->deposit_amount_usd, 2) : '—'),
                        Placeholder::make('deposit_amount_fcfa')
                            ->label('Acompte (FCFA)')
                            ->content(fn ($record) => $record ? number_format($record->deposit_amount_fcfa, 0, ',', ' ').' FCFA' : '—'),
                    ]),
            ]);
    }
}
