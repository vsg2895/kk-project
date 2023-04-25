<?php namespace Student\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Jakten\Facades\Auth;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\UserService;
use Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Jakten\Models\GiftCard;

/**
 * Class GiftCardController
 * @package Student\Http\Controllers
 */
class GiftCardController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * GiftCardController constructor.
     *
     * @param UserService $userService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(UserService $userService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->userService = $userService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $giftCards = $user->giftCardsClaimed()
            ->where('expires', '>=', date('Y-m-d 23:59:59'))
            ->get();

        return view('student::gift-cards.index', [
            'user' => $user,
            'type' => 'student',
            'giftCards' => $giftCards
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function claim(Request $request)
    {
        $redirectResponse = redirect()->back();
        $token = $request->input('token', false);

        if (!$token) {
            return $redirectResponse->withErrors(["Du har inte skrivit in någon presentkortskod."]);
        }

        /** @var GiftCard $giftCard */
        $giftCard = GiftCard::whereToken($token)
            ->first();

        if (!$giftCard) {
            $redirectResponse->withErrors(["Presentkort med koden [$token] finns inte."]);
        } else if ($giftCard->claimed && $giftCard->claimer_id || $giftCard->expired) {
            $redirectResponse->withErrors(["Presentkort med koden [$token] har redan lagts till."]);
        } elseif($giftCard->token !== 'promokkj' && $giftCard->token !== 'SKOLA50' && strtoupper($giftCard->token) !== 'MOPED100') {

            if ($giftCard->claim()) {
                Session::flash('message', 'Presentkortet har nu lagts till på ditt konto');
            }

        } else {
            $redirectResponse->withErrors(["Koden [$token] kan inte användas."]);
        }

        return $redirectResponse;
    }
}
