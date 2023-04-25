<?php namespace Api\Http\Controllers;

use Illuminate\Http\Request;
use Jakten\Facades\Auth;
use Jakten\Models\Course;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\SchoolService;
use Api\Http\Requests\StoreRatingRequest;
use Jakten\Repositories\Contracts\{CourseRepositoryContract, RatingRepositoryContract, SchoolRepositoryContract};

/**
 * Class RatingController
 * @package Api\Http\Controllers
 */
class RatingController extends ApiController
{
    /**
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * @var SchoolService
     */
    private $schoolService;

    /**
     * @var RatingRepositoryContract
     */
    private $ratings;

    /**
     * @var CourseRepositoryContract
     */
    private $courses;

    /**
     * RatingController constructor.
     *
     * @param SchoolRepositoryContract $schools
     * @param SchoolService $schoolService
     * @param CourseRepositoryContract $courses
     * @param RatingRepositoryContract $ratings
     *
     * @param KKJTelegramBotService $botService
     * @internal param RatingService $ratingService
     */
    public function __construct(SchoolRepositoryContract $schools, SchoolService $schoolService, CourseRepositoryContract $courses, RatingRepositoryContract $ratings, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->schools = $schools;
        $this->courses = $courses;
        $this->schoolService = $schoolService;
        $this->ratings = $ratings;
    }

    /**
     * @param $schoolId
     * @param StoreRatingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rate($schoolId, StoreRatingRequest $request)
    {
        $school = $this->schools->query()->findOrFail($schoolId);
        $rating = $this->schoolService->rateSchool($school, $request);

        return $this->success($rating);
    }

    /**
     * @param $schoolId
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByUser($schoolId)
    {
        $school = $this->schools->query()->findOrFail($schoolId);
        $rating = Auth::user() ?
            $this->schoolService->findSchoolRatingByUser($school) :
            $this->schoolService->averageRating($school);

        return $this->success($rating);
    }

    /**
     * @param $schoolId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($schoolId)
    {
        $school = $this->schools->query()->findOrFail($schoolId);
        $rating = $this->ratings->ofSchool($school)->byUser(Auth::user())->query()->firstOrFail();
        $rating->delete();

        return $this->success();
    }
}
