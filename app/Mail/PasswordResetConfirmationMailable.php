<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetConfirmationMailable extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $year;

    public $organisation_logo;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $organisation_logo, $year)
    {
        $this->data = $data;
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
        return $this->markdown('mail.password-reset-confirmation-mailable');
    }

    public function subject($subject)
    {
        return "Password Reset Confirmation";
    }


}
