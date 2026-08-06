<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientQrMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $qrUrl;
    public $qrImageUrl;
    public $eventId;
        public $lang;


    /**
     * Create a new message instance.
     */
    public function __construct(Client $client, string $qrUrl, string $qrImageUrl, int $eventId,$lang = 'en')
    {
        $this->client      = $client;
        $this->qrUrl       = $qrUrl;
        $this->qrImageUrl  = $qrImageUrl;
        $this->eventId     = $eventId;
            $this->lang        = $lang;

    }

    /**
     * Build the message.
     */
  public function build()
{
    $event = Event::find($this->eventId);
    $eventName = $this->lang === 'ar'
        ? ($event?->name_ar ?? 'الفعالية')
        : ($event?->name_en ?? 'Event');

    $subject = $this->lang === 'ar'
        ? 'دعوة مجانية لحضور ' . $eventName
        : 'You’re Invited: Free Access to ' . $eventName;

    $view = $this->lang === 'ar'
        ? 'emails.client.qr_ar'
        : 'emails.client.qr';

    return $this->subject($subject)
                ->markdown($view)
                ->with([
                    'client'     => $this->client,
                    'qrUrl'      => $this->qrUrl,
                    'qrImageUrl' => $this->qrImageUrl,
                    'eventName'  => $eventName,
                ]);
}

}
