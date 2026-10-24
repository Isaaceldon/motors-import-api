<?php

namespace App\Notifications;

use App\Models\Vehicle;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewVehicleArrivalNotification extends Notification
{
    public function __construct(public Vehicle $vehicle)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $vehicle = $this->vehicle;
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');

        $mail = (new MailMessage)
            ->subject('Nouvel arrivage : '.$vehicle->year.' '.$vehicle->brand->name.' '.$vehicle->model)
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line("Un nouveau véhicule vient d'arriver chez USA Motors Import :");

        if ($vehicle->main_photo_url) {
            $mail->line('!['.$vehicle->model.']('.$vehicle->main_photo_url.')');
        }

        return $mail
            ->line("**{$vehicle->year} {$vehicle->brand->name} {$vehicle->model}**".($vehicle->version ? " {$vehicle->version}" : ''))
            ->line('Prix : $'.number_format((float) $vehicle->price_usd, 2).' ('.number_format((float) $vehicle->price_fcfa, 0, ',', ' ').' FCFA)')
            ->line('Kilométrage : '.($vehicle->mileage ? number_format($vehicle->mileage, 0, ',', ' ').' km' : 'Neuf'))
            ->line('Carburant : '.($vehicle->fuel_type ?? '—').' | Transmission : '.($vehicle->transmission ?? '—'))
            ->action('Voir le véhicule', $frontendUrl.'/vehicules/'.$vehicle->id)
            ->line('Ne tardez pas, nos véhicules partent vite !')
            ->salutation("Cordialement,\nUSA Motors Import");
    }

    public function toArray($notifiable): array
    {
        $vehicle = $this->vehicle;

        return [
            'type' => 'new_vehicle_arrival',
            'vehicle_id' => $vehicle->id,
            'title' => 'Nouvel arrivage',
            'message' => "{$vehicle->year} {$vehicle->brand->name} {$vehicle->model} vient d'arriver.",
            'image' => $vehicle->main_photo_url,
            'url' => '/vehicules/'.$vehicle->id,
        ];
    }
}
