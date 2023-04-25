<?php namespace Jakten\Mail;

use Jakten\Helpers\ClassResolver;
use Jakten\Models\User;
use Jakten\Repositories\Contracts\UserRepositoryContract;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class OrganizationCreated
 * @property KKJTelegramBotService telegramBotService
 * @package Jakten\Mail
 */
class OrganizationCreated extends AbstractMail
{

    use ClassResolver;

    /**
     * @var User
     */
    public $user;

    /**
     * @var \Jakten\Models\Organization
     */
    public $organization;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->setQueue();
        $this->user = $user;
        $this->organization = $user->organization;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $to = config('mail.contact_email');
        return $this->markdown('email::organization.created')
            ->to($to)->subject('Ny organisation skapad');
    }
}
