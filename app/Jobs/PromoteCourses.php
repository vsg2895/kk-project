<?php

namespace Jakten\Jobs;


use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Jakten\Mail\PromoteCourses as PromoteCoursesMail;
use Jakten\Models\Course;
use Jakten\Repositories\OrderItemRepository;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\OrderService;
use Jakten\Services\StudentLoyaltyProgramService;

/**
 * @property OrderItemRepository orderItemRepository
 * @property OrderService orderService
 * @property Course course
 */
class PromoteCourses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Course */
    private $course;

    /**
     * Create a new job instance.
     *
     * @param Course $course
     */
    public function __construct(Course $course)
    {
        /** @var KKJTelegramBotService $bot */
        $bot = resolve(KKJTelegramBotService::class);
        $this->course = $course;

        $this->onQueue('courses-promote-' . env('APP_ENV'));

        /*$this->delay(
            $course
                ->start_time
                ->addRealHours(5)
                ->diffInRealSeconds(Carbon::now())
        );*/

        //send reminder to 1 hour after course finished
        $activationTime = $course->start_time
            ->addMinutes($course->length_minutes)
            ->addRealHours(1);
        $this->delay($activationTime->diffInRealSeconds(Carbon::now()));

        $bot->log('courses_promote', [
            'course_id' => $course->id,
            'start' => $course->start_time->toDateTimeString(),
            'activation_time' => $activationTime->toDateTimeString(),
            ]
        );

        $bot->log('course_promote_initiated', ['course_id' => $course->id]);

        Log::info('Courses Promote has been constructed: ', [
            'course_id' => $course->id,
            'start' => $course->start_time->toDateTimeString(),
            'activation_time' => $activationTime->toDateTimeString(),
        ]);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Course #{$this->course->id} Promote is being handled");

        /** @var KKJTelegramBotService $bot */
        $bot = resolve(KKJTelegramBotService::class);

        if ($this->course->bookings->count() > 0) {
            $bot->log('course_promote_started', ['course_id' => $this->course->id]);
            foreach ($this->course->orderItems as $orderItem) {
                
                if ($orderItem->cancelled) {
                    continue;
                }

                //apply student loyalty benefits
                if (in_array($this->course->vehicle_segment_id, array_keys(StudentLoyaltyProgramService::SEGMENT_BENEFITS))) {
                    StudentLoyaltyProgramService::applyBenefits($this->course, $orderItem->participant->email);
                    StudentLoyaltyProgramService::claimBalanceBenefit($this->course, $orderItem->participant->email);
                }

                $bot->log('course_promote_send', ['participant' => $orderItem->participant]);
                Log::info("course_promote_send: course_id #{$this->course->id}");
                Mail::send(new PromoteCoursesMail($this->course, $orderItem->participant->email));
            }
        }
    }
}
