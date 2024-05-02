<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    public $organisation_logo;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $organisation_logo)
    {
        $this->data = $data;
        $this->organisation_logo = $organisation_logo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('Email.PasswordResetConfirmation');
    }
}
