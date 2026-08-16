<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Client $client, public string $code) {}
    public function build()
    {
        return $this->subject('Your password reset code')
            ->markdown('emails.verify_code', [
                'client' => $this->client,
                'code'   => $this->code,
            ]);
    }
}
