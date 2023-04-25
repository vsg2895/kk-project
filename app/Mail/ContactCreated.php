<?php namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\ContactRequest;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class ContactCreated
 * @property KKJTelegramBotService telegramBotService
 * @package Jakten\Mail
 */
class ContactCreated extends AbstractMail
{

    use ClassResolver;

    /**
     * @var KKJTelegramBotService
     */
    public $telegramBotService;

    /**
     * @var ContactRequest
     */
    public $contactRequest;

    /**
     * Create a new message instance.
     *
     * @param ContactRequest $contactRequest
     */
    public function __construct(ContactRequest $contactRequest)
    {
        $this->setQueue();
        $this->contactRequest = $contactRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Ny kontaktförfrågan via Körkortsjakten';
        $to = config('mail.contact_email');
        if ($this->contactRequest->school_id && $this->contactRequest->title !== 'Felaktig prisinformation') {
            $to = $this->contactRequest->school->contact_email;
        } elseif ($this->contactRequest->title == 'Felaktig prisinformation') {
            $subject = $this->contactRequest->title;
        }

        return $this->markdown('email::contact.created')->to($to)->subject($subject);
    }
}
