<?php namespace Jakten\Services;

use Illuminate\Support\{Collection, Facades\Log};
use Jakten\Models\{GiftCard, Order, OrderItem};
use Jakten\Repositories\Contracts\CourseRepositoryContract;

/**
 * Class GiftCardService
 * @package Jakten\Services
 */
class GiftCardService
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

    /**
     * UserService constructor.
     *
     * @param ModelService $modelService
     * @param CourseService $courseService
     * @param CourseRepositoryContract $courses
     */
    public function __construct(ModelService $modelService, CourseService $courseService, CourseRepositoryContract $courses)
    {
        $this->modelService = $modelService;
        $this->courseService = $courseService;
        $this->courses = $courses;
    }

    /**
     * @param Order $order
     * @param null $giftCardsUsed
     */
    public function consumeForOrder(Order $order, $giftCardsUsed = null)
    {
        if ($giftCardsUsed) {
            $toClaim = new Collection();
            foreach ($giftCardsUsed as $giftCardUsed) {

                $token = $giftCardUsed['token'];
                $balanceToUse = $giftCardUsed['balance_to_use'];

                $query = GiftCard::query()
                    ->where('token', $token)
                    ->where(function($query) use ($order) {
                        $query->whereNull('claimer_id')
                            ->orWhere('claimer_id', $order->user_id);
                    });

                if (!isset($giftCardUsed['gift_card_type_id']) || (!in_array((int)$giftCardUsed['gift_card_type_id'], \Jakten\Models\GiftCardType::PERCENT_TYPES))) {
                    $query->where('remaining_balance', '>=', $balanceToUse);
                }

                $giftCard = $query->first();

                if (!$giftCard) {
                    Log::error('Tried to use invalid gift card', [
                        'orderId' => $order->id,
                        'giftCardToken' => $token
                    ]);
                } else {

                    $percentageGiftCardAdmin = in_array((int)$giftCard->gift_card_type_id, \Jakten\Models\GiftCardType::PERCENT_TYPES);
                    if ($percentageGiftCardAdmin) {
                        $orderSum = $order->items->sum(
                            (function (OrderItem $orderItem) {
                                return $orderItem->amount * $orderItem->quantity;
                            }));

                        $balanceToUse = $orderSum * ($giftCard->remaining_balance/100);
                    }

                    if (!$percentageGiftCardAdmin && !$giftCard->reusable) {
                        $giftCard->remaining_balance = $giftCard->remaining_balance - $balanceToUse;
                        $giftCard->save();
                    }

                    $orderItem = new OrderItem([
                        'order_id' => $order->id,
                        'amount' => -$balanceToUse,
                        'quantity' => 1,
                        'name' => $percentageGiftCardAdmin ? 'Användning Doscountkort' : 'Användning Presentkort',
                        'type' => $percentageGiftCardAdmin ? 'Doscountkort' : 'Presentkort',
                        'gift_card_id' => $giftCard->id
                    ]);

                    $orderItem->save();

                    if (!$giftCard->claimer_id && !$percentageGiftCardAdmin && !$giftCard->reusable) {
                        $toClaim->push($giftCard);
                    }
                }
            }

            if ($toClaim->count()) {
                $order->user->giftCardsClaimed()->saveMany($toClaim);
            }

        }
    }
}
