<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class SetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable)
    {
        // ✅ Use company_name from settings if available, otherwise APP_NAME
        $company = \App\Models\Setting::first()->company_name ?? config('app.name');

        return (new MailMessage)
            ->subject("Set Your Password - {$company}")
            ->greeting("Hello {$notifiable->first_name}!")
            ->line("Your account has been created in {$company}. Please click the button below to set your password:")
            ->action('Set Password', url("/reset-password/{$this->token}?email=" . urlencode($notifiable->email)))
            ->line("This password reset link will expire in 60 minutes.")
            ->line("If you did not expect this email, no further action is required.");
    }
}
