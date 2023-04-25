<?php namespace Jakten\Services;

use Jakten\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Jakten\Repositories\Contracts\OrganizationRepositoryContract;
use Jakten\Services\Asset\{AssetService, Strategy\ImageLogo};

/**
 * Class OrganizationService
 * @package Jakten\Services
 */
class OrganizationService
{
    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * @var AssetService
     */
    private $assetService;

    /**
     * @var OrganizationRepositoryContract
     */
    private $organizations;

    /**
     * OrganizationService constructor.
     *
     * @param OrganizationRepositoryContract $organizations
     * @param ModelService $modelService
     */
    public function __construct(OrganizationRepositoryContract $organizations, AssetService $assetService, ModelService $modelService)
    {
        $this->modelService = $modelService;
        $this->organizations = $organizations;
        $this->assetService = $assetService;
    }

    /**
     * @param FormRequest $request
     *
     * @return Organization
     */
    public function storeOrganization(FormRequest $request)
    {
        $logo = $request->file('logo');
        $data = $request->except(['logo']);
        $organization = $this->modelService->createModel(Organization::class, $data);

        if ($logo) {
            $img = $this->assetService->storeImage(new ImageLogo($logo));
            $organization->logo_id = $img->id;
        }
        
        $organization->save();

        return $organization;
    }

    /**
     * @param Organization $organization
     * @param FormRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model|Organization
     */
    public function updateOrganization(Organization $organization, FormRequest $request)
    {
        $logo = $request->file('logo');
        $data = $request->except(['logo']);
        $organization = $this->modelService->updateModel($organization, $data);

        if ($logo) {
            $img = $this->assetService->storeImage(new ImageLogo($logo));
            $organization->logo_id = $img->id;
        }

        $organization->save();

        return $organization;
    }

    /**
     * @param Organization $organization
     * @throws \Exception
     */
    public function delete(Organization $organization)
    {
        $organization->delete();
    }
}
