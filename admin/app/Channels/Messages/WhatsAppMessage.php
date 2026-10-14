<?php

namespace App\Channels\Messages;

use Twilio\Rest\Client;

class WhatsAppMessage
{
    protected $sender;
    protected $appKey;
    protected $to;
    protected $line;

    public function __construct() {}

    public function line($line = ''): self
    {
        $this->line = $line;return $this;
    }

    public function to($to): self
    {
        $this->to = $to;return $this;
    }

    public function send()
    {
        if (!$this->to) {
            return;
        }

        if (!$this->line) {
            throw new \Exception(__('The message content is missing'));
        }

        $sid = config('whatsapp.wa_twilio_account_sid', null);
        $token = config('whatsapp.wa_twilio_auth_token', null);
        $from = "whatsapp:+" . config('whatsapp.wa_twilio_from', null);

        $customer = "whatsapp:" . $this->to;

        $twilio = new Client($sid, $token);
        $message = $twilio->messages->create($customer,
            [
                "from" => $from,
                "body" => $this->line,
            ]
        );
    }
}
