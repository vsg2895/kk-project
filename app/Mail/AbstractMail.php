<?php namespace Jakten\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Jakten\Helpers\Queues;

/**
 * Class AbstractMail
 * @package Jakten\Mail
 */
class AbstractMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * setQueue
     */
    protected function setQueue()
    {
        $this->onQueue(Queues::getName(Queues::TYPE_EMAIL));
    }
}
