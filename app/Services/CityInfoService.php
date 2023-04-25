<?php namespace Jakten\Services;

use Jakten\Models\CityInfo;
use Illuminate\Foundation\Http\FormRequest;
use Jakten\Repositories\Contracts\CityInfoRepositoryContract;

/**
 * Class CourseService
 * @package Jakten\Services
 */
class CityInfoService
{
    /**
     * @var CityInfoRepositoryContract
     */
    private $cityInfo;

    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * CityInfoService constructor.
     *
     * @param CityInfoRepositoryContract $cityInfo
     * @param ModelService $modelService
     */
    public function __construct(CityInfoRepositoryContract $cityInfo, ModelService $modelService)
    {
        $this->cityInfo = $cityInfo;
        $this->modelService = $modelService;
    }

    /**
     * @param CityInfo $cityInfo
     * @param FormRequest $request
     * @return \Illuminate\Database\Eloquent\Model|CityInfo
     */
    public function updateCityInfo(CityInfo $cityInfo, FormRequest $request)
    {
        $cityInfo = $this->modelService->updateModel($cityInfo, $request->all());
        $cityInfo->save();

        return $cityInfo;
    }
}
