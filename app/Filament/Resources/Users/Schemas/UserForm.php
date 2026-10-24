<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Compte')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom complet')
                            ->required(),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel(),
                        Select::make('role')
                            ->label('Rôle')
                            ->options(['client' => 'Client', 'admin' => 'Administrateur'])
                            ->required()
                            ->default('client')
                            ->native(false),
                        TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation) => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->helperText('Laisser vide pour ne pas modifier le mot de passe.'),
                    ]),

                Section::make('Préférences')
                    ->columns(2)
                    ->schema([
                        Select::make('preferred_locale')
                            ->label('Langue')
                            ->options(['fr' => 'Français', 'en' => 'English', 'es' => 'Español'])
                            ->default('fr')
                            ->native(false),
                        Select::make('preferred_currency')
                            ->label('Devise')
                            ->options(['USD' => 'USD', 'XOF' => 'FCFA'])
                            ->default('USD')
                            ->native(false),
                    ]),
            ]);
    }
}
