<?php namespace Jakten\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Jakten\Facades\Auth;
use Jakten\Models\TelegramChat;
use Jakten\Models\User;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramOtherException;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Message;

/**
 * Class UserService
 * @package Jakten\Services
 */
class KKJTelegramBotService
{

    /** @var Api */
    public $bot;

    /**
     * KKJTelegramBotService constructor.
     * @throws TelegramSDKException
     */
    public function __construct()
    {
        $this->bot = new Api(config('telegram.bot_token'));
    }

    /**
     * @param $url
     * @throws TelegramSDKException
     */
    public function setWebHookUrl($url)
    {
        $this->bot->setWebhook(compact('url'));
    }

    /**
     * @param string $action
     * @param array $payload
     * @return bool|Message
     */
    public function log($action = 'test', $payload = [])
    {
        if (env('TELEGRAM_BOT_ENABLED', false)) {
            $send = function (...$args) {
                list($chatId, $action, $payload) = $args;

                $url = env('APP_URL');
                $env = env('APP_ENV');

                $invoker = 'System';

                if (Auth::check()) {
                    /** @var User $user */
                    $user = Auth::user();
                    $invoker = "#{$user->id} {$user->name}";
                }

                try {
                    $this->bot->sendMessage(
                        [
                            'parse_mode' => 'html',
                            'chat_id' => $chatId,
                            'text' => "<b>Environment:</b> {$env}\r\n" .
                                "<b>Action:</b> \"{$action}\"\r\n" .
                                "<b>Invoked By:</b> \"{$invoker}\"\r\n" .
                                "<b>Payload:</b> <code>" . json_encode($payload, JSON_PRETTY_PRINT) . "</code>\r\n" .
                                "<b>Timestamp:</b> " . Carbon::now()->getTimestamp()
                        ]
                    );
                } catch (TelegramOtherException $exception) {
                    Log::info($exception->getMessage());
                }
            };

            switch (in_array(env('APP_ENV'), ['local'])) {
                case true:
                    $send(env('LOG_CHAT_ID'), $action, $payload);
                    break;
                default:
                    TelegramChat::query()
                        ->where('enabled', true)
                        ->get()
                        ->each(function (TelegramChat $telegramChat) use ($send, $action, $payload) {
                            try {
                                $send($telegramChat->chat_id, $action, $payload);
                            } catch (\Exception $exception) {
                                Log::info($exception->getMessage());
                            }

                        });
                    break;
            }

            return true;
        }

        Log::info('Telegram bot is disabled');
        return false;
    }

}
