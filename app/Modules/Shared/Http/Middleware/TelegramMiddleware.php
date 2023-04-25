<?php

namespace Jakten\Modules\Shared\Http\Middleware;

use App\Services\TelegramService;
use Closure;
use Jakten\Models\Log;
use Jakten\Models\TelegramWebHook;
use Jakten\Services\KKJTelegramBotService;

/**
 * @property KKJTelegramBotService telegramService
 */
class TelegramMiddleware
{

    /**
     * TelegramMiddleware constructor.
     */
    public function __construct()
    {
        $this->telegramService = resolve(KKJTelegramBotService::class);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle($request, Closure $next)
    {
        $url = route('telegram::hook');

        if (!preg_match('~\.test~', $url)) {

            if (env('APP_ENV') === 'local') {
                $url = str_replace('http', 'https', $url);
            }

            $webHook = TelegramWebHook::query()
                ->firstOrCreate(compact('url'));

            if ($webHook->wasRecentlyCreated) {
                /** @var KKJTelegramBotService $telegramBot */
                $this->telegramService->setWebHookUrl($url);
            }
        }

        return $next($request);
    }
}
