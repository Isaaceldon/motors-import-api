<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SparePart extends Model
{
    use HasFactory;

    protected ?string $pendingDescription = null;

    protected $fillable = [
        'reference',
        'name',
        'condition',
        'price_usd',
        'price_fcfa',
        'stock_quantity',
        'online_sale_enabled',
        'description',
    ];

    protected $casts = [
        'price_usd' => 'decimal:2',
        'price_fcfa' => 'decimal:2',
        'online_sale_enabled' => 'boolean',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(SparePartTranslation::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(SparePartPhoto::class)->orderBy('position');
    }

    public function getMainPhotoUrlAttribute(): ?string
    {
        return $this->photos->first()?->url;
    }

    public function compatibilities(): HasMany
    {
        return $this->hasMany(SparePartCompatibility::class);
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
        static::saved(function (self $sparePart) {
            if ($sparePart->pendingDescription === null) {
                return;
            }

            $sparePart->translations()->updateOrCreate(
                ['locale' => 'fr'],
                ['description' => $sparePart->pendingDescription]
            );

            $sparePart->pendingDescription = null;
        });
    }
}
