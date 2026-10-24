<?php

namespace App\Notifications;

use App\Models\SparePart;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSparePartArrivalNotification extends Notification
{
    public function __construct(public SparePart $sparePart)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $sparePart = $this->sparePart;
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');

        $mail = (new MailMessage)
            ->subject('Nouvel arrivage : '.$sparePart->name)
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line("Une nouvelle pièce détachée vient d'arriver chez USA Motors Import :");

        if ($sparePart->main_photo_url) {
            $mail->line('!['.$sparePart->name.']('.$sparePart->main_photo_url.')');
        }

        return $mail
            ->line("**{$sparePart->name}**".($sparePart->reference ? " (réf. {$sparePart->reference})" : ''))
            ->line('Prix : $'.number_format((float) $sparePart->price_usd, 2).' ('.number_format((float) $sparePart->price_fcfa, 0, ',', ' ').' FCFA)')
            ->line('État : '.ucfirst($sparePart->condition ?? '—').' | Stock disponible : '.$sparePart->stock_quantity)
            ->action('Voir la pièce', $frontendUrl.'/pieces-detachees')
            ->line('Stock limité, commandez dès maintenant !')
            ->salutation("Cordialement,\nUSA Motors Import");
    }

    public function toArray($notifiable): array
    {
        $sparePart = $this->sparePart;

        return [
            'type' => 'new_spare_part_arrival',
            'spare_part_id' => $sparePart->id,
            'title' => 'Nouvel arrivage',
            'message' => "{$sparePart->name} vient d'arriver.",
            'image' => $sparePart->main_photo_url,
            'url' => '/pieces-detachees',
        ];
    }
}
