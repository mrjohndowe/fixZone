<?php

namespace App\Notifications\Repair;

use App\Channels\Messages\WhatsAppMessage;
use App\Helpers\CustomHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioSmsMessage;

class NewRepairOrder extends Notification implements ShouldQueue
{
    use Queueable;
    protected $mailer;
    protected $template;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($configs)
    {
        $this->mailer = $configs['mailer'];
        $this->template = $configs['templates']['booking'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $this->mailer;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $variables = $this->getVariables($notifiable);
        return (new MailMessage)
            ->subject(CustomHelper::replacePlaceholders(
                $this->template['subject'],
                $variables
            ))
            ->view('notifications.mail', [
                'content' => CustomHelper::replacePlaceholders(
                    $this->template['mail_content'],
                    $variables
                ),
            ]);
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param      <type>         $notifiable  The notifiable
     *
     * @return     VonageMessage  The vonage message.
     */
    public function toVonage($notifiable)
    {
        return (new VonageMessage())
            ->content(CustomHelper::replacePlaceholders(
                $this->template['sms_content'],
                $this->getVariables($notifiable)
            ));
    }

    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content(CustomHelper::replacePlaceholders(
                $this->template['sms_content'],
                $this->getVariables($notifiable)
            ));
    }

    public function toWhatsApp($notifiable)
    {
        return (new WhatsAppMessage())
            ->to($notifiable->phone)
            ->line(CustomHelper::replacePlaceholders(
                $this->template['sms_content'],
                $this->getVariables($notifiable)
            ));
    }

    protected function getVariables($notifiable)
    {
        return [
            'tracking_id' => $notifiable->tracking,
            'tracking_link' => url('/track/' . $notifiable->tracking),
            'customer_name' => $notifiable->name,
            'agent_name' => $notifiable->user ? $notifiable->user->name : '',
            'order_link' => url('/print/repair-order/' . $notifiable->uuid),
            'dispatch_link' => url('/print/repair-dispatch-info/' . $notifiable->uuid),
            'order_date' => $notifiable->created_at,
            'company_name' => config('app.name'),
        ];
    }
}
