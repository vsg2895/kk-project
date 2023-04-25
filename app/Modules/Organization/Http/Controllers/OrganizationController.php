<?php namespace Organization\Http\Controllers;

use Jakten\Facades\Auth;
use Jakten\Services\KKJTelegramBotService;
use Jakten\Services\OrganizationService;
use Organization\Http\Requests\UpdateOrganizationRequest;
use Shared\Http\Controllers\Controller;

/**
 * Class OrganizationController
 * @package Organization\Http\Controllers
 */
class OrganizationController extends Controller
{
    /**
     * @var OrganizationService
     */
    private $organizationService;

    /**
     * OrganizationController constructor.
     *
     * @param OrganizationService $organizationService
     * @param KKJTelegramBotService $botService
     */
    public function __construct(OrganizationService $organizationService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->organizationService = $organizationService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        return view('organization::organization.show', [
            'organization' => Auth::user()->organization,
        ]);
    }

    /**
     * @param UpdateOrganizationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateOrganizationRequest $request)
    {
        $this->organizationService->updateOrganization(Auth::user()->organization, $request);

        $request->session()->flash('message', 'Organisation uppdaterad!');

        return redirect()->route('organization::organization.show');
    }
}
