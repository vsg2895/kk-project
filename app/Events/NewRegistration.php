<?php namespace Jakten\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Jakten\Models\User;

/**
 * Class NewRegistration
 * @package Jakten\Events
 */
class NewRegistration
{
    use Dispatchable, SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var bool
     */
    public $newOrganization;

    /**
     * @var string
     */
    public $label = 'new_register';

    /**
     * NewRegistration constructor.
     * @param User $user
     * @param bool $newOrganization
     */
    public function __construct(User $user, $newOrganization = false)
    {
        $this->user = $user;
        $this->newOrganization = $newOrganization;
    }
}
