<?php namespace Jakten\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Events\MessageSent;

/**
 * Class LogSentMessage
 * @package Jakten\Listeners
 */
class LogSentMessage
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param MessageSent $message
     *
     * @return void
     */
    public function handle(MessageSent $message)
    {
        // TODO: FIX THIS!
        $msg = "No to email in message source.";
        try {
            $msg = (explode("MIME-Version", $message->message))[0];
            $msg = trim((explode("To: ", $msg))[1]);
        }catch (\Error $e) {
        }catch (\ErrorException $e) {
        }catch (\Exception $e) {}

        Log::info("(event) Handle event", [
            "event" => "MessageSending",
            "messageTo" => $msg
        ]);
    }
}
