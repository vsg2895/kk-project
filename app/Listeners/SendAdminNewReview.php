<?php namespace Jakten\Listeners;

use Illuminate\Support\Facades\Mail;
use Jakten\Events\NewReview;
use Jakten\Mail\NewReview as NewReviewEmail;
use Illuminate\Support\Facades\Log;
use Jakten\Models\User;
use Jakten\Repositories\UserRepository;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class SendAdminNewReview
 * @package Jakten\Listeners
 */
class SendAdminNewReview
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
     * @param  NewReview $event
     *
     * @return void
     */
    public function handle(NewReview $event)
    {
        Log::info("(event) Handle event", [
            "class" => __CLASS__,
            "event" => "SendAdminNewReview",
            "mail" => "AdminNewReview",
            "review" => [
                "id" => $event->rating->id,
                "school" => $event->school->id,
                "course" => $event->course->id,
                "user" => $event->user->id,
            ]
        ]);

        $users = $this->userRepository->selectAdminsChannels('new_order')->get();

        Log::info("(event) Handle event", [
            "class" => __CLASS__,
            "event" => "SendAdminNewReview",
            "mail" => "NewReviewEmail",
            "link" => route('admin::ratings.edit', $event->rating->id),
        ]);


        foreach ($users as $user) {
            Mail::send(new NewReviewEmail($event, $user));
        }

        $userAdditional = User::query()->where('email', 'kontakt@korkortsjakten.se')->firstOrFail();
        Mail::send(new NewReviewEmail($event, $userAdditional));
    }
}
