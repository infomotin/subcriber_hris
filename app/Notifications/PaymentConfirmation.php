<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmation extends Notification
{
    use Queueable;

    public function __construct(
        public Tenant $tenant,
        public float $amount,
        public string $planName,
        public string $paymentMethod = 'Manual'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $expiresAt = $this->tenant->expires_at
            ? $this->tenant->expires_at->format('F d, Y')
            : 'N/A';

        return (new MailMessage)
            ->subject('Payment Confirmed - Your Subscription at ' . config('app.name'))
            ->greeting('Dear ' . $this->tenant->name . ',')
            ->line('Your payment of **' . number_format($this->amount, 0) . ' BDT** for the **' . $this->planName . '** plan has been received successfully.')
            ->line('Payment Method: **' . $this->paymentMethod . '**')
            ->line('Your subscription is now active until: **' . $expiresAt . '**')
            ->line('Thank you for your continued partnership!')
            ->salutation('Best Regards, ' . config('app.name') . ' Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tenant_id' => $this->tenant->id,
            'amount' => $this->amount,
            'plan' => $this->planName,
            'payment_method' => $this->paymentMethod,
        ];
    }
}
