<?php namespace Shared\Http\Controllers;

use DB;
use Jakten\Facades\Auth;
use Illuminate\Http\Request;
use Jakten\Models\PendingExternalOrder;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\Payment\Klarna\KlarnaService;
use Illuminate\Support\Facades\Cache;

/**
 * Class GiftCardController
 *
 * @package Shared\Http\Controllers
 */
class GiftCardController extends Controller
{
    /**
     * @var KlarnaService $klarnaService
     */
    private $klarnaService;

    /**
     * GiftCardController constructor.
     *
     * @param KlarnaService $klarnaService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(KlarnaService $klarnaService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->klarnaService = $klarnaService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->session()->get('klarna_order_id')) {
            $request->session()->forget('klarna_order_id');
            $request->session()->forget('merchant_id');
        }

        $klarnaOrder = $this->klarnaService->createGiftCardOrder(Auth::user());

        $bonus = (int)round(
            100 * ((float)Cache::get('GIFT_CARD_INCREASED_VALUE', 1) - 1)
        );

        return view('shared::gift_card.index', [
            'klarnaOrder' => $klarnaOrder,
            'bonus'       => $bonus,
        ]);
    }
}
