<?php

namespace Jakten\Exports;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Jakten\Models\Addon;
use Jakten\Models\GiftCard;
use Jakten\Models\VehicleSegment;
use Jakten\Repositories\Contracts\OrderItemRepositoryContract;
use Jakten\Services\StudentLoyaltyProgramService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment};

class MonthlyReportExport implements FromCollection, WithHeadings, ShouldAutoSize,WithEvents
{
    private $isDaily;
    private $groupBy;

    public function __construct()
    {
        $this->isDaily = request()->is_daily;
        $this->groupBy = $this->isDaily ? "" : "GROUP BY date";
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $startTime = request()->start_time . " 00:00:00";
        $endTime = request()->end_time . " 23:59:59";
        $excelData = new Collection();
        $addonsAll = Addon::query()->get();
        $addons = $addonsAll->whereNotIn('name', ['Testlektion', 'Körlektion x5', 'Körlektion x10'])
            ->pluck('name')->toArray();
        $addons = "'" . implode("','", $addons) . "'";
        $bookingFee = config('fees.booking_fee_to_kkj');
        $packages = "'Testlektion', 'Körlektion x5', 'Körlektion x10'";
        $bonusAmounts = array_unique(array_column(StudentLoyaltyProgramService::SEGMENT_BENEFITS, 'balance'));
        $bonusAmounts = "'" . implode("','", $bonusAmounts) . "'";

        $orderData = $this->getOrderData($packages, $addons, $addonsAll, $bookingFee, $startTime, $endTime, $bonusAmounts);
        $historicalTotalAmounts = $this->getHistoricalData($bookingFee, $startTime, $endTime);
        $rebookedData = $this->getRebookedCount($startTime, $endTime);
        $orderData = $this->getFormatted($orderData, $rebookedData, $historicalTotalAmounts);

        collect($orderData)
            ->each(function ($data, $key) use (&$excelData) {
                if ($key !== 'date') {
                    $row = [$key];
                    foreach ($data as $month => $value) {
                        $row[] = $value;
                    }

                    $excelData->push($row);
                }
            });

        return $excelData;

    }

    /**
     * @return array
     */
    public function headings(): array
    {
        if ($this->isDaily) {
            $months = ['Månad', request()->start_time . '/' . request()->end_time];
        } else {
            $months = [];
            foreach (CarbonPeriod::create(request()->start_time, '1 month', request()->end_time) as $month) {
                $months[$month->format('y-m')] = $month->format('M Y');
            }

            array_unshift($months, 'Månad');
        }

        return $months;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $columnCount = count($this->headings());
                $rowCount = count($this->collection()->toArray()) + 1;

                $bottomBorderStyleArray = [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '#131313'],
                        ],
                    ],
                ];

                $topBottomBorderStyleArray = [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '#131313'],
                        ],
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '#131313'],
                        ],
                    ],
                ];

                $cellHeadersRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellHeadersRange)->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('B1:W100')->getAlignment()->setHorizontal('right');

                for ($i = 0; $i <= $rowCount; $i++) {
                    if ($event->sheet->getDelegate()->getCellByColumnAndRow(1, $i)->getValue() === "Genomsnittligt_ordervärde"
                        || $event->sheet->getDelegate()->getCellByColumnAndRow(1, $i)->getValue() === "Summa_bokningar"
                        || $event->sheet->getDelegate()->getCellByColumnAndRow(1, $i)->getValue() === "Antal_fullföljda_ordrar") {

                        if ($event->sheet->getDelegate()->getCellByColumnAndRow(1, $i)->getValue() === "Antal_fullföljda_ordrar") {
                            $event->sheet->getDelegate()->getCellByColumnAndRow(1, $i)->getStyle()->getFont()->setBold(true);
                            for ($j = 2; $j <= $columnCount; $j++) {
                                $event->sheet->getDelegate()->getCellByColumnAndRow($j, $i)->getStyle()->getFont()->setBold(true);
                            }
                        } else {
                            $event->sheet->getDelegate()->getCellByColumnAndRow(1, $i)->getStyle()->applyFromArray($bottomBorderStyleArray);
                            $event->sheet->getDelegate()->getCellByColumnAndRow(1, $i)->getStyle()->getFont()->setBold(true);
                            for ($j = 2; $j <= $columnCount; $j++) {
                                $event->sheet->getDelegate()->getCellByColumnAndRow($j, $i)->getStyle()->applyFromArray($topBottomBorderStyleArray);
                                $event->sheet->getDelegate()->getCellByColumnAndRow($j, $i)->getStyle()->getFont()->setBold(true);
                            }
                        }
                    }

                    if ($event->sheet->getDelegate()->getCellByColumnAndRow(1, $i)->getValue() === "Antal_Paket") {
                        $event->sheet->getDelegate()->getCellByColumnAndRow(1, $i)->getStyle()->applyFromArray($bottomBorderStyleArray);
                        for ($j = 2; $j <= $columnCount; $j++) {
                            $event->sheet->getDelegate()->getCellByColumnAndRow($j, $i)->getStyle()->applyFromArray($bottomBorderStyleArray);
                        }
                    }
                }
            },
        ];
    }

    private function getFormatted($orderData, $rebookedData, $historicalTotalAmounts)
    {
        $formatted = [];
        foreach ($orderData as $data) {
            foreach ($data as $key => $value) {
                if ($key !== 'date' && $key !== 'Summa_bokningar' && $key !== 'Elever_per_bokning' ) {
                    $value = number_format($value, 0, false, ',');
                }
                $formatted[$key][$data->date] = in_array($key,VehicleSegment::MONTHLY_REPORTS_MONEY_TITLES) ? $value . ' kr' : $value;
                if ($key === 'Elever_per_bokning') {
                    $formatted['Antal_Ombokningar'][$data->date] = isset($rebookedData[$data->date]) && isset($rebookedData[$data->date][0])
                        ? $rebookedData[$data->date][0]->Antal_Ombokningar : 0;
                }
                if ($key === 'Summa_bokningar') {
                    $formatted[$key][$data->date] = number_format($value, 0, false, ',') . ' kr';

                    if (!$this->isDaily) {//add comparison
                        $lastMonth = Carbon::createFromFormat('y-m', $data->date)->subMonth()->format('y-m');
                        $lastYear = Carbon::createFromFormat('y-m', $data->date)->subYear()->format('y-m');
                        $lastMonthValue = collect($historicalTotalAmounts)->where('date', $lastMonth)->first()->Summa_bokningar;
                        $lastYearValue = collect($historicalTotalAmounts)->where('date', $lastYear)->first()->Summa_bokningar;
                        $lastMonthSumPercentage = round((($value / $lastMonthValue) * 100) - 100, 1);
                        $lastYearSumPercentage = round((($value / $lastYearValue) * 100) - 100, 1);
                        $formatted['Jämförelse föregående månad'][$data->date] = $lastMonthSumPercentage . ' %';
                        $formatted['Jämförelse föregående år'][$data->date] = $lastYearSumPercentage . ' %';
                    }
                }
            }
        }

        return $formatted;
    }

    private function getOrderData($packages, $addons, $addonsAll, $bookingFee, $startTime, $endTime, $bonusAmounts)
    {
        $addonsAll = $addonsAll->pluck('name')->toArray();
        $addonsAll = "'" . implode("','", $addonsAll) . "'";
        $reusableGiftCards = GiftCard::where('reusable', 1)->pluck('id')->toArray();
        $reusableGiftCards = implode(",", $reusableGiftCards);

        return DB::select("SELECT  DATE_FORMAT(o.created_at, '%y-%m') as date,
                COUNT(DISTINCT CASE WHEN oi.cancelled = false THEN o.id ELSE null END) as Antal_fullföljda_ordrar,
                COUNT(DISTINCT o.id) as Totalt_antal_ordrar,
                COUNT(CASE WHEN oi.cancelled = false THEN cp.id ELSE null END) + COUNT(CASE WHEN oi.cancelled = false THEN ap.id ELSE null END) as Antal_elever,
                ROUND((COUNT(CASE WHEN oi.cancelled = false THEN cp.id ELSE null END) + COUNT(CASE WHEN oi.cancelled = false THEN ap.id ELSE null END)) 
                / COUNT(DISTINCT CASE WHEN oi.cancelled = false THEN o.id ELSE null END), 2) as Elever_per_bokning,
                COUNT(DISTINCT CASE WHEN oi.cancelled = true THEN o.id ELSE null END) as Antal_avbokningar,
                ROUND(COUNT(DISTINCT CASE WHEN oi.cancelled = true THEN o.id ELSE null END) * 100 / COUNT(DISTINCT o.id), 2) as Avbokningsfrekvens,
                ROUND(SUM(IF(oi.cancelled = false, oi.amount * oi.quantity, 0)) / COUNT(DISTINCT CASE WHEN oi.cancelled = false THEN o.id ELSE null END), 2) as Genomsnittligt_ordervärde,
                
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'INTRODUCTION_CAR') THEN o.id ELSE null END) as Antal_Introduktionskurs,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'ONLINE_INTRIDUKTIONSUTBILDING') THEN o.id ELSE NULL END) as Antal_Digital_Introduktionskurs,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'ONLINE_INTRIDUKTIONSUTBILDING_ADMIN') THEN o.id ELSE NULL END) as Antal_Digital_Introduktionskurs_kkj,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'INTRODUKTIONSKURS_ENGLISH') THEN o.id ELSE NULL END) as Antal_Introduction_Course_English,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'RISK_ONE_CAR') THEN o.id ELSE NULL END) as Antal_Riskettan,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'RISK_ONE_ARABISKA_CAR') THEN o.id ELSE NULL END) as Antal_Riskettan_Arabiska,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'SPANISH_RISK_ONE') THEN o.id ELSE NULL END) as Antal_Riskettan_Spanish,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'ENGLISH_RISK_ONE') THEN o.id ELSE NULL END) as Antal_Engelska_Riskettan,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'RISK_ONE_MC') THEN o.id ELSE NULL END) as Antal_Riskettan_MC,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'RISK_TWO_CAR') THEN o.id ELSE NULL END) as Antal_Risktvåan,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'RISK_TWO_ARABISKA_CAR') THEN o.id ELSE NULL END) as Antal_Risktvåan_English,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'RISK_TWO_MC') THEN o.id ELSE NULL END) as Antal_Risktvåan_MC,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'RISK_ONE_TWO_COMBO') THEN o.id ELSE NULL END) as Antal_Risk_1_2_combo,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'RISK_ONE_TWO_COMBO_ENGLISH') THEN o.id ELSE NULL END) as Antal_Risk_1_2_combo_English,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'THEORY_LESSON_CAR') THEN o.id ELSE NULL END) as Antal_Körlektioner,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'ONLINE_LICENSE_THEORY') THEN o.id ELSE NULL END) as Antal_Körkortsteori_och_Testprov,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'MOPED_PACKAGE') THEN o.id ELSE NULL END) as Antal_Moped_AM,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'YKB_GRUNDKURS_140_H') THEN o.id ELSE NULL END) as Antal_YKB_Grundkurs_140_h,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND oi.type = 'YKB_FORTUTBILDNING_35_H') THEN o.id ELSE NULL END) as Antal_YKB_Fortutbildning_35_h,
                COUNT(DISTINCT CASE WHEN (o.cancelled = false AND (oi.type IN ($addonsAll) OR oi.custom_addon_id IS NOT NULL)) THEN o.id ELSE NULL END) as Antal_Paket,
                
                SUM(IF(o.cancelled = false AND oi.type = 'INTRODUCTION_CAR', oi.amount * oi.quantity, 0)) as Introduktionskurs,
                SUM(IF(o.cancelled = false AND oi.type = 'ONLINE_INTRIDUKTIONSUTBILDING', oi.amount * oi.quantity, 0)) as Digital_Introduktionskurs,
                SUM(IF(o.cancelled = false AND oi.type = 'ONLINE_INTRIDUKTIONSUTBILDING_ADMIN', oi.amount * oi.quantity, 0)) as Digital_Introduktionskurs_kkj,
                SUM(IF(o.cancelled = false AND oi.type = 'INTRODUKTIONSKURS_ENGLISH', oi.amount * oi.quantity, 0)) as Introduction_Course_English,
                SUM(IF(o.cancelled = false AND oi.type = 'RISK_ONE_CAR', oi.amount * oi.quantity, 0)) as Riskettan,
                SUM(IF(o.cancelled = false AND oi.type = 'RISK_ONE_ARABISKA_CAR', oi.amount * oi.quantity, 0)) as Riskettan_Arabiska,
                SUM(IF(o.cancelled = false AND oi.type = 'SPANISH_RISK_ONE', oi.amount * oi.quantity, 0)) as Riskettan_Spanish,
                SUM(IF(o.cancelled = false AND oi.type = 'ENGLISH_RISK_ONE', oi.amount * oi.quantity, 0)) as Engelska_Riskettan,
                SUM(IF(o.cancelled = false AND oi.type = 'RISK_ONE_MC', oi.amount * oi.quantity, 0)) as Riskettan_MC,
                SUM(IF(o.cancelled = false AND oi.type = 'RISK_TWO_CAR', oi.amount * oi.quantity, 0)) as Risktvåan,
                SUM(IF(o.cancelled = false AND oi.type = 'RISK_TWO_ARABISKA_CAR', oi.amount * oi.quantity, 0)) as Risktvåan_English,
                SUM(IF(o.cancelled = false AND oi.type = 'RISK_TWO_MC', oi.amount * oi.quantity, 0)) as Risktvåan_MC,
                SUM(IF(o.cancelled = false AND oi.type = 'RISK_ONE_TWO_COMBO', oi.amount * oi.quantity, 0)) as Risk_1_2_combo,
                SUM(IF(o.cancelled = false AND oi.type = 'RISK_ONE_TWO_COMBO_ENGLISH', oi.amount * oi.quantity, 0)) as Risk_1_2_combo_English,
                SUM(IF(o.cancelled = false AND oi.type = 'THEORY_LESSON_CAR', oi.amount * oi.quantity, 0)) as Körlektioner,
                SUM(IF(o.cancelled = false AND oi.type = 'ONLINE_LICENSE_THEORY', oi.amount * oi.quantity, 0)) as Körkortsteori_och_Testprov,
                SUM(IF(o.cancelled = false AND oi.type = 'MOPED_PACKAGE', oi.amount * oi.quantity, 0)) as Moped_AM,
                SUM(IF(o.cancelled = false AND oi.type = 'YKB_GRUNDKURS_140_H', oi.amount * oi.quantity, 0)) as YKB_Grundkurs_140_h,
                SUM(IF(o.cancelled = false AND oi.type = 'YKB_FORTUTBILDNING_35_H', oi.amount * oi.quantity, 0)) as YKB_Fortutbildning_35_h,
                SUM(IF(o.cancelled = false AND (oi.type IN ($packages) OR oi.custom_addon_id IS NOT NULL), oi.amount * oi.quantity, 0)) as Package,
                SUM(IF(o.cancelled = false AND oi.type IN ($addons), oi.amount * oi.quantity, 0)) as Övrigt,
                COUNT(DISTINCT o.id) * $bookingFee as Bokningsavgift,
                SUM(IF(oi.cancelled = false AND oi.gift_card_id IS NULL, oi.amount * oi.quantity, 0)) + COUNT(DISTINCT o.id) * $bookingFee as Summa_bokningar,
                
                SUM(IF(o.cancelled = false AND ABS(o.saldo_amount) IN ($bonusAmounts), o.saldo_amount, 0))
                - SUM(IF(o.cancelled = false AND oi.gift_card_id IN ($reusableGiftCards), gc.remaining_balance, 0)) as Bonus_saldo
            
            FROM orders o 
            RIGHT JOIN order_items as oi ON o.id = oi.order_id
            LEFT JOIN courses as c ON c.id = oi.course_id
            LEFT JOIN course_participants as cp ON oi.id = cp.order_item_id
            LEFT JOIN addon_participants as ap ON oi.id = ap.order_item_id
            LEFT JOIN gift_cards as gc ON oi.gift_card_id = gc.id
            LEFT JOIN vehicle_segments as vh ON vh.id = c.vehicle_segment_id
            WHERE o.created_at >= '$startTime' AND o.created_at <= '$endTime'
            AND o.rebooked = false
            $this->groupBy
        ");
    }

    private function getHistoricalData($bookingFee, $startTime, $endTime)
    {
        $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $startTime)->subYear()->toDateTimeString();
        return DB::select("SELECT  DATE_FORMAT(o.created_at, '%y-%m') as date,
                SUM(IF(oi.cancelled = false AND oi.gift_card_id IS NULL, oi.amount * oi.quantity, 0)) + COUNT(DISTINCT o.id) * $bookingFee as Summa_bokningar
            
            FROM orders o 
            RIGHT JOIN order_items as oi ON o.id = oi.order_id AND oi.amount > 0
            WHERE o.created_at >= '$startTime' AND o.created_at <= '$endTime'
            AND o.rebooked = false
            $this->groupBy
        ");
    }

    private function getRebookedCount($startTime, $endTime)
    {
        $rebookedData = DB::select("SELECT COUNT(o.id) as Antal_Ombokningar, DATE_FORMAT(o.created_at, '%y-%m') as date
            FROM orders o
            WHERE o.created_at >= '$startTime' AND o.created_at <= '$endTime'
            AND o.rebooked = true
            $this->groupBy
        ");

        return collect($rebookedData)->groupBy('date');
    }
}
