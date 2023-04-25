<?php

namespace Jakten\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Jakten\Models\School;
use Jakten\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return Collection
     */
    public function collection()
    {
        $startTime = request()->start_time . " 00:00:00";
        $endTime = request()->end_time . " 23:59:59";
        $includeParticipants = isset(request()->include_participants) && request()->include_participants === 'on';
        $segmentId = request()->segment_id !== 'all' ? request()->segment_id : 0;
        $users = new Collection();
        $courseQuery = '';
        $index = 0;

        //todo add "Gift card" column to show that order used gift card or not
        if (!$includeParticipants) {
            if ($segmentId) {
                $courseQuery = " JOIN courses c ON c.id = oi.course_id and c.vehicle_segment_id = $segmentId ";
            }
            $usersData = DB::select("SELECT users.*, SUM(oi.amount) as amount, cities.name as city
                                        FROM `users`
                                        JOIN orders o ON o.user_id = users.id
                                            AND o.created_at >= '$startTime'
                                            AND o.created_at <= '$endTime'
                                        JOIN order_items oi ON o.id = oi.order_id
                                        $courseQuery
                                        JOIN schools s ON s.id = o.school_id
                                        JOIN cities ON cities.id = s.city_id
                                        GROUP BY users.email");
            collect($usersData)
                ->each(function ($user) use (&$users, &$index) {
                    $index++;
                    $users->push([
                        $index,
                        $user->given_name,
                        $user->family_name,
                        $user->email,
                        $user->phone_number,
                        $user->city,
                        $user->amount,
                    ]);
                });
        } else {
            if ($segmentId) {
                $courseQuery = " JOIN courses c ON c.id = oi.course_id and c.vehicle_segment_id = $segmentId ";
            }
            $usersData = DB::select("SELECT cp.given_name, cp.family_name, cp.email, SUM(oi.amount) as amount, cities.name as city
                                    FROM `course_participants` cp
                                        JOIN order_items oi ON oi.id = cp.order_item_id
                                        JOIN orders o ON o.id = oi.order_id
                                            AND o.created_at >= '$startTime' 
                                            AND o.created_at <= '$endTime'
                                        JOIN schools s ON s.id = o.school_id
                                        JOIN cities ON cities.id = s.city_id
                                        $courseQuery
                                        GROUP BY cp.email");
            collect($usersData)
                ->each(function ($user) use (&$users, &$index) {
                    $index++;
                    $users->push([
                        $index,
                        $user->given_name,
                        $user->family_name,
                        $user->email,
                        $user->city,
                        $user->amount,
                    ]);
                });
        }

        return $users;
    }

    /**
     * @return array
     */
    public function headings(): array
    {

        return isset(request()->include_participants) && request()->include_participants === 'on' ? [
            '#', 'Name', 'Surname', 'Email', 'City', 'Amount'
        ] : [
            '#', 'Name', 'Surname', 'Email', 'Phone', 'City', 'Amount'
        ];
    }
}
