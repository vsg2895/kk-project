<?php namespace Admin\Http\Controllers;

use Admin\Http\Requests\StoreOrganizationRequest;
use Admin\Http\Requests\UpdateOrganizationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Jakten\Repositories\Contracts\OrganizationRepositoryContract;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\OrganizationService;
use Shared\Http\Controllers\Controller;

/**
 * Class OrganizationController
 * @package Admin\Http\Controllers
 */
class OrganizationController extends Controller
{
    /**
     * @var OrganizationRepositoryContract
     */
    private $organizations;

    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * OrganizationController constructor.
     *
     * @param OrganizationRepositoryContract $organizations
     * @param OrganizationService $organizationService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(OrganizationRepositoryContract $organizations, OrganizationService $organizationService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->organizations = $organizations;
        $this->organizationService = $organizationService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $organizations = $this->organizations->search($request)->orderBy('created_at', 'DESC');

        return view('admin::organizations.index', [
            'organizations' => $organizations->select('organizations.*')->paginate()->appends(Input::except('page')),
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $organization = $this->organizations->query()->with('logo')->withTrashed()->findOrFail($id);
        return view('admin::organizations.show', [
            'organization' => $organization,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin::organizations.create');
    }

    /**
     * @param StoreOrganizationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreOrganizationRequest $request)
    {
        $organization = $this->organizationService->storeOrganization($request);

        $request->session()->flash('message', 'Organisation skapad!');

        return redirect()->route('admin::organizations.show', ['id' => $organization->id]);
    }

    /**
     * @param $id
     * @param UpdateOrganizationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, UpdateOrganizationRequest $request)
    {
        $organization = $this->organizations->query()->withTrashed()->findOrFail($id);
        $organization = $this->organizationService->updateOrganization($organization, $request);

        $request->session()->flash('message', 'Organisation uppdaterad!');

        return redirect()->route('admin::organizations.show', ['id' => $organization->id]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $organization = $this->organizations->query()->findOrFail($id);
        $this->organizationService->delete($organization);

        return redirect()->route('admin::organizations.index')->with('message', 'Organisation borttagen!');
    }
}
