<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SparePartPhoto extends Model
{
    protected $fillable = ['spare_part_id', 'path', 'position'];

    protected $appends = ['url'];

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => str_starts_with($this->path, 'http')
                ? $this->path
                : Storage::disk('public')->url($this->path),
        );
    }
}
