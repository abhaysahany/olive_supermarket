<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public string $token;

    /**
     * Create a notification instance.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via(mixed $notifiable): array|string
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url') ?? env('FRONTEND_URL');
        $email = $notifiable->getEmailForPasswordReset();

        if ($frontendUrl) {
            $resetUrl = rtrim($frontendUrl, '/') . '/reset-password?token=' . $this->token . '&email=' . urlencode($email);
        } else {
            $resetUrl = url('/reset-password?token=' . $this->token . '&email=' . urlencode($email));
        }

        $expireMinutes = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60);
        $appName = config('app.name', 'Olivia Supermarket');

        return (new MailMessage)
            ->subject('Reset Your Password — ' . $appName)
            ->view('emails.reset-password', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
                'token' => $this->token,
                'expireMinutes' => $expireMinutes,
                'appName' => $appName,
            ]);
    }
}
