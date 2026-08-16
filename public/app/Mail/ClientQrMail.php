<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientQrMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $qrUrl;
    public $qrImageUrl;

    public function __construct(Client $client, $qrUrl, $qrImageUrl)
    {
        $this->client = $client;
        $this->qrUrl = $qrUrl;
        $this->qrImageUrl = $qrImageUrl;
    }

    public function build()
    {
        return $this->subject('تأكيد التسجيل - Dubai Affiliate Summit ')
                    ->markdown('emails.client.qr');
    }
}
