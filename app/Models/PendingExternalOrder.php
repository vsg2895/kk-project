<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\{Collection, Facades\App};
use Jakten\Services\Payment\Klarna\KlarnaService;

/**
 * Class SchoolUsp
 *
 * @property User user
 * @property int user_id
 * @property int id
 * @property string external_order_id
 * @property array data
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class PendingExternalOrder extends Model
{
    //TODO: This whole models needs real relations instead of doing everything in a data column. No time now
    use SoftDeletes;

    /**
     * @var string $table
     */
    protected $table = 'pending_external_orders';

    /**
     * @var array $fillable
     */
    protected $fillable = ['external_order_id', 'reservation_id', 'data', 'user_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param $value
     */
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = json_encode($value);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @param Collection $courses
     * @param array $students
     * @param array $tutors
     * @param array $addons
     * @param array $customAddons
     * @param Collection|null $giftCardsToUse
     */
    public function setData(Collection $courses, array $students, array $tutors, array $addons, array $customAddons, Collection $giftCardsToUse = null)
    {
        $courseIds = $courses->map(function ($course) {
            return $course->id;
        })->toArray();

        $this->data = [
            'course_ids' => $courseIds,
            'students' => $students,
            'tutors' => $tutors,
            'addons' => $addons,
            'custom_addons' => $customAddons,
            'gift_cards' => $giftCardsToUse ? $giftCardsToUse->all() : null,
        ];
    }

    /**
     * @param $giftCardType
     */
    public function setGiftCardData($giftCardType)
    {
        $this->data = $giftCardType;
    }

    /**
     * @return mixed|null
     */
    public function getCourseIds()
    {
        if (isset($this->data['course_ids'])) {
            return $this->data['course_ids'];
        }
        return null;
    }

    /**
     * @return mixed|null
     */
    public function getAddons()
    {
        if (isset($this->data['addons'])) {
            return $this->data['addons'];
        }
        return null;
    }

    /**
     * @return mixed|null
     */
    public function getCustomAddons()
    {
        if (isset($this->data['course_ids'])) {
            return $this->data['course_ids'];
        }
        return null;
    }

    /**
     * @return mixed|null
     */
    public function getGiftCardTypeId()
    {
        if (isset($this->data['id'])) {
            return $this->data['id'];
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function getGiftCardsUsed()
    {
        if (isset($this->data['gift_cards'])){
            return $this->data['gift_cards'];
        }

        return null;
    }

    /**
     * @param $merchantId
     * @throws \Exception
     */
    public function deleteAndCancelKlarnaReservation($merchantId)
    {
        $reservationId = $this->reservation_id;
        if ($reservationId) {
            $organization = Organization::wherePaymentId($merchantId)->first();

            $klarnaService = App::make(KlarnaService::class);
            try {
                $klarnaService->cancelReservation($merchantId, $organization->payment_secret, $reservationId);
            } catch (\KlarnaException $exception) {
                $klarnaService->cancelReservation(config('klarna.kkj_payment_id'), config('klarna.kkj_payment_secret'), $reservationId);
            }

        }

        $this->delete();
    }
}
