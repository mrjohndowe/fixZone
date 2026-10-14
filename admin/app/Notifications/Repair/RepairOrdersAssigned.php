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

class RepairOrdersAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    private $repair;
    private $orderInfo;
    protected $mailer;
    protected $template;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($configs, $repairOrder)
    {
        $this->repair = $repairOrder;
        $this->mailer = $configs['mailer'];
        $this->template = $configs['templates']['assigning'];
        $this->orderInfo = '[' . $this->repair->tracking . '][' . $this->repair->repairPriority->name . '] ';
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
        $variables = $this->getVariables($this->repair);
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
                $this->getVariables($this->repair)
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
            'tracking_id' => $this->repair->tracking,
            'tracking_link' => url('/track/' . $this->repair->tracking),
            'customer_name' => $this->repair->name,
            'agent_name' => $this->repair->user ? $this->repair->user->name : '',
            'order_link' => url('/print/repair-order/' . $this->repair->uuid),
            'dispatch_link' => url('/print/repair-dispatch-info/' . $this->repair->uuid),
            'order_date' => $notifiable->created_at,
            'company_name' => config('app.name'),
        ];
    }
}
