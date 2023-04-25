<?php

namespace Jakten\Modules\TelegramBot\Commands;

use Illuminate\Support\Facades\Artisan;
use Jakten\Helpers\TelegramBotHelper;
use Jakten\Models\Log;
use Telegram\Bot\Commands\Command;

class RunActivationCommand extends Command
{

    use TelegramBotHelper;

    /**
     * @var string
     */
    protected $name = 'run_activation';

    /**
     * @var string
     */
    protected $description = 'Running activation of the courses';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        list($env) = explode(' ', $arguments);

        if (!$this->can($this->getUser()) || env('APP_ENV') !== $env) {
            Log::info('Cannot be ran here');
            return false;
        }

        try {
            $this->replyWithChatAction(['action' => 'typing']);
            $this->replyWithMessage(['text' => 'Invoking Re-Activation of the courses on the ' . $env]);

            Artisan::call('courses:reactivate');
            $this->replyWithMessage(['text' => 'Reactivation has been invoked successfully!']);
        } catch (\Exception $exception) {
            $this->replyWithMessage(['text' => "Exception : {$exception->getMessage()}"]);
        }
    }
}
