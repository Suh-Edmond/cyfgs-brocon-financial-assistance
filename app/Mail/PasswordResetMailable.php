<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMailable extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $redirectLink;
    public $organisation_logo;
    public $year;
    public $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $redirectLink, $organisation_logo, $year, $token)
    {
        $this->data = $data;
        $this->redirectLink = $redirectLink;
        $this->organisation_logo = $organisation_logo;
        $this->year = $year;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.password-reset-mailable');
    }

    public function subject($subject)
    {
        return "Password Reset Mail";
    }


}
