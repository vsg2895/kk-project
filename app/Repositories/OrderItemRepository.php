<?php namespace Jakten\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Jakten\Models\{OrderItem, Organization, School, User};
use Jakten\Repositories\Contracts\OrderItemRepositoryContract;

/**
 * Class OrderItemRepository
 * @package Jakten\Repositories
 */
class OrderItemRepository extends BaseRepository implements OrderItemRepositoryContract
{
    /**
     * @var bool
     */
    protected $setIsCourseBooking = true;

    /**
     * @return Model
     */
    protected function model()
    {
        return OrderItem::class;
    }

    /**
     * @param School $school
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function forSchool(School $school)
    {
        $this->query()->whereIn('school_id', $school->id);

        return $this;
    }

    /**
     * @param Organization $organization
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function forOrganization(Organization $organization)
    {
        $this->query()->whereIn('school_id', $organization->schools->pluck('id')->all());

        return $this;
    }

    /**
     * @param User $user
     *
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function byUser(User $user)
    {
        $this->query()->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $user->id)
            ->select(['order_items.*']);

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function isCourseBooking()
    {
        if($this->setIsCourseBooking) {
            $this->query()->whereNotNull('course_id');
            $this->setIsCourseBooking = false;
        }

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function isGiftCard()
    {
        $this->query()->whereNotNull('gift_card_id');

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function active()
    {
        $this->query()->where('order_items.credited', false)->where('order_items.cancelled', false);

        return $this;
    }


    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function notDelivered()
    {
        $this->query()->where('delivered', false);

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function delivered()
    {
        $this->query()->where('delivered', true);

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function getCoursesSinceYesterday()
    {
        $this->isCourseBooking();
        $this->query()
            ->leftJoin('courses', 'courses.id', '=', 'order_items.course_id')
            ->where('courses.start_time', '<=', Carbon::now()->subHours(24))
            ->select(['order_items.*', 'courses.start_time as start_time']);
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function getCoursesSinceTomorrow()
    {
        $this->isCourseBooking();
        $this->query()
            ->leftJoin('courses', 'courses.id', '=', 'order_items.course_id')
            ->where('courses.start_time', '<=', Carbon::now()->addHours(24))
            ->select(['order_items.*', 'courses.start_time as start_time']);
        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|OrderItemRepositoryContract
     */
    public function hasExternalId()
    {
        $this->query()->whereNotNull('external_id');

        return $this;
    }

    /**
     * Function is used to get total amount for loyalty program for current year
     */
    public function getTotalAmountBySchoolCurrentYear($schoolId, $startMonth)
    {
        $startDate = Carbon::createFromFormat('m-d','01-01');
        $endDate = Carbon::createFromFormat('m-d', $startMonth);
        $check = Carbon::now()->between($startDate, $endDate);

        if ($check) {//between 01-01 to 05-01
            $prevYear = Carbon::now()->subYear()->format('Y');
            $filterDate = $prevYear . '-' . $startMonth;
        } else {//between 05-01 to 12-31
            $filterDate = date('Y') . '-' . $startMonth;
        }

        $this->query()->where('school_id', $schoolId)
            ->whereHas('order', function (Builder $q) {
                $q->where('cancelled', 0);
            })
            ->where('cancelled', 0)
            ->whereRaw('DATE(`created_at`) >= ?', [$filterDate]);

        return $this;
    }

    public function getTotalAmountBySchoolPrevYear($schoolId, $startMonth)
    {
        $startDate = Carbon::createFromFormat('m-d','01-01');
        $endDate = Carbon::createFromFormat('m-d', $startMonth);
        $check = Carbon::now()->between($startDate, $endDate);

        if ($check) {//between 01-01 to 05-01
            $prevYear = Carbon::now()->subYears(2)->format('Y');
            $currentYear = Carbon::now()->subYear()->format('Y');
        } else {//between 05-01 to 12-31
            $prevYear = Carbon::now()->subYear()->format('Y');
            $currentYear = Carbon::now()->format('Y');
        }

        $this->reset()->query()->where('school_id', $schoolId)
            ->whereHas('order', function (Builder $q) {
                $q->where('cancelled', 0);
            })
            ->where('cancelled', 0)
            ->whereRaw('DATE(`created_at`) < ?', [$currentYear . '-' . $startMonth])
            ->whereRaw('DATE(`created_at`) >= ?', [$prevYear . '-' . $startMonth]);

        return $this;
    }
}
