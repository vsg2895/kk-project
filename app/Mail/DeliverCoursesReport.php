<?php

namespace Jakten\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Jakten\Helpers\ClassResolver;
use Jakten\Services\KKJTelegramBotService;

/**
 * @property KKJTelegramBotService telegramBotService
 */
class DeliverCoursesReport extends Mailable
{
    use Queueable, SerializesModels, ClassResolver;

    /**
     * @var array
     */
    protected $data;

    /**
     * Create a new message instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email::delivery_courses_report', ['data' => $this->data]);
    }
}
