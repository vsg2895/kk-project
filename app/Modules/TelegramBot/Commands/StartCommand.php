<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 2019-08-22
 * Time: 00:05
 */

namespace Jakten\Modules\TelegramBot\Commands;


use Illuminate\Support\Facades\Log;
use Jakten\Helpers\TelegramBotHelper;
use Jakten\Models\TelegramChat;
use Psy\Command\HelpCommand;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Objects\Chat;
use Telegram\Bot\Objects\User;

class StartCommand extends Command
{

    use TelegramBotHelper;

    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = 'Chat registering to receive the notifications';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        /** @var Chat $user */
        $tgUserInstance = $this->getUpdate()->getMessage()->getChat();
        $user = $this->getUser();

        if ($user instanceof TelegramChat) {
            $user->update([
                'username' => $tgUserInstance['username'], 'full_name' => $this->getFullName($tgUserInstance)
            ]);
            $user->refresh();
        }

        if ($user->wasRecentlyCreated) {
            $this->replyWithChatAction(['action' => 'typing']);
            $this->replyWithMessage([
                'text' => "Hello _{$user->full_name}!_\r\n" .
                    "Welcome to the *Korkortsjakten Logger Bot!*\r\n" .
                    "Here You will be receiving latest logs action,\r\n" .
                    "which has been performed on the *Staging* and *Production* servers.\r\n" .
                    "But, while Your account is _disabled_, You are not going to receive notifications from the events.",
                'parse_mode' => 'markdown'
            ]);
        }
    }
}
