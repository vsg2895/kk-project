<?php namespace Api\Http\Controllers;

use Jakten\Presenters\SimplifiedCities;
use Jakten\Repositories\Contracts\CityRepositoryContract;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class CityController
 * @package Api\Http\Controllers
 */
class CityController extends ApiController
{
    /**
     * @var CityRepositoryContract
     */
    private $cities;

    /**
     * CitiesController constructor.
     *
     * @param CityRepositoryContract $cities
     * @param KKJTelegramBotService $botService
     */
    public function __construct(CityRepositoryContract $cities, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->cities = $cities;
    }

    /**
     * @param SimplifiedCities $presenter
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(SimplifiedCities $presenter)
    {
        $cities = $this->cities->getForSelect(['schools', 'info']);
        $cities = $presenter->format($cities);

        return $this->success($cities);
    }
}
