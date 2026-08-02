<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email as SymfonyEmail;

class ClientQrMail extends Mailable
{
    use Queueable, SerializesModels;

    public Client $client;
    public string $qrUrl;
    public string $qrImageUrl;

    public function __construct(Client $client, string $qrUrl, string $qrImageUrl)
    {
        $this->client     = $client;
        $this->qrUrl      = $qrUrl;
        $this->qrImageUrl = $qrImageUrl;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->subject('تأكيد التسجيل - Forex Traders Summit')
            ->markdown('emails.client.qr')
            ->with([
                'client'     => $this->client,
                'qrUrl'      => $this->qrUrl,
                'qrImageUrl' => $this->qrImageUrl,
            ])
            ->withSymfonyMessage(function (SymfonyEmail $message) {
                $message->getHeaders()->addTextHeader(
                    'List-Unsubscribe',
                    '<mailto:unsubscribe@forextraderssummit.com>, <https://forextraderssummit.com/unsubscribe>'
                );
                $message->getHeaders()->addTextHeader('X-Entity-Ref-ID', (string) $this->client->id);
            });
    }
}
