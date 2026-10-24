<?php

namespace App\Models;

use App\Notifications\ReservationStatusChangedNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Notification;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'additional_info',
        'status',
        'deposit_amount_usd',
        'deposit_amount_fcfa',
        'cancelled_at',
    ];

    protected $casts = [
        'deposit_amount_usd' => 'decimal:2',
        'deposit_amount_fcfa' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    protected static function booted(): void
    {
        static::updated(function (self $reservation) {
            if (! $reservation->wasChanged('status')) {
                return;
            }

            $previousStatus = $reservation->getOriginal('status');
            $notification = new ReservationStatusChangedNotification($reservation, $previousStatus);

            if ($reservation->user_id) {
                $reservation->user->notify($notification);
            } else {
                Notification::route('mail', $reservation->customer_email)->notify($notification);
            }
        });
    }
}
