<?php namespace Jakten\Listeners;

use Illuminate\Support\Facades\Mail;
use Jakten\Events\ReviewVerified as EventReviewVerified;
use Jakten\Mail\ReviewVerified;
use Illuminate\Support\Facades\Log;
use Jakten\Repositories\UserRepository;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class SendAdminNewReview
 * @package Jakten\Listeners
 */
class SendSchoolNewReview
{

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * SendNewOrderNotify constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the event.
     *
     * @param EventReviewVerified $event
     *
     * @return void
     */
    public function handle(EventReviewVerified $event)
    {
        Log::info("(event) Handle event", [
            "class" => __CLASS__,
            "event" => "SendSchoolNewReview",
            "mail" => "SchoolNewReview",
            "review" => [
                "id" => $event->rating->id,
                "school" => $event->rating->school->id,
                "course" => $event->rating->course->id,
                "user" => $event->rating->user->id,
            ]
        ]);

        Log::info("(event) Handle event", [
            "class" => __CLASS__,
            "event" => "SendAdminNewReview",
            "mail" => "ReviewVerified",
            "link" => route('admin::ratings.edit', $event->rating->id),
        ]);

        Mail::send(new ReviewVerified($event));
    }
}
