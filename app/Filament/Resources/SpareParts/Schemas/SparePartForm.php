<?php

namespace App\Filament\Resources\SpareParts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SparePartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->columns(2)
                    ->schema([
                        TextInput::make('reference')
                            ->label('Référence')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('name')
                            ->label('Nom de la pièce')
                            ->required(),
                        Select::make('condition')
                            ->label('État')
                            ->options(['neuve' => 'Neuve', 'occasion' => 'Occasion'])
                            ->required()
                            ->native(false),
                        Toggle::make('online_sale_enabled')
                            ->label('Vente en ligne activée')
                            ->helperText('Désactivé = "Sur devis uniquement"')
                            ->inline(false),
                    ]),

                Section::make('Prix & stock')
                    ->columns(3)
                    ->schema([
                        TextInput::make('price_usd')
                            ->label('Prix (USD)')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('price_fcfa')
                            ->label('Prix (FCFA)')
                            ->required()
                            ->numeric()
                            ->suffix('FCFA'),
                        TextInput::make('stock_quantity')
                            ->label('Quantité en stock')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('Compatibilité')
                    ->schema([
                        Repeater::make('compatibilities')
                            ->relationship()
                            ->label('')
                            ->columns(3)
                            ->schema([
                                Select::make('brand_id')
                                    ->label('Marque')
                                    ->relationship('brand', 'name')
                                    ->required()
                                    ->searchable(),
                                TextInput::make('model')
                                    ->label('Modèle')
                                    ->required(),
                                TextInput::make('year_from')
                                    ->label('Année (de)')
                                    ->numeric(),
                            ])
                            ->addActionLabel('Ajouter une compatibilité')
                            ->defaultItems(0),
                    ]),

                Section::make('Description')
                    ->schema([
                        Textarea::make('description')
                            ->label('Description (français)')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Photos')
                    ->schema([
                        FileUpload::make('photo_uploads')
                            ->label('')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->directory('spare-parts')
                            ->disk('public')
                            ->dehydrated(false)
                            ->afterStateHydrated(function (FileUpload $component, $record) {
                                if ($record) {
                                    $component->state(
                                        $record->photos->pluck('path')
                                            ->reject(fn ($path) => str_starts_with($path, 'http'))
                                            ->toArray()
                                    );
                                }
                            }),
                    ]),
            ]);
    }
}
