<?php

namespace TelegramBot\Http\Controllers;

use Shared\Http\Controllers\Controller;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

/**
 * Class WebHookController
 * @package TelegramBot\Http\Controllers
 */
class WebHookController extends Controller
{
    public function init()
    {
        /** @var Update $update */
        $update = Telegram::commandsHandler(true);
    }
}
