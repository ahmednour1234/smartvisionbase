<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SponsorWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function build()
    {
        return $this->subject('Forex Traders Summit - Welcome Sponsor')
                    ->markdown('emails.client.qrcodesponsor'); // يمكنك تغيير اسم الـ view إذا أردت
    }
}
