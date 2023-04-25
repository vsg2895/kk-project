<?php namespace Jakten\Services;

use Jakten\Models\SchoolUsps;
use Admin\Http\Requests\UspsRequest;
use Jakten\Repositories\Contracts\{UspsRepositoryContract, SchoolRepositoryContract};

/**
 * Class UspsService
 * @package Jakten\Services
 */
class UspsService
{
    /**
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * @var UspsRepositoryContract
     */
    private $usps;

    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * UspsService constructor.
     * @param SchoolRepositoryContract $schools
     * @param UspsRepositoryContract $usps
     * @param ModelService $modelService
     */
    public function __construct(SchoolRepositoryContract $schools, UspsRepositoryContract $usps,
                                ModelService $modelService)
    {
        $this->schools = $schools;
        $this->usps = $usps;
        $this->modelService = $modelService;
    }

    /**
     * @param UspsRequest $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function storeUsps(UspsRequest $request)
    {
        $usps = $this->modelService->createModel(SchoolUsps::class, $request->all());
        $usps->save();
        return $usps;
    }

    /**
     * @param SchoolUsps $schoolUsps
     * @param UspsRequest $request
     * @return SchoolUsps
     */
    public function updateUsps(SchoolUsps $schoolUsps, UspsRequest $request)
    {
        $data = $request->all();
        $schoolUsps = $this->modelService->updateModel($schoolUsps, $data);
        $schoolUsps->save();
        return $schoolUsps;
    }

    /**
     * @param SchoolUsps $schoolUsps
     * @throws \Exception
     */
    public function delete(SchoolUsps $schoolUsps)
    {
        $schoolUsps->delete();
    }

}
