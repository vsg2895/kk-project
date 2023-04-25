<?php

namespace Jakten\Jobs;


use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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
class ActivateCourses implements ShouldQueue
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

        $this->onQueue('courses-activate-' . env('APP_ENV'));

        if ($course->start_time->subRealHours(24)->isFuture()) {
            $this->delay(
                $course
                    ->start_time
                    ->subDay()
                    ->diffInRealSeconds(Carbon::now())
            );

            $bot->log('course_queue', [
                'course_id' => $course->id,
                'start' => $course->start_time->toDateTimeString(),
                'activation_time' => $course->start_time->subRealHours(24)->toDateTimeString(),
                'activation_starts_in' => $course->start_time->isFuture() ? $course
                        ->start_time
                        ->subDay()
                        ->diffInRealSeconds(Carbon::now()) . ' seconds' : 'Already started or completed'
            ]);
        }

        $bot->log('course_activation_initiated', ['course_id' => $course->id]);

        Log::info('Courses Activation has been constructed');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Course #{$this->course->id} Activation is being handled");

        /** @var KKJTelegramBotService $bot */
        $bot = resolve(KKJTelegramBotService::class);

        if ($this->course->bookings->count() > 0) {
            $bot->log('course_activation_started', ['course_id' => $this->course->id]);
            $orders = [];

            $this->course
                ->orderItems()
                ->each(function (OrderItem $orderItem) use (&$orders) {
                    if (!$orderItem->delivered && !$orderItem->cancelled && !$orderItem->credited) {
                        Log::info("Order Item #{$orderItem->id} which belongs to {$orderItem->order_id} will be processed");
                        $orders[$orderItem->order_id][] = $orderItem->id;
                    }
                });

            /** @var KlarnaService $klarna */
            $klarna = resolve(KlarnaService::class);

            foreach ($orders as $orderId => $orderItemsId) {
                try {
                    /** @var Order $order */
                    $order = Order::where('id', '=', $orderId)->first();
                    $bot->log('course_order_activation', ['order_id' => $orderId, 'items' => $orderItemsId, 'course_id' => $this->course->id]);
                    Log::info("Set delivered items : " . json_encode($orderItemsId) . ". Order #{$order->id}");

                    $items = OrderItem::query()->whereIn('id', $orderItemsId)->get();
                    if (!$order->isKlarnaV3()) {
                        $klarna->activateItems($order, $items);
                    } else {
                        $klarna->captureOrderItems($order, $items);
                    }

                    $order->handleOrderIfAllItemsAreFulfilled();
                } catch (\Exception $exception) {
                    Log::error($exception->getMessage());
                }
            }
        }
    }
}
