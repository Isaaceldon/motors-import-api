<?php

namespace App\Notifications;

use App\Models\ContactRequest;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactRequestAdminNotification extends Notification
{
    public function __construct(public ContactRequest $contactRequest)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouveau message de contact')
            ->greeting('Bonjour,')
            ->line("Un nouveau message a été envoyé par {$this->contactRequest->name}.")
            ->line('Sujet : '.($this->contactRequest->subject ?? '—'))
            ->line('Message : '.$this->contactRequest->message)
            ->line('Contact : '.$this->contactRequest->email.' / '.($this->contactRequest->phone ?? '—'))
            ->action('Voir le message', config('app.url').'/admin/contact-requests/'.$this->contactRequest->id.'/edit')
            ->salutation("Cordialement,\nUSA Motors Import");
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('Nouveau message de contact')
            ->body("{$this->contactRequest->name} : ".\Illuminate\Support\Str::limit($this->contactRequest->message, 60))
            ->icon('heroicon-o-envelope')
            ->iconColor('info')
            ->actions([
                Action::make('view')
                    ->label('Voir le message')
                    ->url('/admin/contact-requests/'.$this->contactRequest->id.'/edit')
                    ->markAsRead(),
            ])
            ->getDatabaseMessage();
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'new_contact_request',
            'contact_request_id' => $this->contactRequest->id,
            'title' => 'Nouveau message de contact',
            'message' => "{$this->contactRequest->name} : ".\Illuminate\Support\Str::limit($this->contactRequest->message, 60),
            'url' => '/admin/contact-requests/'.$this->contactRequest->id.'/edit',
        ];
    }
}
