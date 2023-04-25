<?php

namespace Jakten\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SchoolLevelChanged extends AbstractMail
{
    use Queueable, SerializesModels;

    private $email;
    private $name;
    private $loyaltyLevel;
    /**
     * @var string
     */
    private $sendTo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $sendTo, string $schoolEmail, string $schoolName, string $loyaltyLevel)
    {
        $this->onQueue('queue-' . env('APP_ENV') . '-email');
        $this->email = $schoolEmail;
        $this->name = $schoolName;
        $this->loyaltyLevel = $loyaltyLevel;
        $this->sendTo = $sendTo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Grattis ni har uppnått ny lojalitets nivå';

        return $this->markdown('email::loyalty_level.changed', [
            'email' => $this->email,
            'name' => $this->name,
            'loyaltyLevel' => $this->loyaltyLevel,
        ])->to($this->sendTo)->subject($subject);
    }
}
