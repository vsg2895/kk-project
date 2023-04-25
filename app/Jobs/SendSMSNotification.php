<?php namespace Jakten\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Jakten\Services\SMS46elks;
use Jakten\Helpers\Queues;

/**
 * Class SendSMSNotification
 * @package Jakten\Jobs
 */
class SendSMSNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    protected $sms;

    /**
     * SendSMSNotification constructor.
     *
     * @param array $sms
     */
    public function __construct(array $sms)
    {
        $this->onQueue(Queues::getName(Queues::TYPE_SMS));
        $this->sms = $sms;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        SMS46elks::sendSMS($this->sms);
    }
}