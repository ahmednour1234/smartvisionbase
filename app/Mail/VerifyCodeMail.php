<?php
// app/Mail/VerifyCodeMail.php
namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Client $client, public string $code) {}

    public function build()
    {
        return $this->subject('Your Verification Code')
            ->view('emails.verify_code');
    }
}
