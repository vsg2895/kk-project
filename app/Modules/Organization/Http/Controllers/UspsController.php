<?php namespace Organization\Http\Controllers;

use Admin\Http\Requests\UspsRequest;
use Illuminate\Http\Request;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\UspsService;
use Jakten\Repositories\Contracts\UspsRepositoryContract;
use Jakten\Models\SchoolUsps;
use Shared\Http\Controllers\Controller;

/**
 * Class UspsController
 * @package Organization\Http\Controllers
 */
class UspsController extends Controller
{
    /**
     * @var UspsRepositoryContract
     */
    private $uspsRepository;

    /**
     * @var UspsService
     */
    private $uspsService;

    /**
     * UspsController constructor.
     * @param UspsRepositoryContract $uspsRepository
     * @param UspsService $uspsService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(UspsRepositoryContract $uspsRepository, UspsService $uspsService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->uspsRepository = $uspsRepository;
        $this->uspsService = $uspsService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request)
    {
        $this->authorize('create', \Jakten\Models\SchoolUsps::class);
        return view('organization::usps.create', [
            'text' => $request->get('text', ''),
            'school' => $request->get('school', null)
        ]);
    }

    /**
     * @param UspsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(UspsRequest $request)
    {
        $this->authorize('create', \Jakten\Models\SchoolUsps::class);
        $usps = $this->uspsService->storeUsps($request);
        $request->session()->flash('message', 'Erbjudande/Fördel skapad!');
        // TODO: FIX: #usps parameter.
        return redirect()->route('organization::schools.show', ['id' => $usps->school_id . "#usps"]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete($id)
    {
        $usps = $this->uspsRepository->query()->findOrFail($id);
        $this->authorize('delete', $usps);
        $schoolId = $usps->school()->first()->id;
        $this->uspsRepository->delete($usps);
        return redirect()->route('organization::schools.show', ['id' => $schoolId])->with('message', 'Erbjudande/Fördel borttagen!');
    }

    /**
     * @param SchoolUsps $usps
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(SchoolUsps $usps)
    {
        return view('organization::usps.edit', [
            'usps' => $usps,
            'school' => $usps->school()->first()->id,
        ]);
    }

    /**
     * @param $id
     * @param UspsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update($id, UspsRequest $request)
    {
        $usps = $this->uspsRepository->query()->findOrFail($id);
        $this->authorize('update', $usps);

        $this->uspsService->updateUsps($usps, $request);

        $request->session()->flash('message', 'Erbjudande/Fördel uppdaterad!');

        return redirect()->route('organization::schools.show', ['id' => $usps->school_id . "#usps"]);

    }
}
