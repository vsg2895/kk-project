<?php namespace Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Shared\Http\Controllers\Controller;

/**
 * Class GiftCardController
 * @package Admin\Http\Controllers
 */
class GiftCardController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $increasedValue = floatval(Cache::get('GIFT_CARD_INCREASED_VALUE', 1));
        return view('admin::gift_cards.index', [
            'increasedValue' => $increasedValue
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $value = floatval($request->input('increasedValue'));
        Cache::forever('GIFT_CARD_INCREASED_VALUE', $value);
        return redirect(route('admin::gift_cards.index'));
    }
}
