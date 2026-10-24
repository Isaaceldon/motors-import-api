<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationStatusChangedNotification extends Notification
{
    private const LABELS = [
        'en_attente' => 'En attente',
        'acompte_paye' => 'Acompte payé',
        'annulee' => 'Annulée',
        'terminee' => 'Terminée',
    ];

    public function __construct(public Reservation $reservation, public string $previousStatus)
    {
    }

    public function via($notifiable): array
    {
        return $notifiable instanceof \App\Models\User ? ['mail', 'database'] : ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $vehicle = $this->reservation->vehicle;
        $statusLabel = self::LABELS[$this->reservation->status] ?? $this->reservation->status;

        $mail = (new MailMessage)
            ->subject('Mise à jour de votre réservation')
            ->greeting('Bonjour '.$this->reservation->customer_name.',')
            ->line("Le statut de votre réservation pour le véhicule {$vehicle->year} {$vehicle->brand->name} {$vehicle->model} a changé.");

        if ($vehicle->main_photo_url) {
            $mail->line('!['.$vehicle->model.']('.$vehicle->main_photo_url.')');
        }

        return $mail
            ->line("Nouveau statut : **{$statusLabel}**")
            ->line('Pour toute question, n\'hésitez pas à nous contacter.')
            ->salutation("Cordialement,\nUSA Motors Import");
    }

    public function toArray($notifiable): array
    {
        $vehicle = $this->reservation->vehicle;
        $statusLabel = self::LABELS[$this->reservation->status] ?? $this->reservation->status;

        return [
            'type' => 'reservation_status_changed',
            'reservation_id' => $this->reservation->id,
            'title' => 'Réservation mise à jour',
            'message' => "{$vehicle->year} {$vehicle->brand->name} {$vehicle->model} — statut : {$statusLabel}",
            'image' => $vehicle->main_photo_url,
            'url' => '/espace-client/reservations',
        ];
    }
}
