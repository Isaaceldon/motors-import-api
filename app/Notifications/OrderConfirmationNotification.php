<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmationNotification extends Notification
{
    public function __construct(public Order $order)
    {
    }

    public function via($notifiable): array
    {
        return $notifiable instanceof \App\Models\User ? ['mail', 'database'] : ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Confirmation de votre commande')
            ->greeting('Bonjour '.$this->order->customer_name.',')
            ->line('Nous avons bien reçu votre commande de pièces détachées.');

        $firstPhotoUrl = $this->order->items->first()?->sparePart?->main_photo_url;

        if ($firstPhotoUrl) {
            $mail->line('!['.'Pièce commandée'.']('.$firstPhotoUrl.')');
        }

        foreach ($this->order->items as $item) {
            $mail->line("- {$item->quantity} x {$item->sparePart->name} (\${$item->unit_price_usd})");
        }

        return $mail
            ->line('Total : $'.number_format($this->order->total_usd, 2).' ('.number_format($this->order->total_fcfa, 0, ',', ' ').' FCFA)')
            ->line('Notre équipe vous recontactera pour finaliser le paiement et l\'expédition.')
            ->salutation("Cordialement,\nUSA Motors Import");
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'order_confirmation',
            'order_id' => $this->order->id,
            'title' => 'Commande confirmée',
            'message' => 'Votre commande de $'.number_format($this->order->total_usd, 2).' a été enregistrée.',
            'url' => '/espace-client/commandes',
        ];
    }
}
