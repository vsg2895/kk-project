<?php namespace Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Jakten\Http\Requests\PartnerRequest;
use Jakten\Models\Asset;
use Jakten\Models\Partner;
use Jakten\Repositories\Contracts\PartnerRepositoryContract;
use Jakten\Services\Asset\AssetService;
use Jakten\Services\Asset\Strategy\ImageGallery;
use Jakten\Services\Asset\Strategy\ImagePartner;
use Jakten\Services\Asset\Strategy\ImagePost;
use Jakten\Services\KKJTelegramBotService;
use Shared\Http\Controllers\Controller;

/**
 * Class CourseController
 *
 * @property PartnerRepositoryContract partners
 * @property AssetService assetService
 * @package Admin\Http\Controllers
 */
class PartnerController extends Controller
{

    public function __construct(PartnerRepositoryContract $partners, AssetService $assetService, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->partners = $partners;
        $this->assetService = $assetService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $partners = $this->partners
            ->order('desc', 'id')
            ->paginate($request);

        return view('admin::partners.index', compact('partners'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin::partners.create');
    }

    /**
     * @param PartnerRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function store(PartnerRequest $request)
    {
        $partnerData = $request->only(['partner', 'short_description', 'url', 'active']);
        $partner = $this->partners->create($partnerData);

        if ($partner instanceof Partner) {

            if ($this->uploadPartnersImage($request, $partner)) {
                return redirect()->route('admin::partners.index');
            }
        }

        return redirect()->back()->with($request->post());
    }

    /**
     * @param Partner $partner
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Partner $partner)
    {
        return view('admin::partners.edit', compact('partner'));
    }

    /**
     * @param Request $request
     * @param Partner $partner
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Partner $partner)
    {
        $partnerData = $request->only(['partner', 'short_description', 'url', 'active']);

        if (!array_key_exists('active', $partnerData)) {
            $partner['active'] = false;
        }

        $partner->update($partnerData);

        if ($partner instanceof Partner) {
            if ($request->has('image')) {
                if ($this->uploadPartnersImage($request, $partner)) {
                    return redirect()->route('admin::partners.index');
                }
            }
        }

        return redirect()->back();
    }

    /**
     * @param Partner $partner
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Partner $partner)
    {
        $partner->delete();
        return redirect()->back();
    }


    /**
     * @param Request $request
     * @param Partner $partner
     * @return bool
     */
    private function uploadPartnersImage(Request $request, Partner $partner)
    {
        if ($request->hasFile('image')) {
            /** @var Asset $image */
            $asset = $this->assetService->storeImage(new ImagePartner($request->file('image')));

            if ($asset instanceof Asset) {
                return $partner->update(['asset_id' => $asset->id, 'image_type' => 'file']);
            }
        } else {
            $image = $request->post('image', null);
            $image_type = 'url';
            return $image ? $partner->update(compact('image', 'image_type')) : true;
        }

        return true;
    }

}
