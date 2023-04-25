<?php namespace Jakten\Services;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Jakten\Events\NewOrder;
use Jakten\Events\NewRegistration;
use Jakten\Events\OrderCancelled;
use Jakten\Events\OrderRebooked;
use Jakten\Exceptions\CourseFullException;
use Jakten\Helpers\Participants;
use Jakten\Helpers\Payment;
use Jakten\Helpers\Roles;
use Jakten\Models\AddonParticipant;
use Jakten\Models\Benefit;
use Jakten\Models\Course;
use Jakten\Models\CourseParticipant;
use Jakten\Models\GiftCard;
use Jakten\Models\GiftCardType;
use Jakten\Models\Order;
use Jakten\Models\OrderItem;
use Jakten\Models\User;
use Jakten\Repositories\Contracts\CourseRepositoryContract;
use Jakten\Repositories\Contracts\GiftCardTypeRepositoryContract;
use Jakten\Services\Payment\Klarna\KlarnaService;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * @var ModelService
     */
    private $modelService;
    /**
     * @var CourseService
     */
    private $courseService;
    /**
     * @var CourseRepositoryContract
     */
    private $courses;

    /** @var  GiftCardService $giftCardService */
    private $giftCardService;

    /** @var  GiftCardService $giftCardService */
    private $giftCardTypes;
    
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var User
     */
    private $userModel;

    /**
     * @var StudentLoyaltyProgramService
     */
    private $studentLoyaltyProgramService;

    /**
     * @var RuleAPIService
     */
    private $ruleAPIService;

    /**
     * UserService constructor.
     *
     * @param ModelService $modelService
     * @param CourseService $courseService
     * @param CourseRepositoryContract $courses
     */
    public function __construct(
        ModelService $modelService,
        CourseService $courseService,
        CourseRepositoryContract $courses,
        GiftCardTypeRepositoryContract $giftCardTypes,
        GiftCardService $giftCardService,
        UserService $userService,
        User $userModel,
        StudentLoyaltyProgramService $studentLoyaltyProgramService,
        RuleAPIService $ruleAPIService
    )
    {
        $this->modelService = $modelService;
        $this->courseService = $courseService;
        $this->courses = $courses;
        $this->giftCardService = $giftCardService;
        $this->giftCardTypes = $giftCardTypes;
        $this->userService = $userService;
        $this->userModel = $userModel;
        $this->studentLoyaltyProgramService = $studentLoyaltyProgramService;
        $this->ruleAPIService = $ruleAPIService;
    }

    /**
     * @param FormRequest $request
     * @param Course $course
     * @param User $user
     *
     * @return \Illuminate\Database\Eloquent\Model|Order
     */
    public function storeOrder(FormRequest $request, Collection $courses, User $user, $schoolId = 0)
    {
        //TODO: Should pass in school ID instead of finding it through courses. Leaving it like this for now though
        foreach ($courses as $course) {
            $schoolId = $course->school_id;
            break;
        }

        [$order, $e] = DB::transaction(function () use ($request, $user, $courses, $schoolId) {
            //Create and save the order
            $orderUser = $user;

            /** @var Order $order */
            $order = $this->modelService->createModel(Order::class, [
                'external_order_id' => $request->input('external_order_id', null),
                'external_reservation_id' => $request->input('external_reservation_id', null),
                'user_id' => $user->id,
                'school_id' => $schoolId,
                'payment_method' => $request->input('payment_method', Payment::MANUAL_PAYMENT),
                'booking_fee' => config('fees.booking_fee_to_kkj')
            ]);

            $order->save();

            try {
                $studentsArray = $request->input('students', []);

                //Save participants as users
                foreach ($studentsArray as $student) {
                    $userExists = $this->userModel->where('email', $student['email'])->first();
                    if (!$userExists) {
                        $user = $this->userService->storeUser(with(new FormRequest(
                            array_merge(
                                $student,
                                ['role_id' => Roles::ROLE_STUDENT]
                            ))));
                        event(new NewRegistration($user));
                    }
                }

                foreach ($courses as $course) {
                    //Add a participant for every course.
                    $benefitId = null;
                    //apply student loyalty program
                    if (in_array($course->vehicle_segment_id, array_keys(StudentLoyaltyProgramService::SEGMENT_BENEFITS))) {
                        $this->studentLoyaltyProgramService->createCourseBenefit($order, $course, $request, $orderUser);
                    }

                    if (in_array($course->vehicle_segment_id, StudentLoyaltyProgramService::BENEFICIARY_SEGMENT_IDS)) {
                        $benefitId = $this->studentLoyaltyProgramService->claimDiscountBenefit($course, $orderUser);
                    }

                    $this->courseService->addParticipants($order, $course, $request, $benefitId, $orderUser);
                }

                $addons = $request->input('addons', []);
                $students = new Collection($request->input('students', []));

                foreach ($addons as $addon) {
                    Log::info('storeOrder@addon: ', $addon);

                    $student = $students->filter(function ($student) use ($addon) {
                        return ($addon['local_id'] == $student['addonId'] || $addon['id'] == $student['addonId']);
                    });

                    $orderItem = [
                        'external_id' => isset($addon['id']) ? $addon['id'] : null,
                        'amount' => $addon['price'],
                        'quantity' => $addon['quantity'],
                        'type' => $addon['name'],
                        'provision' => config('fees.provision'),
                        'order_id' => $order->id,
                        'school_id' => $schoolId,
                    ];

                    if (!$student->first()) {
                        /** @var KKJTelegramBotService $bot */
                        $bot = resolve(KKJTelegramBotService::class);
                        $bot->log('add_package_participant_failed', compact('students'));

                        Log::error('addPackageParticipant failed', ['addon id' => $addon['local_id'], 'id' => $addon['id']]);
                    }

                    $this->addPackageParticipant($orderItem, $orderUser, $student->first());
                    //apply student loyalty program
                    if (in_array($addon['id'], StudentLoyaltyProgramService::PACKAGES_TO_APPLY)) {
                        $this->studentLoyaltyProgramService->createAddonBenefit($order, $addon, $student->first(), $orderUser);
                    }
                }

                $customAddons = $request->input('custom_addons', []);

                foreach ($customAddons as $customAddon) {
                    Log::info('storeOrder@customAddon: ', $customAddon);

                    $student = $students->filter(function ($student) use ($customAddon) {
                        return ($customAddon['local_id'] == $student['addonId'] || $customAddon['id'] == $student['addonId']);
                    });
                    Log::info('storeOrder@customAddon: ', $customAddon);

                    $orderItem = [
                        'external_id' => isset($customAddon['id']) ? $customAddon['id'] : null,
                        'amount' => $customAddon['price'],
                        'quantity' => $customAddon['quantity'],
                        'type' => $customAddon['name'],
                        'custom_addon_id' => $customAddon['local_id'],
                        'provision' => config('fees.provision'),
                        'order_id' => $order->id,
                        'school_id' => $schoolId,
                    ];


                    if (!$student->first()) {
                        /** @var KKJTelegramBotService $bot */
                        $bot = resolve(KKJTelegramBotService::class);
                        $bot->log('add_package_participant_fail', compact('students'));

                        Log::error('addPackageParticipant failed', ['addon id' => $customAddon['local_id'], 'id' => $customAddon['id']]);
                    }

                    $this->addPackageParticipant($orderItem, $orderUser, $student->first());
                }
            } catch (CourseFullException $e) {
                $order->cancel();
                $this->studentLoyaltyProgramService->removeBenefits($order);
                $order->refresh();

                return [$order, $e];
            } catch (\Exception $e) {
                $order->cancel();
                $this->studentLoyaltyProgramService->removeBenefits($order);
                $order->refresh();

                return [$order, $e];
            }

            $order->refresh();

            return [$order, null];
        });

        if ($e) {
            throw $e;
        }

        return $order;
    }

    /**
     * @param array $order
     * @param $student
     */
    private function addPackageParticipant(array $order, $payer, $student = null)
    {
        $addonBooking = $this->modelService->createModel(OrderItem::class, $order);
        $addonBooking->save();

        if ($student) {
            $participant = $this->modelService->createModel(AddonParticipant::class, $student);
            $participant->type = Participants::PARTICIPANT_STUDENT;
            $participant->booking()->associate($addonBooking);
            $participant->save();

            if (config('app.env') === 'production') {
                $this->ruleAPIService->addSubscriber($participant, 'student_addon', $payer);
            }
        }
    }

    public function storeGiftCardOrder(FormRequest $request, User $user, $giftCardTypeId)
    {
        $giftCardType = $this->giftCardTypes->getById($giftCardTypeId);
        $order = DB::transaction(function () use ($request, $user, $giftCardType) {
            //Create and save the order
            $order = $this->modelService->createModel(Order::class, [
                'external_order_id' => $request->input('external_order_id', null),
                'external_reservation_id' => $request->input('external_reservation_id', null),
                'user_id' => $user->id,
                'payment_method' => $request->input('payment_method', Payment::MANUAL_PAYMENT),
            ]);
            $order->save();

            $expires = new Carbon();
            $expires->addDay($giftCardType['valid_days']);
            $giftCard = $this->modelService->createModel(GiftCard::class, [
                'gift_card_type_id' => $giftCardType['id'],
                'remaining_balance' => $giftCardType['value'],
                'token' => str_random(),
                'buyer_id' => $user->id,
                'expires' => $expires,
            ]);

            $giftCard->save();

            /** @var GiftCardType $giftCardType */
            $orderItem = $this->modelService->createModel(OrderItem::class, [
                'external_id' => $request->input('external_id'),
                'amount' => $giftCardType['price'],
                'quantity' => 1,
                'type' => $giftCardType['name'] . ' - ' . $giftCardType['price'],
                'provision' => 0,
                'order_id' => $order->id,
                'gift_card_id' => $giftCard->id
            ]);

            $orderItem->save();

            return $order;
        });

        return $order;
    }

    /**
     * @param Order $order
     * @param array $orderItemIds
     * @throws \KlarnaException
     */
    public function setDeliveredItems(Order $order, $orderItemIds = [])
    {
        $sendToKlarna = [];

        $payedByKlarna = $order->payment_method === Payment::KLARNA ? 'Yes' : 'No';

        Log::info("Payment Method : {$order->payment_method}");
        Log::info("Order Payed by Klarna? {$payedByKlarna}");

        foreach ($order->items as $orderItem) {
            if (in_array($orderItem->id, $orderItemIds)) {
                if ($order->payment_method === Payment::KLARNA) {
                    $sendToKlarna[] = $orderItem;
                } else {
                    $orderItem->update(['delivered' => true]);
                }
            }
        }

        Log::info("Sending to Klarna for activation : " . json_encode($sendToKlarna));

        if (!empty($sendToKlarna)) {
            /** @var KlarnaService $klarnaService */
            $klarnaService = resolve(KlarnaService::class);
            $klarnaService->activateItems($order, $sendToKlarna);
        }
    }

    /**
     * @param Order $order
     * @return Order
     */
    public function cancelOrder(Order $order)
    {
        /**
         * @param Course|null $course
         * @param int|null $seats
         */
        $courseSeatUpdateFunc = function (Course $course, int $seats = 0) {
            if (in_array($course->vehicle_segment_id, \Jakten\Models\VehicleSegment::SHARED_COURSES) && $course->digital_shared) {
                $duplicateCourses = Course::query()
                    ->where('vehicle_segment_id', '=', $course->vehicle_segment_id)
                    ->where('start_time', '=', $course->start_time)
                    ->get();

                foreach ($duplicateCourses as $courseDuplicate) {
                    $courseDuplicate = $this->modelService->updateModel($courseDuplicate, ['seats' => $course->seats + $seats]);
                    $courseDuplicate->save();
                }
            } else {
                $course->update(['seats' => $course->seats + $seats]);
            }
        };

        $giftCardUpdateFunc = function ($giftCard, float $amount = 0.00) {
            $percentageGiftCardAdmin = in_array((int)$giftCard->gift_card_type_id, \Jakten\Models\GiftCardType::PERCENT_TYPES);
            if ($giftCard instanceof GiftCard && !$percentageGiftCardAdmin && !$giftCard->reusable) {
                $giftCard->updateBalance($amount);
            }
        };

        $order->items()
            ->each(
                function (OrderItem $orderItem)
                use ($courseSeatUpdateFunc, $giftCardUpdateFunc, &$courseSeats) {
                    $orderItem->isCourse() ?
                        $courseSeatUpdateFunc($orderItem->course, $orderItem->quantity) :
                        $orderItem->isGiftCard() ?
                            $giftCardUpdateFunc($orderItem->giftCard, abs($orderItem->amount)) : null;

                    $orderItem->cancel();
                });

        if ($order->items()->where('cancelled', '=', 1)->count() && $order->items()->count()) {
            $order->cancel();
            $order->refresh();
        }

        //rollback benefits
        $this->studentLoyaltyProgramService->removeBenefits($order);

        Log::info("(event) Raise new event", [
            "class" => __CLASS__,
            "event" => "OrderCancelled",
            "order" => ["id" => $order->id, "email" => $order->user->email]
        ]);

        event(new OrderCancelled($order));

        return $order;
    }

    /**
     * @param Order $order
     * @return Order
     */
    public function rebookOrder(Order $order)
    {
        /** @var KlarnaService $klarna */
        $klarna = resolve(KlarnaService::class);

        $itemsToRefund = new Collection();
        foreach ($order->items as $item) {
            if (!$item->isCourse() && !$item->isGiftCard() && !$item->packageParticipant()) {
                $itemsToRefund->push($item);
            }
        }

        $klarna->refund($order, $itemsToRefund);

        /**
         * @param Course|null $course
         * @param int|null $seats
         */
        $courseSeatUpdateFunc = function (Course $course, int $seats = 0) {
            if (in_array($course->vehicle_segment_id, \Jakten\Models\VehicleSegment::SHARED_COURSES) && $course->digital_shared) {
                $duplicateCourses = Course::query()
                    ->where('vehicle_segment_id', '=', $course->vehicle_segment_id)
                    ->where('start_time', '=', $course->start_time)
                    ->get();

                foreach ($duplicateCourses as $courseDuplicate) {
                    $courseDuplicate = $this->modelService->updateModel($courseDuplicate, ['seats' => $course->seats + $seats]);
                    $courseDuplicate->save();
                }
            } else {
                $course->update(['seats' => $course->seats + $seats]);
            }
        };

        foreach ($order->items as $item) {
            if ($item->isCourse()) {
                $courseSeatUpdateFunc($item->course, $item->quantity);
                $item->cancel();

                $order->user->amount += abs($item->amount);
            } elseif ($item->packageParticipant) {
                $item->cancel();

                $order->user->amount += abs($item->amount);
            } elseif ($item->isGiftCard()) {
                $item->cancel();

                //todo make sure there are no discount(%) gift cards
                $order->user->amount += $item->amount;
            }
            else {
                $item->cancel();
            }

            //check for teori discount
            if ($item->benefit_id) {//only discount type benefits are used here
                $benefit = Benefit::withTrashed()->where('benefit_type', StudentLoyaltyProgramService::BENEFIT_TYPES['discount'])->find($item->benefit_id);
                $discountAmount = $item->amount * $benefit->amount / 100;
                $order->user->amount -= $discountAmount;
            }
        }

        $order->user->amount += config('fees.booking_fee_to_kkj');
        $order->user->save();
        $order->update([
            'cancelled' => true,
            'rebooked' => true
        ]);

        //rollback benefits, update user amount if needed
        $this->studentLoyaltyProgramService->removeBenefits($order);

        event(new OrderRebooked($order));


        return $order;
    }

    /**
     * @param Order $order
     * @param FormRequest $request
     * @return Order
     * @throws \KlarnaException
     */
    public function updateOrder(Order $order, FormRequest $request)
    {
        $items = $request->input('items');
        $invoiceSent = $request->input('invoice_sent', false);
        return $this->setUpdateOrder($order, $items, $invoiceSent);
    }

    /**
     * @param Order $order
     * @param       $data
     * @throws \Exception
     * Update Order, self => Order Items => CourseParticipant | AddonParticipant
     */
    public function updateOrderDetails(Order $order, $data)
    {
        $courseParticipantUpdated = [];
        $addonsParticipantUpdated = [];
        $oldOrder = clone $order;
        $oldOrder->load('items');
        $schoolIdChanged = $order->school_id != $data['school_id'];
        $courseIdChanged = false;
        $newCourse = null;
        $oldCourseId = 0;

        /**
         * @param Course|null $course
         * @param int|null $seats
         */
        $courseSeatUpdateFunc = function (Course $course, int $seats = 0) {
            if (in_array($course->vehicle_segment_id, \Jakten\Models\VehicleSegment::SHARED_COURSES) && $course->digital_shared) {
                $duplicateCourses = Course::query()
                    ->where('vehicle_segment_id', '=', $course->vehicle_segment_id)
                    ->where('start_time', '=', $course->start_time)
                    ->get();

                foreach ($duplicateCourses as $courseDuplicate) {
                    $courseDuplicate = $this->modelService->updateModel($courseDuplicate, ['seats' => $course->seats + $seats]);
                    $courseDuplicate->save();
                }
            } else {
                $course->update(['seats' => $course->seats + $seats]);
            }
        };

        if (($orderItem = $order->items->where('course_id', '!=', null)->first()) && isset($data['course_id'])) {
            $oldCourseId = $orderItem->course_id;
            $courseIdChanged = $orderItem->course_id != $data['course_id'];
            $newCourse = Course::find($data['course_id']);
        }

        if (isset($data['addons'])) {
            foreach ($data['addons'] as $value) {
                $addonsParticipantUpdated[$value['id']] = [
                    'given_name' => explode(' ', $value['name'])[0],
                    'family_name' => explode(' ', $value['name'])[1],
                    'social_security_number' => $value['ssn'],
                    'email' => $value['email'],
                ];
            }
        }

        if (isset($data['partisipants'])) {
            foreach ($data['partisipants'] as $value) {
                $courseParticipantUpdated[$value['id']] = [
                    'given_name' => explode(' ', $value['name'])[0],
                    'family_name' => explode(' ', $value['name'])[1],
                    'social_security_number' => $value['ssn'],
                    'email' => $value['email'],
                    'course_id' => $data['course_id']
                ];
            }
        }

        DB::beginTransaction();
        foreach ($order->items as $orderItem) {
            if (isset($data['course_id'])) {
                if (!is_null($orderItem->course_id) && is_null($data['course_id'])) {
                    throw new \Exception('Please type valid course id');
                } elseif (is_null($orderItem->course_id) && !is_null($data['course_id'])) {
                    if ($schoolIdChanged) {
                        $orderItem->update(['school_id' => $data['school_id']]);
                    }
                } elseif ($courseIdChanged) {
                    $courseSeatUpdateFunc($orderItem->course, $orderItem->quantity);//increment old course seats
                    $updateData = [
                        'school_id' => $data['school_id'],
                        'course_id' => $data['course_id'],
                    ];
                    if ($newCourse) {
                        $updateData['type'] = $newCourse->segment->name;
                    }
                    $orderItem->update($updateData);
                }
            } else {
                $orderItem->update(['school_id' => $data['school_id']]);
            }
        }

        $order = $order->refresh();
        foreach ($order->items as $orderItem) {
            //decrement new course seats
            if (isset($data['course_id'])) {
                if ($orderItem->course && $orderItem->course->seats >= $orderItem->quantity && $orderItem->isCourse()) {
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
        if (count($courseParticipantUpdated)) {
            foreach ($courseParticipantUpdated as $key => $elem) {
                CourseParticipant::where('id', $key)->update($elem);
            }
        }
        if (count($addonsParticipantUpdated)) {
            foreach ($addonsParticipantUpdated as $key => $elem) {
                AddonParticipant::where('id', $key)->update($elem);
            }
        }
        if ($schoolIdChanged) {
            $order->update(['school_id' => $data['school_id']]);
        }
        DB::commit();

        Log::info("Order Details Updated", [
            "User Id" => auth()->user()->id,
            "User Email" => auth()->user()->email,
            "Old School Id" => $oldOrder->school_id,
            "New School Id" => $data['school_id'],
            "Old Course Id" => $oldCourseId,
            "New Course Id" => $data['course_id'] ?? 0,
            "old_data" => $oldOrder->toArray(),
            "new_data" => $order->toArray(),
        ]);

        if ($schoolIdChanged || $courseIdChanged) {
            event(new OrderCancelled($oldOrder));
            event(new NewOrder($order));
        }
    }


    /**
     * @param Order $order
     * @param       $items
     * @return Order
     * @throws \KlarnaException
     */
    public function updateOrderItems(Order $order, $items)
    {
        return $this->setUpdateOrder($order, $items, false);
    }

    /**
     * @param $orderItemId
     * @return String
     */
    public function deleteOrderItemWithParticipant($orderItemId)
    {
        try {
            $orderItem = OrderItem::where('id', $orderItemId)->first();
            $deleteRelationType = '';
            DB::beginTransaction();

            if (!is_null($orderItem->participant)) {
                $orderItem->participant()->delete();
                $deleteRelationType = ' with Course Participant';
            } elseif (!is_null($orderItem->packageParticipant)) {
                $orderItem->packageParticipant()->delete();
                $deleteRelationType = ' with Addon Participant';
            }
            $orderItem->delete();

            Log::info("Order Item Deleted", [
                'order item' => $orderItem->toArray(),
                "order" => [
                    "Order id" => $orderItem->order->id,
                    "User email" => auth()->user()->email,
                    "User id" => auth()->user()->id,
                ],
            ]);

            DB::commit();

            return $deleteRelationType;
        }
        catch (\Exception $e)
        {
            Log::info("Order Item Deleted:failed", [
                'order item' => $orderItem->toArray(),
                "order" => [
                    "Order id" => $orderItem->order->id,
                    "User email" => auth()->user()->email,
                    "User id" => auth()->user()->id,
                ],
                "exception" => $e->getMessage(),
                "exception_line" => $e->getLine(),
            ]);
            throw new \Exception('Something went wrong.');
        }

    }

    /**
     * @param Order $order
     * @param       $items
     * @param bool $invoiceSent
     * @return Order
     * @throws \KlarnaException
     */
    protected function setUpdateOrder(Order $order, $items, $invoiceSent = false)
    {
        $toDeliver = [];
        $toCancel = [];
        $toRefund = [];

        foreach ($items as $updatedItem) {
            $oldItem = $order->items->where('id', $updatedItem['id'])->first();

            if ($oldItem) {
                if ($updatedItem['delivered'] && !$oldItem->delivered) {
                    $toDeliver[] = $oldItem->id;
                } elseif ($updatedItem['cancelled'] && !$oldItem->cancelled) {
                    $toCancel[] = $oldItem->id;
                }

                if ($updatedItem['credited'] && !$oldItem->credited) {
                    $toRefund[] = $oldItem->id;
                }
            }
        }

        Log::info("Items to refund : " . json_encode($toRefund));

        $this->setDeliveredItems($order, $toDeliver);
        $this->refundItems($order, $toRefund);

        $allItemsHandled = true;
        foreach ($order->items as $orderItem) {
            if (!$orderItem->delivered && !$orderItem->credited) {
                $allItemsHandled = false;
            }
        }

        if ($allItemsHandled) {
            $order->update(['handled' => true]);
        }

        $order->update(['invoice_sent' => $invoiceSent]);

        return $order;
    }

    /**
     * @param Order $order
     * @param array $orderItemIds
     * @throws \KlarnaException
     */
    public function refundItems(Order $order, array $orderItemIds)
    {
        $sendToKlarna = [];

        foreach ($order->items as $orderItem) {
            if (in_array($orderItem->id, $orderItemIds)) {
                if ($order->payment_method === Payment::KLARNA && $orderItem->external_invoice_id) {
                    $sendToKlarna[] = $orderItem;
                } else {
                    if ($orderItem->isCourse()) {
                        $course = $orderItem->course;
                        $course->update(['seats' => $course->seats++]);
                    }

                    $orderItem->update(['credited' => true]);
                }
            }
        }

        if (!empty($sendToKlarna)) {
            /** @var KlarnaService $klarnaService */
            $klarnaService = resolve(KlarnaService::class);
            $klarnaService->refund($order, $sendToKlarna);
        }
    }
}
