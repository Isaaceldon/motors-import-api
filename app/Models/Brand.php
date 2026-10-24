<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'deposit_percentage',
    ];

    protected $casts = [
        'deposit_percentage' => 'integer',
    ];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function depositHistories(): HasMany
    {
        return $this->hasMany(BrandDepositHistory::class);
    }

    protected static function booted(): void
    {
        static::updated(function (self $brand) {
            if (! $brand->wasChanged('deposit_percentage')) {
                return;
            }

            $brand->depositHistories()->create([
                'changed_by_user_id' => auth()->id(),
                'old_percentage' => $brand->getOriginal('deposit_percentage'),
                'new_percentage' => $brand->deposit_percentage,
            ]);
        });
    }
}
