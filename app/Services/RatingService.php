<?php namespace Jakten\Services;

use Illuminate\Foundation\Http\FormRequest;
use Jakten\Models\{School, SchoolRating, User};
use Jakten\Repositories\Contracts\RatingRepositoryContract;

/**
 * Class RatingService
 * @package Jakten\Services
 */
class RatingService
{
    /**
     * @var RatingRepositoryContract
     */
    private $ratings;

    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * RatingService constructor.
     *
     * @param RatingRepositoryContract $ratings
     * @param ModelService $modelService
     */
    public function __construct(RatingRepositoryContract $ratings, ModelService $modelService)
    {
        $this->ratings = $ratings;
        $this->modelService = $modelService;
    }

    /**
     * @param School $school
     * @param User $user
     * @param $rating
     * @return SchoolRating
     * @throws \Exception
     */
    public function rateSchool(School $school, User $user, $rating)
    {
        if ($this->ratingExist($school, $user)) {
            throw new \Exception('You have already rated this school');
        }
        $rating = new SchoolRating([
            'school_id' => $school->id,
            'user_id' => $user->id,
            'value' => $rating,
        ]);
        $rating->save();

        return $rating;
    }

    /**
     * @param School $school
     * @param User $user
     *
     * @return bool
     */
    public function ratingExist(School $school, User $user)
    {
        return $this->ratings->byUser($user)->ofSchool($school)->exists();
    }

    /**
     * @param SchoolRating $rating
     * @param FormRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model|SchoolRating
     */
    public function updateRating(SchoolRating $rating, FormRequest $request)
    {
        $rating = $this->modelService->updateModel($rating, $request->all());
        $rating->save();

        return $rating;
    }
}
