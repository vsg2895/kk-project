<?php namespace Jakten\Observers;

use Jakten\Models\OrderItem;
use Jakten\Services\CourseService;

/**
 * Class OrderItemObserver
 * @package Jakten\Observers
 */
class OrderItemObserver
{
    /**
     * @var CourseService
     */
    private $courseService;

    /**
     * OrderObserver constructor.
     * @param CourseService $courseService
     */
    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * Listen to the OrderItem created event.
     *
     * @param OrderItem $orderItem
     */
    public function created(OrderItem $orderItem)
    {
        //this is unused, but keep the code for a while
//        $school = $orderItem->school;
//        if ($school && $school->left_seats > 0) {
//            $school->decrement('left_seats');
//        }

        if ($orderItem->course && $orderItem->course->seats > 0 && $orderItem->isCourse()) {
            if (in_array($orderItem->course->vehicle_segment_id, \Jakten\Models\VehicleSegment::SHARED_COURSES) && $orderItem->course->digital_shared) {
                $digitalCourses = $this->courseService->getBy(
                    $orderItem->course->vehicle_segment_id,
                    $orderItem->course->start_time
                );

                foreach ($digitalCourses as $digitalCourse) {
                    $digitalCourse->decrement('seats');
                }
            } else {
                $orderItem->course->decrement('seats');
            }
        }
    }
}
