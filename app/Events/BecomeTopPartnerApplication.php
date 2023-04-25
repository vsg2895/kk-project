<?php namespace Jakten\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BecomeTopPartnerApplication
 * @package Jakten\Events
 */
class BecomeTopPartnerApplication
{
    use Dispatchable, SerializesModels;

    /**
     * @var string
     */
    public $schoolName;

    /**
     * @var string
     */
    public $schoolEmail;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($schoolName, $schoolEmail)
    {
        $this->schoolName = $schoolName;
        $this->schoolEmail = $schoolEmail;
    }

}
