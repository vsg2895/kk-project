<?php

namespace Jakten\Modules\TelegramBot\Commands;

use Jakten\Helpers\TelegramBotHelper;
use Telegram\Bot\Commands\Command;

class TemplateCommand extends Command
{

    use TelegramBotHelper;

    /**
     * @var string
     */
    protected $name = 'template';

    /**
     * @var string
     */
    protected $description = 'This is the command Template';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {

    }
}
