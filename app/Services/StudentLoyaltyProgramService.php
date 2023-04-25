<?php

namespace Jakten\Services;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Jakten\Models\Benefit;
use Jakten\Models\Course;
use Jakten\Models\Order;
use Jakten\Models\User;

/**
 * Program to give some gifts to users, when ordering specific courses or packages.
 * Balance(50kr, 175kr) benefits used to add some amount to user balance
 * and being applied once the course is finished(except theory)
 * Discount(10%) benefits are being applied during an order and defined for specific courses (currently only for theory)
 */
class StudentLoyaltyProgramService
{
    /**
     * @var User
     */
    private $userModel;

    /**
     * @var Benefit
     */
    private $benefitModel;

    public function __construct(User $userModel, Benefit $benefitModel)
    {
        $this->benefitModel = $benefitModel;
        $this->userModel = $userModel;
    }

    const START_DATE = '2022-10-21 13:00:00';

    /**
     * Packages for student loyalty program (table: addons)
     */
    const PACKAGES_TO_APPLY = [1, 3, 4, 5, 6, 8];

    const PACKAGE_BENEFIT = [
        'balance' => 75,
        'discount' => 10,
        'beneficiary_segment_ids' => self::BENEFICIARY_SEGMENT_IDS,
    ];

    /**
     * Segments, that can apply discounts
     */
    const BENEFICIARY_SEGMENT_IDS = [32];

    /**
     * Courses, that benefit should be applied immediately and not wait till course is finished, as
     * there is no course time
     * Currently only theory courses
     */
    const APPLY_IMMEDIATELY = [32];

    const APPLY_AFTER_COURSE = [6, 7, 13, 26];

    const BENEFIT_TYPES = ['balance' => 'balance', 'discount' => 'discount'];

    const GARAGES = [7, 0, 6, 13, 32];

    /**
     * Courses for student loyalty program (table: vehicle_segments)
     *
     * 6:RISK_ONE_CAR(Riskettan), 7:INTRODUCTION_CAR(c), 13:RISK_TWO_CAR(Risktvåan),
     * 16:THEORY_LESSON_CAR(Körlektioner), 26:RISK_ONE_TWO_COMBO(Risk 1&2 combo),
     * 32:ONLINE_LICENSE_THEORY(Körkortsteori och Testprov)
     */
    const SEGMENT_BENEFITS = [
        6 => [
            'balance' => 75,
            'discount' => 10,
            'beneficiary_segment_ids' => self::BENEFICIARY_SEGMENT_IDS,
            'name' => 'Riskettan',
            'url' => '/riskettan',
        ],
        7 => [
            'balance' => 50,
            'discount' => null,
            'beneficiary_segment_ids' => [],
            'name' => 'Introduktionskurs',
            'url' => '/introduktionskurser',
        ],
        13 => [
            'balance' => 100,
            'discount' => 10,
            'beneficiary_segment_ids' => self::BENEFICIARY_SEGMENT_IDS,
            'name' => 'Risktvåan',
            'url' => '/risktvaan',
        ],
        16 => [
            'balance' => 50,
            'discount' => null,
            'beneficiary_segment_ids' => [],
            'name' => 'Körlektioner',
            'url' => '/korlektion',
        ],
        26 => [
            'balance' => 175,
            'discount' => 10,
            'beneficiary_segment_ids' => self::BENEFICIARY_SEGMENT_IDS,
            'name' => 'Risk 1&2 combo',
            'url' => '/kurser/risk1+2',
        ],
        32 => [
            'balance' => 50,
            'discount' => null,
            'beneficiary_segment_ids' => [],
            'name' => 'Körkortsteori',
            'url' => '/teoriprov-online',
        ],
    ];

    /**
     * Create benefit if needed
     */
    public function createCourseBenefit(Order $order, Course $course, FormRequest $request, User $orderUser)
    {
        Log::info('createCourseBenefit:createBenefit start');
        $students = (new Collection($request->input('students', [])))->filter(function ($student) use ($course) {
            return $course->id === $student['courseId'];
        });
        $vehicleSegmentId = $course->vehicle_segment_id;
        $benefitToAdd = self::SEGMENT_BENEFITS[$vehicleSegmentId];
        $students->push($orderUser);

        foreach ($students as $student) {
            $user = $this->userModel->where('email', $student['email'])->first();

            //create benefits
            //create discounts for selected segments
            if (count($benefitToAdd['beneficiary_segment_ids']) && $benefitToAdd['discount']) {
                $existingDiscountBenefits = $this->benefitModel->where('benefit_type', self::BENEFIT_TYPES['discount'])
                    ->where('user_id', $user->id)->whereIn('beneficiary_segment_id', self::BENEFICIARY_SEGMENT_IDS)->get();

                foreach ($benefitToAdd['beneficiary_segment_ids'] as $benSegmentId) {
                    $benefitExist = $existingDiscountBenefits->where('beneficiary_segment_id', $benSegmentId)->first();
                    if (!$benefitExist) {
                        $this->benefitModel->create([
                            'benefit_type' => self::BENEFIT_TYPES['discount'],
                            'amount' => $benefitToAdd['discount'],
                            'claimed' => false,
                            'applied' => false,
                            'user_id' => $user->id,
                            'order_id' => $order->id,
                            'vehicle_segment_id' => $vehicleSegmentId,
                            'beneficiary_segment_id' => $benSegmentId,
                            'addon_id' => null,
                            'custom_addon_id' => null,
                        ]);
                    }
                }
            }
            //create balance benefit
            if ($benefitToAdd['balance']) {
                if (in_array($vehicleSegmentId, [6, 13])) {//if risk or risktvan, check also risk1+2 combo doesn't exist
                    $vehicleSegmentIds = [$vehicleSegmentId, 26];
                    $benefitExist = $this->benefitModel->where('benefit_type', self::BENEFIT_TYPES['balance'])
                        ->where('user_id', $user->id)->whereIn('vehicle_segment_id', $vehicleSegmentIds)->first();
                } else {
                    $benefitExist = $this->benefitModel->where('benefit_type', self::BENEFIT_TYPES['balance'])
                        ->where('user_id', $user->id)->where('vehicle_segment_id', $vehicleSegmentId)->first();
                }
                if (!$benefitExist) {
                    $applyImmediately = in_array($course->vehicle_segment_id, self::APPLY_IMMEDIATELY);
                    $this->benefitModel->create([
                        'benefit_type' => self::BENEFIT_TYPES['balance'],
                        'amount' => $benefitToAdd['balance'],
                        'claimed' => $applyImmediately,
                        'applied' => $applyImmediately,
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'vehicle_segment_id' => $vehicleSegmentId,
                        'beneficiary_segment_id' => null,
                        'addon_id' => null,
                        'custom_addon_id' => null,
                    ]);
                    if ($applyImmediately) {
                        $user->amount += $benefitToAdd['balance'];
                        $user->save();
                    }
                }
            }
        }
    }

    public function createAddonBenefit(Order $order, $addon, $student, $orderUser)
    {
        $benefitToAdd = self::PACKAGE_BENEFIT;
        $students = collect([$student, $orderUser]);
        //gettype($student) == object
        //gettype($orderUser) == array
        foreach ($students as $student) {
            $user = $this->userModel->where('email', $student['email'])->first();

            if (count($benefitToAdd['beneficiary_segment_ids']) && $benefitToAdd['discount']) {
                $existingDiscountBenefits = $this->benefitModel->where('benefit_type', self::BENEFIT_TYPES['discount'])
                    ->where('user_id', $user->id)->whereIn('beneficiary_segment_id', self::BENEFICIARY_SEGMENT_IDS)->get();

                foreach ($benefitToAdd['beneficiary_segment_ids'] as $benSegmentId) {
                    Log::info('createAddonBenefit:$benSegmentId ' . $benSegmentId);

                    $benefitExist = $existingDiscountBenefits->where('beneficiary_segment_id', $benSegmentId)->first();
                    if (!$benefitExist) {
                        $this->benefitModel->create([
                            'benefit_type' => self::BENEFIT_TYPES['discount'],
                            'amount' => $benefitToAdd['discount'],
                            'claimed' => false,
                            'applied' => true,//ready to use
                            'user_id' => $user->id,
                            'order_id' => $order->id,
                            'vehicle_segment_id' => null,
                            'beneficiary_segment_id' => $benSegmentId,
                            'addon_id' => $addon['local_id'],
                            'custom_addon_id' => null,
                        ]);
                    }
                }

                //addon balance benefits should be claimed immediately, because they don't have time for start and finish
                if ($benefitToAdd['balance']) {
                    $benefitExist = $this->benefitModel->where('benefit_type', self::BENEFIT_TYPES['balance'])
                        ->where('user_id', $user->id)->whereNotNull('addon_id')->first();
                    if (!$benefitExist) {
                        $this->benefitModel->create([
                            'benefit_type' => self::BENEFIT_TYPES['balance'],
                            'amount' => $benefitToAdd['balance'],
                            'claimed' => true,
                            'applied' => true,//ready to use
                            'user_id' => $user->id,
                            'order_id' => $order->id,
                            'vehicle_segment_id' => null,
                            'beneficiary_segment_id' => null,
                            'addon_id' => $addon['local_id'],
                            'custom_addon_id' => null,
                        ]);
                        $user->amount += $benefitToAdd['balance'];
                        $user->save();
                    }
                }
            }
        }
    }

    public static function claimBalanceBenefit(Course $course, $email)
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            $benefit = Benefit::where('benefit_type', self::BENEFIT_TYPES['balance'])
                ->where('claimed', false)
                ->where('applied', true)
                ->where('user_id', $user->id)->where('vehicle_segment_id', $course->vehicle_segment_id)->first();

            if ($benefit) {//claim balance benefit, as discounts are claimed during order
                $user->amount += $benefit['amount'];
                $user->save();
                $benefit->claimed = true;
                $benefit->save();

                Log::info('Balance benefit claimed for user with id: ' . $user->id . '. Course id: ' . $course->id);
            }
        }
    }

    public function claimDiscountBenefit(Course $course, User $orderUser)
    {
        $benefit = Benefit::where('benefit_type', self::BENEFIT_TYPES['discount'])
            ->where('claimed', false)
            ->where('applied', true)
            ->where('user_id', $orderUser->id)
            ->where('beneficiary_segment_id', $course->vehicle_segment_id)->first();

        if ($benefit) {
            $benefit->update(['claimed' => true]);
            Log::info('Discount benefit claimed for user with id: ' . $orderUser->id . '. Course id: ' . $course->id);
            return $benefit->id;
        } else {
            return $benefit;
        }
    }

    public static function applyBenefits(Course $course, $email)
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            Benefit::where('user_id', $user->id)->where('vehicle_segment_id', $course->vehicle_segment_id)
                ->update([
                    'applied' => true,
                ]);

            Log::info('Benefits applied for user with id: ' . $user->id . '. Course id: ' . $course->id);
        }
    }

    public function checkTheoryDiscount($user, $course)
    {
        return Benefit::where('benefit_type', self::BENEFIT_TYPES['discount'])
            ->where('claimed', false)
            ->where('user_id', $user->id)
            ->where('beneficiary_segment_id', $course->vehicle_segment_id)->first();
    }

    public function removeBenefits($order)
    {
        $benefits = Benefit::where('order_id', $order->id)->get();

        if ($benefits) {
            $users = $this->userModel->whereIn('id', $benefits->pluck('id')->toArray())->get();
            foreach ($benefits as $benefit) {
                $user = $users->where('id', $benefit->user_id)->first();
                if ($benefit->vehicle_segment_id) {//course
                    if (in_array($benefit->vehicle_segment_id, self::APPLY_IMMEDIATELY)) {//theory
                        $claimedBalance = self::SEGMENT_BENEFITS[$benefit->vehicle_segment_id]['balance'];
                        if ($claimedBalance && $user->amount >= $claimedBalance) {
                            $user->amount -= $claimedBalance;
                            $user->save();
                        }
                    }
                } else {//addon(package)
                    $claimedBalance = self::PACKAGE_BENEFIT['balance'];
                    if ($claimedBalance && $user->amount >= $claimedBalance) {
                        $user->amount -= $claimedBalance;
                        $user->save();
                    }
                }
                //otherwise is course, do nothing, because the benefits would be applied after course is finished

                //delete benefit
                $benefit->delete();
            }
        }
    }
}
