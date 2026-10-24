<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use App\Models\Brand;
use App\Services\VinDecoderService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identification')
                    ->columns(2)
                    ->schema([
                        TextInput::make('vin')
                            ->label('Numéro de châssis (VIN)')
                            ->maxLength(17)
                            ->suffixAction(
                                \Filament\Actions\Action::make('decodeVin')
                                    ->icon('heroicon-m-magnifying-glass')
                                    ->tooltip('Décoder le VIN via NHTSA vPIC')
                                    ->action(function (\Filament\Schemas\Components\Utilities\Get $get, \Filament\Schemas\Components\Utilities\Set $set) {
                                        $vin = $get('vin');

                                        if (! $vin) {
                                            Notification::make()->title('Saisissez un VIN d\'abord')->warning()->send();

                                            return;
                                        }

                                        $data = app(VinDecoderService::class)->decode($vin);

                                        if (! $data) {
                                            Notification::make()->title('VIN introuvable ou invalide')->danger()->send();

                                            return;
                                        }

                                        if (! empty($data['model'])) {
                                            $set('model', $data['model']);
                                        }
                                        if (! empty($data['version'])) {
                                            $set('version', $data['version']);
                                        }
                                        if (! empty($data['year'])) {
                                            $set('year', (int) $data['year']);
                                        }
                                        if (! empty($data['body_style'])) {
                                            $set('body_style', $data['body_style']);
                                        }
                                        if (! empty($data['fuel_type'])) {
                                            $set('fuel_type', $data['fuel_type']);
                                        }
                                        if (! empty($data['transmission'])) {
                                            $set('transmission', $data['transmission']);
                                        }
                                        if (! empty($data['engine'])) {
                                            $set('engine', $data['engine']);
                                        }
                                        if (! empty($data['doors'])) {
                                            $set('doors', (int) $data['doors']);
                                        }

                                        if (! empty($data['make'])) {
                                            $brand = Brand::whereRaw('LOWER(name) = ?', [strtolower($data['make'])])->first();
                                            if ($brand) {
                                                $set('brand_id', $brand->id);
                                                $set('deposit_percentage_snapshot', $brand->deposit_percentage);
                                            }
                                        }

                                        Notification::make()->title('Champs pré-remplis depuis le VIN')->success()->send();
                                    })
                            ),
                        Select::make('brand_id')
                            ->label('Marque')
                            ->relationship('brand', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, \Filament\Schemas\Components\Utilities\Set $set) {
                                if ($state) {
                                    $set('deposit_percentage_snapshot', Brand::find($state)?->deposit_percentage);
                                }
                            }),
                        TextInput::make('model')
                            ->label('Modèle')
                            ->required(),
                        TextInput::make('version')
                            ->label('Version / Finition'),
                        TextInput::make('year')
                            ->label('Année')
                            ->required()
                            ->numeric()
                            ->minValue(1980)
                            ->maxValue((int) date('Y') + 1),
                        TextInput::make('body_style')
                            ->label('Carrosserie'),
                    ]),

                Section::make('Prix & disponibilité')
                    ->columns(3)
                    ->schema([
                        Select::make('condition')
                            ->label('État')
                            ->options(['neuf' => 'Neuf', 'occasion' => 'Occasion'])
                            ->required()
                            ->native(false),
                        Select::make('status')
                            ->label('Statut')
                            ->options(['disponible' => 'Disponible', 'reserve' => 'Réservé', 'vendu' => 'Vendu'])
                            ->required()
                            ->default('disponible')
                            ->native(false),
                        Toggle::make('is_featured')
                            ->label('Mettre en avant sur l\'accueil')
                            ->inline(false),
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
                        TextInput::make('deposit_percentage_snapshot')
                            ->label('Acompte (%)')
                            ->numeric()
                            ->helperText('Pré-rempli depuis la marque, modifiable pour ce véhicule.'),
                        DateTimePicker::make('sold_at')
                            ->label('Vendu le')
                            ->columnSpan(3),
                    ]),

                Section::make('Caractéristiques techniques')
                    ->columns(3)
                    ->schema([
                        TextInput::make('mileage')
                            ->label('Kilométrage')
                            ->numeric()
                            ->suffix('km'),
                        TextInput::make('fuel_type')
                            ->label('Carburant'),
                        TextInput::make('transmission')
                            ->label('Transmission'),
                        TextInput::make('engine')
                            ->label('Motorisation'),
                        TextInput::make('color')
                            ->label('Couleur'),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('doors')
                                    ->label('Portes')
                                    ->numeric(),
                                TextInput::make('seats')
                                    ->label('Places')
                                    ->numeric(),
                            ]),
                    ]),

                Section::make('Équipements')
                    ->schema([
                        CheckboxList::make('features')
                            ->label('')
                            ->relationship('features', 'name')
                            ->columns(3)
                            ->searchable(),
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
                            ->directory('vehicles')
                            ->disk('public')
                            ->dehydrated(false)
                            ->helperText('Les photos externes existantes (ex. données de démo) ne sont pas affichées ici mais restent conservées.')
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
