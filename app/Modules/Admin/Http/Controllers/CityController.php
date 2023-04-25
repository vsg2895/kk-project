<?php namespace Admin\Http\Controllers;

use Admin\Http\Requests\UpdateCityRequest;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Jakten\Models\CityBestDeals;
use Jakten\Models\School;
use Jakten\Repositories\Contracts\CityInfoRepositoryContract;
use Jakten\Repositories\Contracts\CityRepositoryContract;
use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use Jakten\Services\CityInfoService;

class CityController
{
    /**
     * @var CityRepositoryContract
     */
    protected $cities;

    /**
     * @var CityInfoRepositoryContract
     */
    protected $cityInfo;

    /**
     * @var CityInfoService
     */
    protected $cityInfoService;
    /**
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * CityController constructor.
     * @param SchoolRepositoryContract $schools
     * @param CityRepositoryContract $cities
     * @param CityInfoRepositoryContract $cityInfo
     * @param CityInfoService $cityInfoService
     */
    public function __construct(SchoolRepositoryContract $schools, CityRepositoryContract $cities, CityInfoRepositoryContract $cityInfo, CityInfoService $cityInfoService)
    {
        $this->schools = $schools;
        $this->cities = $cities;
        $this->cityInfo = $cityInfo;
        $this->cityInfoService = $cityInfoService;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $cities = $this->cities->search($request);

        return view('admin::cities.index', [
            'cities' => $cities->paginate()->appends(Input::except('page')),
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $city = $this->cities->query()->findOrFail($id);
        return view('admin::cities.show', ['city' => $city]);
    }

    /**
     * @param $id
     * @param UpdateCityRequest $request
     * @return UpdateCityRequest
     */
    public function update($id, UpdateCityRequest $request)
    {
        $city = $this->cities->query()->findOrFail($id);
        $bestDeals = School::query()->whereIn('id', explode(',', $request->post(('best_schools_id'))))->get();

        CityBestDeals::query()->where('city_id', '=', $id)->delete();

        if ($bestDeals) {
            $city->bestDeals()->saveMany($bestDeals);
        }

        $city->update(['school_description' => $request->post('school_description') ?? null, 'search_radius' => $request->post('search_radius') ?? null]);
        $city->refresh();

        $cityInfo = $this->cityInfo->query()->where('city_id', $id)->firstOrCreate(['city_id' => $id]);
        $cityInfo = $this->cityInfoService->updateCityInfo($cityInfo, $request);
        $request->session()->flash('message', 'Stad uppdaterad!');

        return redirect()->route('admin::cities.show', ['id' => $cityInfo->city_id]);
    }
}
