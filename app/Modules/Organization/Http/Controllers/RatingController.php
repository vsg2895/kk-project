<?php namespace Organization\Http\Controllers;

use Jakten\Facades\Auth;
use Jakten\Repositories\Contracts\RatingRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Shared\Http\Controllers\Controller;

/**
 * Class RatingController
 * @package Organization\Http\Controllers
 */
class RatingController extends Controller
{
    /**
     * @var RatingRepositoryContract
     */
    private $ratings;

    /**
     * RatingController constructor.
     *
     * @param RatingRepositoryContract $ratings
     * @param KKJTelegramBotService $botService
     */
    public function __construct(RatingRepositoryContract $ratings, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->ratings = $ratings;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $ratings = $this->ratings->withinOrganization(Auth::user()->organization)->query()->orderBy('created_at', 'desc')->paginate();

        return view('organization::ratings.index', [
            'ratings' => $ratings,
        ]);
    }
}
