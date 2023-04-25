<?php namespace Shared\Http\Controllers;

use Jakten\Events\BecomeTopPartnerApplication;
use Jakten\Models\PageUri;
use Jakten\Repositories\Contracts\CityRepositoryContract;
use Jakten\Repositories\Contracts\PageUriRepositoryContract;
use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\Schema\LocalBusinessService;
use Shared\Http\Requests\BecomeTopPartnerRequest;

/**
 * Class PageController
 * @package Shared\Http\Controllers
 */
class PageController extends Controller
{
    /**
     * @var PageUriRepositoryContract
     */
    private $uris;

    /**
     * @var CityRepositoryContract
     */
    private $cities;

    /**
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * @var LocalBusinessService
     */
    private $localBusiness;

    /**
     * PageController constructor.
     *
     * @param PageUriRepositoryContract $uris
     * @param CityRepositoryContract $cities
     * @param KKJTelegramBotService $botService
     */
    public function __construct(PageUriRepositoryContract $uris, CityRepositoryContract $cities,
                                KKJTelegramBotService $botService, SchoolRepositoryContract $schools, LocalBusinessService $localBusiness)
    {
        parent::__construct($botService);
        $this->uris = $uris;
        $this->cities = $cities;
        $this->schools = $schools;
        $this->localBusiness = $localBusiness;
    }

    public function getIframePage($school_id)
    {
        $school = $this->schools->query()->where('id',$school_id)->with('upcomingCourses', 'city','segments')->firstOrFail();
        $this->localBusiness->tryParse($school);
        $segments = $school->segments()->whereHas('availableCourses')->with('courses')->get();
        $title = $school->name . ' | ';

        return view('shared::iframe.index',[
            'title' => $title,
            'school' => $school,
            'segments' => $segments,
            'localBusiness' => $this->localBusiness->getLdJsonTag(),
        ]);
    }

    /**
     * @param $uri
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getPage($uri)
    {
        $uriEntity = $this->uris->byUri($uri)->first();
        if (!$uriEntity) {
            abort(404);
        }

        $page = $uriEntity->page;

        if ($uriEntity->status !== PageUri::ACTIVE) {
            $uriEntity = $page->uri;
            if (!$uriEntity) {
                abort(404);
            }

            return redirect()->route('shared::page.show', $uriEntity->uri);
        }

        return view('shared::pages.show', [
            'page' => $page,
        ]);
    }

    /**
     * @param string $subject
     * @param null $school
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contact($subject = 'false', $school = null)
    {
        switch ($subject) {
            case 'generella-fragor':
                $subject = 'ask-question';
                break;
            case 'bokning-fragor':
                $subject = 'booking-question';
                break;
            case 'rapportera':
                $subject = 'incorrect-price';
                break;
        }

        return view('shared::pages.contact', [
            'subject' => $subject,
            'school' => $school,
        ]);
    }

    public function intensiveCoursePage($city)
    {
        $city = $this->cities->bySlug($city)->first();

        return view('shared::pages.intensivkurser', ['city' => $city]);
    }

    public function topPartner()
    {
        return view('shared::pages.top-partner');
    }

    public function musicHelper()
    {
        return view('shared::pages.landing.music-helper');
    }

    public function becomeTopPartner(BecomeTopPartnerRequest $request)
    {
        try {
            event(new BecomeTopPartnerApplication($request->school_name, $request->school_email));

            return redirect()->back()->withMessage('Top Partner Application sent successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors( $exception->getMessage());
        }

    }
}
