<?php

namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;

class TopPartnerMail extends AbstractMail
{
    use ClassResolver;

    public $schoolName;

    public $schoolEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($schoolName, $schoolEmail)
    {
        $this->setQueue();
        $this->schoolName = $schoolName;
        $this->schoolEmail = $schoolEmail;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to(config('mail.contact_email'))
            ->subject('Top Partner Application')
            ->markdown('email::school_contact.application-top-partner',
                ['schoolName' => $this->schoolName, 'schoolEmail' => $this->schoolEmail]);
    }
}
