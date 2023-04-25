<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use Jakten\Services\RuleAPIService;

class ExportSchoolsToRule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rule:schools';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all schools to Rule';

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
    public function handle(SchoolRepositoryContract $schoolRepo, RuleAPIService $ruleAPIService)
    {
        $schools = $schoolRepo->query()->get();

        foreach ($schools as $school) {
            echo $school->id . '--';
            $ruleAPIService->addSubscriber($school);
        }

        $this->info('Schools exported to Rule successfully');
        Log::info('Command rule:schools-> Schools exported to Rule successfully');
    }
}
