<?php namespace Api\Http\Controllers;

use Jakten\Facades\Auth;
use Jakten\Helpers\KlarnaError;
use Jakten\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Jakten\Repositories\SchoolRepository;
use Jakten\Services\KKJTelegramBotService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Jakten\Services\Payment\Klarna\{KlarnaService, KlarnaCheckoutOrder};
use Jakten\Repositories\Contracts\{CourseRepositoryContract, SchoolRepositoryContract};

/**
 * Class KlarnaController
 * @package Api\Http\Controllers
 */
class KlarnaController extends ApiController
{
    /**
     * @var SchoolRepository
     */
    private $schools;

    /**
     * @var CourseRepositoryContract
     */
    private $courses;

    /**
     * @var KlarnaService
     */
    private $klarnaService;

    /**
     * KlarnaController constructor.
     *
     * @param SchoolRepositoryContract $schools
     * @param CourseRepositoryContract $courses
     * @param KlarnaService $klarnaService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(SchoolRepositoryContract $schools, CourseRepositoryContract $courses, KlarnaService $klarnaService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->schools = $schools;
        $this->courses = $courses;
        $this->klarnaService = $klarnaService;
    }

    /**
     * @param $courseId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($courseId, Request $request)
    {
        $course = $this->courses->query()->with('school.organization')->findOrFail($courseId);
        $user = Auth::user();
        $addons = $request->input('addons', []);
        $students = $request->input('students', []);
        $tutors = $request->input('tutors', []);

        if ($course->school && $user) {
            $klarnaOrder = $this->klarnaService->createOrder($course->school, collect([$course]), $user, $students, $tutors, $addons);

            return $this->success([
                'id' => $klarnaOrder['id'],
                'snippet' => $klarnaOrder['html_snippet'],
            ]);
        } elseif ($courseId == 76518) {
            $klarnaOrder = $this->klarnaService->createOrder($course->school, collect([$course]), $user, $students, $tutors, $addons);

            return $this->success([
                'id' => $klarnaOrder['id'],
                'snippet' => $klarnaOrder['html_snippet'],
            ]);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @param $orderId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Klarna_Checkout_ApiErrorException
     */
    public function update($orderId, Request $request)
    {
        $user = Auth::user();
        $schoolId = $request->input('schoolId');

        if ($schoolId) {
            $school = School::whereId($schoolId)->with('organization')->first();
            $secret = config('klarna.kkj_payment_secret');

            if ($school) {
                $courseIds = $request->input('courseIds', []);
                $students = $request->input('students', []);
                $tutors = $request->input('tutors', []);
                $addons = $request->input('addons', []);
                $customAddons = $request->input('customAddons', []);
                $giftCardTokens = $request->input('giftCardTokens', []);

                $this->klarnaService->updateOrder($orderId, $secret, $courseIds, $user, $students, $tutors, $addons, $customAddons, $giftCardTokens);
                Log::debug('Order update: '. $orderId .', '. var_export($students, true) . var_export($tutors, true));
                return $this->success();
            } else {
                throw new NotFoundHttpException();
            }
        } elseif($request->has('giftCardType')) {
            $this->klarnaService->updateGiftCardOrder($orderId, $request->input('giftCardType'), $user);
            return $this->success();
        } else {
            abort(400);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function initiateOnboarding()
    {
        $user = Auth::user();
        $response = $this->klarnaService->nativeOnboarding($user->organization);

        if ($response instanceof KlarnaError) {
            return $this->error($response->get(), $response->statusCode);
        }

        $user->load('organization.schools');
        return $this->success($user->organization);
    }

    public function address() {
        return $this->success();
    }
}
