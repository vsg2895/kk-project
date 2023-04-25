<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Jakten\Services\KKJTelegramBotService;

class SetTelegramWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tg:web-hook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enables the web hook';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var KKJTelegramBotService $tgService */
        $tgService = resolve(KKJTelegramBotService::class);

        try {
            $tgService->bot->setWebhook(['url' => route('telegram::hook')]);
            $this->output->success('Web Hook has been is set up correctly');
        } catch (\Exception $exception) {
            $this->output->error("Failed to setup the web hook : {$exception->getMessage()}");
        }
    }
}
