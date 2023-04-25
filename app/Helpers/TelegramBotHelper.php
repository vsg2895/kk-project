<?php

namespace Jakten\Helpers;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\TelegramChat;
use Telegram\Bot\Objects\Chat;

/**
 * Trait TelegramBotHelper
 * @package Jakten\Helpers
 */
trait TelegramBotHelper
{


    /**
     * Returns nor create a telegram chat
     *
     * @return Model|TelegramChat
     */
    public function getUser()
    {
        $tgUserInstance = $this->getUpdate()
            ->getMessage()
            ->getChat();

        return TelegramChat::query()
            ->firstOrCreate([
                'chat_id' => $tgUserInstance['id'],
            ]);
    }

    /**
     * Returns a full name of the chat user
     *
     * @param Chat $chat
     * @return string
     */
    public function getFullName(Chat $chat)
    {
        return implode(' ', array_filter([$chat['first_name'], $chat['last_name']]));
    }

    /**
     * Checks if this particular chat can perform some operations
     *
     * @param TelegramChat $chat
     * @return bool
     */
    public function can(TelegramChat $chat)
    {
        return $chat->enabled;
    }

}
