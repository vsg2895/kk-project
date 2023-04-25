<?php namespace Jakten\Console\Commands;

use Jakten\Models\School;
use Illuminate\Console\Command;
use Jakten\Services\Statistics\StatisticsService;

/**
 * Class GetStats
 * @package Jakten\Console\Commands
 */
class GetStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kkj:rebuild_statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forced statistics cache rebuild';

    /**
     * @var StatisticsService
     */
    private $statisticsService;

    /**
     * GetStats constructor.
     * @param StatisticsService $statisticsService
     */
    public function __construct(StatisticsService $statisticsService)
    {
        parent::__construct();
        $this->statisticsService = $statisticsService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->statisticsService->setForceRebuild();
        if (env('StatisticsServiceCache', "no") == 'yes') {
            if (env('StatisticsServiceCacheAll', "no") == 'yes') {
                $schools = School::query()
                    ->select('organization_id')
                    ->groupBy('organization_id')
                    ->orderByDesc('organization_id')
                    ->get()
                    ->all();
                foreach ($schools as $school) {
                    // Rebuild by organization
                    $this->statisticsService->rebuild($school->organization_id);
                }
            }
            // Rebuild admin
            $this->statisticsService->rebuild(null);
        }
    }
}
