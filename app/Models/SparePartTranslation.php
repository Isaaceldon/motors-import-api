<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SparePartTranslation extends Model
{
    protected $fillable = ['spare_part_id', 'locale', 'description'];

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }
}
