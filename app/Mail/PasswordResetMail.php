<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $redirectLink;
    public $organisation_logo;
    public $year;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $redirectLink, $organisation_logo, $year)
    {
        $this->data = $data;
        $this->redirectLink = $redirectLink;
        $this->organisation_logo = $organisation_logo;
        $this->year = $year;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('Email.PasswordResetEmail');
    }
}
