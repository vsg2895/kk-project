<?php namespace Api\Http\Controllers;

use Jakten\Repositories\Contracts\CountyRepositoryContract;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class CountyController
 * @package Api\Http\Controllers
 */
class CountyController extends ApiController
{
    /**
     * @var CountyRepositoryContract
     */
    private $counties;

    /**
     * CitiesController constructor.
     *
     * @param CountyRepositoryContract $counties
     * @param KKJTelegramBotService $botService
     */
    public function __construct(CountyRepositoryContract $counties, KKJTelegramBotService $botService)
    {
        parent::__construct($botService);
        $this->counties = $counties;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        return $this->success($this->counties->query()->with('cities')->get());
    }
}
