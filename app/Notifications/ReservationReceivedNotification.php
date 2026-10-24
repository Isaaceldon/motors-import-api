<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationReceivedNotification extends Notification
{
    public function __construct(public Reservation $reservation)
    {
    }

    public function via($notifiable): array
    {
        return $notifiable instanceof \App\Models\User ? ['mail', 'database'] : ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $vehicle = $this->reservation->vehicle;

        $mail = (new MailMessage)
            ->subject('Votre réservation a bien été reçue')
            ->greeting('Bonjour '.$this->reservation->customer_name.',')
            ->line("Nous avons bien reçu votre demande de réservation pour le véhicule {$vehicle->year} {$vehicle->brand->name} {$vehicle->model}.");

        if ($vehicle->main_photo_url) {
            $mail->line('!['.$vehicle->model.']('.$vehicle->main_photo_url.')');
        }

        return $mail
            ->line('Montant de l\'acompte : $'.number_format($this->reservation->deposit_amount_usd, 2).' ('.number_format($this->reservation->deposit_amount_fcfa, 0, ',', ' ').' FCFA)')
            ->line('Notre équipe va vous contacter sous peu pour finaliser le paiement de l\'acompte.')
            ->line('Merci de votre confiance.')
            ->salutation("Cordialement,\nUSA Motors Import");
    }

    public function toArray($notifiable): array
    {
        $vehicle = $this->reservation->vehicle;

        return [
            'type' => 'reservation_received',
            'reservation_id' => $this->reservation->id,
            'title' => 'Réservation reçue',
            'message' => "Votre demande de réservation pour {$vehicle->year} {$vehicle->brand->name} {$vehicle->model} a été enregistrée.",
            'image' => $vehicle->main_photo_url,
            'url' => '/espace-client/reservations',
        ];
    }
}
