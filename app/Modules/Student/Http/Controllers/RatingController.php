<?php namespace Student\Http\Controllers;

use Jakten\Facades\Auth;
use Jakten\Repositories\Contracts\RatingRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\RatingService;
use Shared\Http\Controllers\Controller;
use Student\Http\Requests\UpdateRatingRequest;

/**
 * Class RatingController
 * @package Student\Http\Controllers
 */
class RatingController extends Controller
{
    /**
     * @var RatingRepositoryContract
     */
    private $ratings;

    /**
     * @var RatingService
     */
    private $ratingService;

    /**
     * RatingController constructor.
     *
     * @param RatingRepositoryContract $ratings
     * @param RatingService $ratingService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(RatingRepositoryContract $ratings, RatingService $ratingService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->ratings = $ratings;
        $this->ratingService = $ratingService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $ratings = $this->ratings->byUser(Auth::user())->paginate();

        return view('student::ratings.index', [
            'ratings' => $ratings,
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        $rating = $this->ratings->query()->findOrFail($id);
        $this->authorize('view', $rating);

        return view('student::ratings.show', [
            'rating' => $rating,
        ]);
    }

    /**
     * @param $id
     * @param UpdateRatingRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update($id, UpdateRatingRequest $request)
    {
        $rating = $this->ratings->query()->findOrFail($id);
        $this->authorize('update', $rating);

        $this->ratingService->updateRating($rating, $request);

        return view('student::ratings.show', [
            'rating' => $rating,
            'message' => 'Bedömning uppdaterad!',
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete($id)
    {
        $rating = $this->ratings->query()->findOrFail($id);
        $this->authorize('delete', $rating);

        $rating->delete();

        return redirect()->route('student::ratings.index')->with('message', 'Bedömning borttagen!');
    }
}
