<?php namespace Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Jakten\Facades\Auth;
use Shared\Http\Controllers\Controller;

/**
 * Class SearchAlgorithmController
 * @package Admin\Http\Controllers
 */
class SearchAlgorithmController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data = DB::table('search_algorithm_config')
            ->select('*')
            ->latest('created_at')
            ->first();

        return view('admin::search_algorithm.index', [
            'data' => $data ?? []
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        if (((int)$request->get('courses') + (int)$request->get('sum_seats') + (int)$request->get('canceled') +
                (int)$request->get('conversion') + (int)$request->get('avg_price')) > 100) {
            $request->session()->flash('errors', 'There is more then 100% in sum.');
        } else {
            DB::table('search_algorithm_config')->insert(
                [
                    'courses' => $request->get('courses'),
                    'sum_seats' => $request->get('sum_seats'),
                    'canceled' => $request->get('canceled'),
                    'conversion' => $request->get('conversion'),
                    'rating' => $request->get('rating'),
                    'avg_price' => $request->get('avg_price'),
                    'user_id' => Auth::user()->id,
                ]
            );
        }

        return redirect(route('admin::search_algorithm.index'));
    }
}
