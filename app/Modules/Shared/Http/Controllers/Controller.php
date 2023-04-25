<?php namespace Shared\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class Controller
 * @package Shared\Http\Controllers
 * @property KKJTelegramBotService botService
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** @var KKJTelegramBotService  */
    protected $botService;

    public function __construct(KKJTelegramBotService $botService)
    {
        $this->botService = $botService;
    }
}
