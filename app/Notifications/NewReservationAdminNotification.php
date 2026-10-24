<?php

namespace App\Notifications;

use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReservationAdminNotification extends Notification
{
    public function __construct(public Reservation $reservation)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $vehicle = $this->reservation->vehicle;

        $mail = (new MailMessage)
            ->subject('Nouvelle réservation reçue')
            ->greeting('Bonjour,')
            ->line("Une nouvelle réservation vient d'être créée par {$this->reservation->customer_name}.")
            ->line("Véhicule : {$vehicle->year} {$vehicle->brand->name} {$vehicle->model}");

        if ($vehicle->main_photo_url) {
            $mail->line('!['.$vehicle->model.']('.$vehicle->main_photo_url.')');
        }

        return $mail
            ->line('Contact : '.$this->reservation->customer_email.' / '.$this->reservation->customer_phone)
            ->line('Acompte attendu : $'.number_format($this->reservation->deposit_amount_usd, 2))
            ->action('Voir la réservation', config('app.url').'/admin/reservations/'.$this->reservation->id.'/edit')
            ->salutation("Cordialement,\nUSA Motors Import");
    }

    public function toDatabase($notifiable): array
    {
        $vehicle = $this->reservation->vehicle;

        return FilamentNotification::make()
            ->title('Nouvelle réservation')
            ->body("{$this->reservation->customer_name} — {$vehicle->year} {$vehicle->brand->name} {$vehicle->model}")
            ->icon('heroicon-o-calendar-days')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->label('Voir la réservation')
                    ->url('/admin/reservations/'.$this->reservation->id.'/edit')
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }

    public function toArray($notifiable): array
    {
        $vehicle = $this->reservation->vehicle;

        return [
            'type' => 'new_reservation',
            'reservation_id' => $this->reservation->id,
            'title' => 'Nouvelle réservation',
            'message' => "{$this->reservation->customer_name} — {$vehicle->year} {$vehicle->brand->name} {$vehicle->model}",
            'url' => '/admin/reservations/'.$this->reservation->id.'/edit',
        ];
    }
}
