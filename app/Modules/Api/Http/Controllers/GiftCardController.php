<?php namespace Api\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Jakten\Models\GiftCard;
use Jakten\Facades\Auth;
use Jakten\Models\Order;

/**
 * Class GiftCardController
 * @package Api\Http\Controllers
 */
class GiftCardController extends ApiController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(Request $request)
    {
        $token = $request->input('token');
        $courseId = $request->input('courseId');
        $hasMopedAm = $request->input('hasMopedAm');
        $giftCard = GiftCard::where('token', '=', $token)->where('expires', '>=', Carbon::now()->format('Y-m-d H:i:s'))->first();

        if (!$giftCard) {
            return $this->success($obj = (object)array('exists' => false));
        } elseif ($giftCard->claimed) {
            return $this->success($obj = (object)array('exists' => true, 'claimed' => true ));
        }
//        elseif (strtoupper($token) == 'TEORI20' && $courseId != 76518) {//customize by course
//            return $this->success($obj = (object)array('exists' => false, 'message' => 'Wrong course selected'));
//        }
        elseif (strtoupper($token) == 'MOPED100' && !$hasMopedAm) {//customize by segment
            return $this->success($obj = (object)array('exists' => false, 'message' => 'Wrong product selected'));
        }
        else {
            if (Auth::user()) {

                $isGiftCardUser = Order::query()
                    ->where('orders.cancelled', '=', 0)
                    ->where('orders.user_id', '=', Auth::user()->id)
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->where('order_items.gift_card_id', '=' , $giftCard->id)->get();

                if (count($isGiftCardUser)) {
                    return $this->success($obj = (object)array('exists' => true, 'claimed' => true ));
                }
            }
            return $this->success($obj = (object)array('exists' => true, 'claimed' => false, 'giftCard' => $giftCard ));
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function giftCardsForUser(Request $request)
    {
        $giftCards = Auth::user()->giftCardsClaimed()->get();
        return $this->success($giftCards);
    }
}
