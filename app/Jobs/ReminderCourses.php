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
use Jakten\Mail\OrderCreated;
use Jakten\Models\Course;
use Jakten\Models\Order;
use Jakten\Models\OrderItem;
use Jakten\Repositories\OrderItemRepository;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\OrderService;
use Jakten\Services\Payment\Klarna\KlarnaService;

/**
 * @property OrderItemRepository orderItemRepository
 * @property OrderService orderService
 * @property Course course
 */
class ReminderCourses implements ShouldQueue
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

        $this->onQueue('courses-reminder-' . env('APP_ENV'));

        if ($course->start_time->subRealHours(40)->isFuture()) {
            $this->delay(
                $course
                    ->start_time
                    ->subRealHours(40)
                    ->diffInRealSeconds(Carbon::now())
            );

            $bot->log('course_reminder', [
                'course_id' => $course->id,
                'start' => $course->start_time->toDateTimeString(),
                'activation_time' => $course->start_time->subRealHours(40)->toDateTimeString(),
                'activation_starts_in' => $course->start_time->isFuture() ? $course
                        ->start_time
                        ->subRealHours(40)
                        ->diffInRealSeconds(Carbon::now()) . ' seconds' : 'Already started or completed'
            ]);
        }

        $bot->log('course_reminder_initiated', ['course_id' => $course->id]);

        Log::info('Courses Reminder has been constructed');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Course #{$this->course->id} Reminder is being handled");

        /** @var KKJTelegramBotService $bot */
        $bot = resolve(KKJTelegramBotService::class);

        if ($this->course->bookings->count() > 0) {
            $bot->log('course_reminder_started', ['course_id' => $this->course->id]);
            foreach ($this->course->orderItems  as $orderItem) {

                if ($orderItem->cancelled) {
                    continue;
                }

                $bot->log('course_reminder_item', ['participant' => $orderItem->participant]);
                Mail::send(new OrderCreated($orderItem->order, $orderItem->participant->email, true));
            }
        }
    }
}
