<?php

namespace App\Filament\Resources\ContactRequests\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContactRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('subject'),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                Select::make('vehicle_id')
                    ->relationship('vehicle', 'id'),
                Select::make('spare_part_id')
                    ->relationship('sparePart', 'name'),
                TextInput::make('status')
                    ->required()
                    ->default('nouvelle'),
            ]);
    }
}
