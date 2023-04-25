<?php

namespace Jakten\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Jakten\Events\NewOrder;
use Jakten\Mail\NewReview as NewReviewEmail;
use Jakten\Mail\SchoolLevelChanged;
use Jakten\Models\School;
use Jakten\Repositories\UserRepository;
use Jakten\Services\LoyaltyProgramService;

class CheckSchoolLevel
{
    /**
     * @var LoyaltyProgramService
     */
    private $loyaltyProgramService;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        LoyaltyProgramService $loyaltyProgramService,
        UserRepository $userRepository
    )
    {
        $this->loyaltyProgramService = $loyaltyProgramService;
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the event.
     *
     * @param  NewOrder  $event
     * @return void
     */
    public function handle(NewOrder $event)
    {
        $schoolEmail = $event->order->school->contact_email;
        $schoolName = $event->order->school->name;
        $loyaltyData = $this->loyaltyProgramService->getLoyaltyLevelBasedOnSchool($event->order->school);

        if ($loyaltyData['loyalty_level'] !== $event->order->school->loyalty_level) {//loyalty level changed
            Log::info("(event) Handle event", [
                "class" => __CLASS__,
                "event" => "NewOrder",
                "mail" => "SchoolLevelChanged",
                "school_id" => $event->order->school->id,
                "levels" => "From " . $event->order->school->loyalty_level . ' to ' . $loyaltyData['loyalty_level'],
            ]);

            //update school loyalty level
            $event->order->school->update(['loyalty_level' => $loyaltyData['loyalty_level']]);

            //send mails
            Mail::send(new SchoolLevelChanged($schoolEmail, $schoolEmail, $schoolName, $loyaltyData['loyalty_level']));

            $users = $this->userRepository->selectAdminsChannels('new_order')->get();

            foreach ($users as $user) {
                Mail::send(new SchoolLevelChanged($user->email, $schoolEmail, $schoolName, $loyaltyData['loyalty_level']));
            }

            //send mail to admin
            Mail::send(new SchoolLevelChanged(config('mail.contact_email'), $schoolEmail, $schoolName, $loyaltyData['loyalty_level']));
        }
    }
}
