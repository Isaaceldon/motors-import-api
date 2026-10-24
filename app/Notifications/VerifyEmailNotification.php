<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Bienvenue chez USA Motors Import')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Merci de votre inscription sur USA Motors Import.')
            ->line('Pour activer votre compte, merci de confirmer votre adresse e-mail en cliquant sur le bouton ci-dessous.')
            ->action('Confirmer mon e-mail', $url)
            ->line('Ce lien de vérification expirera dans 60 minutes.')
            ->line("Si vous n'êtes pas à l'origine de cette inscription, aucune action n'est requise.")
            ->salutation("Cordialement,\nUSA Motors Import");
    }
}
