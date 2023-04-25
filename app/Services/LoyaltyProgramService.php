<?php

namespace Jakten\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jakten\Models\OrderItem;
use Jakten\Repositories\Contracts\OrderItemRepositoryContract;

class LoyaltyProgramService
{
    public $startFrom = '05-01';//1 of may

    //in reporting queries the amounts are hardcoded
    public $loyaltyLevels = [
        'bronze' => [
            'label' => 'Brons',
            'start_revenue' => 0,
            'end_revenue' => 250000,
            'discount' => 0,//%
            'description' => 'Alla fördelar med Körkortsjakten!',
        ],
        'silver' => [
            'label' => 'Silver',
            'start_revenue' => 250000,
            'end_revenue' => 500000,
            'discount' => 1,//%
            'description' => 'Silvermedalj diplom utskickat med ram. <br><br> 2 st biobiljetter. <br><br> 1 % rabatt på provisionsavgiften',
        ],
        'gold' => [
            'label' => 'Guld',
            'start_revenue' => 500000,
            'end_revenue' => 750000,
            'discount' => 1.5,//%
            'description' => 'Guldmedalj diplom utskickat med ram. <br><br> Presentkort Åhléns värde 800:-. <br><br> 1,5 % rabatt på provisionsavgiften',
        ],
        'diamond' => [
            'label' => 'Diamant',
            'start_revenue' => 750000,
            'end_revenue' => 2000000,
            'discount' => 2,//%
            'description' => 'Diamantmedalj diplom utskickat med ram. <br><br> Presentkort hotellövernattning värde 1200:-. <br><br> 2 % rabatt på provisionsavgiften',
        ],
    ];

    /**
     * @var OrderItemRepositoryContract
     */
    private $orderItemModel;

    /**
     * LoyaltyProgramService constructor.
     *
     * For more information about Loyalty Program visit:
     * https://www.notion.so/exsys/Loyalty-program-a7e4247f6d8f46fcba65dbe9d72a0cd9
     */
    public function __construct(OrderItemRepositoryContract $orderItem)
    {
        $this->orderItemModel = $orderItem;
    }

    private function getLoyaltyLevel($amount)
    {
        $loyaltyLevel = 'bronze';

        foreach ($this->loyaltyLevels as $level => $data) {
            if ($amount >= $data['start_revenue'] && $amount < $data['end_revenue']) {

                $loyaltyLevel = $level;
                break;
            } elseif ($amount >= $this->loyaltyLevels['diamond']['start_revenue']) {
                $loyaltyLevel = 'diamond';
                break;
            }
        }

        return $loyaltyLevel;
    }

    public function getLoyaltyLevelBasedOnSchool($school)
    {
        //todo:later get current year and previous year with one query
        // check if item exists, after it get ->total_amount
        $totalAmount = $this->orderItemModel->getTotalAmountBySchoolCurrentYear($school->id, $this->startFrom)
            ->select('*', DB::raw('SUM(amount * quantity) as total_amount'))->first()->total_amount;

        $totalAmountPrevYear = $this->orderItemModel->getTotalAmountBySchoolPrevYear($school->id, $this->startFrom)
            ->select('*', DB::raw('SUM(amount * quantity) as total_amount'))->first()->total_amount;

        $totalAmount = $totalAmount ?: 0;
        $totalAmountPrevYear = $totalAmountPrevYear ?: 0;

        //add 25000 to total amount, if the school was diamond level for previous year
        if ($totalAmountPrevYear >= $this->loyaltyLevels['diamond']['start_revenue']) {
            $totalAmount += 25000;
        }

        //add fixed amount if exist (loyalty_fixed_amount default = 0)
        $totalAmount += $school->loyalty_fixed_amount;

        //if total amount is higher, set it to 1m to avoid frontend issues
        $totalAmount = $totalAmount > $this->loyaltyLevels['diamond']['end_revenue']
            ? $this->loyaltyLevels['diamond']['end_revenue']
            : $totalAmount;

        $loyaltyLevel = $this->getLoyaltyLevel($totalAmount);

        return [
            'total_amount' => $totalAmount,
            'loyalty_level' => $loyaltyLevel,
        ];
    }

    public function getCurrentYearDate()
    {
        $startDate = Carbon::createFromFormat('m-d','01-01');
        $endDate = Carbon::createFromFormat('m-d', $this->startFrom);
        $check = Carbon::now()->between($startDate, $endDate);

        if ($check) {//between 01-01 to 05-01
            return Carbon::now()->subYear()->format('Y') . '-' .$this->startFrom;
        } else {//between 05-01 to 12-31
            return date('Y') . '-' .$this->startFrom;
        }
    }

    public function getPreviousYearDate()
    {
        $startDate = Carbon::createFromFormat('m-d','01-01');
        $endDate = Carbon::createFromFormat('m-d', $this->startFrom);
        $check = Carbon::now()->between($startDate, $endDate);

        if ($check) {//between 01-01 to 05-01
            return Carbon::now()->subYears(2)->format('Y') . '-' .$this->startFrom;
        } else {//between 05-01 to 12-31
            return Carbon::now()->subYear()->format('Y') . '-' .$this->startFrom;
        }
    }
}
