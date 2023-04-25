<?php namespace Admin\Http\Controllers;

use Admin\Http\Requests\StoreSchoolRatingRequest;
use Carbon\Carbon;
use Jakten\Events\ReviewVerified;
use Jakten\Models\School;
use Jakten\Models\SchoolRating;
use Jakten\Models\User;
use Jakten\Repositories\Contracts\RatingRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Shared\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RatingController
 * @package Admin\Http\Controllers
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
        $ratings = $this->ratings->query()->orderBy('created_at', 'desc')->paginate();

        return view('admin::ratings.index', [
            'ratings' => $ratings,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $schools = School::all();
        $users = User::where('role_id', 1)->orderBy('email')->get();
        return view('admin::ratings.create', compact('schools', 'users'));
    }

    /**
     * @param StoreSchoolRatingRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreSchoolRatingRequest $request)
    {
        $rating = new SchoolRating($request->request->all());

        $rating->user()->associate(User::findOrFail((int)$request->request->get('user_id')));
        $rating->school()->associate(School::findOrFail((int)$request->request->get('school_id')));

        $rating->save();

        return redirect()->route('admin::ratings.index');
    }

    /**
     * @param SchoolRating $rating
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(SchoolRating $rating)
    {
        $schools = School::all();
        $users = User::where('role_id', 1)->orderBy('family_name')->get();

        return view('admin::ratings.edit', compact('rating', 'schools', 'users'));
    }

    /**
     * @param SchoolRating $rating
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SchoolRating $rating, Request $request)
    {
        $all = $request->request->all();
        $sendEvent = false;

        if (!$rating->verified && !$rating->school_notified) {
            $all['school_notified'] = Carbon::now();
            $sendEvent = true;
        }

        $rating->update($all);

        if ($sendEvent) {
            event(new ReviewVerified($rating));
        }

        return redirect()->route('admin::ratings.index');
    }

    /**
     * @param SchoolRating $rating
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(SchoolRating $rating)
    {
        $rating->delete();

        return redirect()->route('admin::ratings.index');
    }
}
