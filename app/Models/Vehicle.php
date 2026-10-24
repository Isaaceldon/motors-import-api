<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected ?string $pendingDescription = null;

    protected $fillable = [
        'brand_id',
        'model',
        'version',
        'year',
        'condition',
        'status',
        'price_usd',
        'price_fcfa',
        'deposit_percentage_snapshot',
        'mileage',
        'fuel_type',
        'transmission',
        'engine',
        'color',
        'doors',
        'seats',
        'body_style',
        'vin',
        'is_featured',
        'sold_at',
        'description',
    ];

    protected $casts = [
        'price_usd' => 'decimal:2',
        'price_fcfa' => 'decimal:2',
        'is_featured' => 'boolean',
        'sold_at' => 'datetime',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(VehicleTranslation::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(VehiclePhoto::class)->orderBy('position');
    }

    public function getMainPhotoUrlAttribute(): ?string
    {
        return $this->photos->first()?->url;
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->relationLoaded('translations')
            ? $this->translations->firstWhere('locale', 'fr')?->description
            : $this->translations()->where('locale', 'fr')->value('description');
    }

    public function setDescriptionAttribute($value): void
    {
        $this->pendingDescription = $value;
    }

    protected static function booted(): void
    {
        static::saved(function (self $vehicle) {
            if ($vehicle->pendingDescription === null) {
                return;
            }

            $vehicle->translations()->updateOrCreate(
                ['locale' => 'fr'],
                ['description' => $vehicle->pendingDescription]
            );

            $vehicle->pendingDescription = null;
        });
    }
}
