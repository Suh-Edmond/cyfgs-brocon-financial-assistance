<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemberInvitationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user_name;
    public $user_email;
    public $redirectLink;
    public $organisation_logo;
    public $sender;
    public $organisation_name;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user_name, $user_email, $redirectLink, $organisation_logo, $sender, $organisation_name)
    {
        $this->user_name = $user_name;
        $this->user_email = $user_email;
        $this->redirectLink = $redirectLink;
        $this->organisation_logo = $organisation_logo;
        $this->sender = $sender;
        $this->organisation_name = $organisation_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('Email.MemberInvitationEmail');
    }
}
