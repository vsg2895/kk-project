<?php

namespace Jakten\Console\Commands;

use Illuminate\Container\Container as App;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use Jakten\Repositories\SchoolRepository;
use Jakten\Services\LoyaltyProgramService;

class ResetLoyaltyLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kkj:reset_loyalty_level';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'All schools loyalty-level become to Bronze (default)';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $schoolRepo;

    public function __construct(SchoolRepository $schoolRepository)
    {
        parent::__construct();
        $this->schoolRepo = $schoolRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        try {
            $this->schoolRepo->query()->update(['loyalty_level' => 'bronze']);
            Log::info('All schools loyalty-levels reset successfully');
        }catch (\Exception $e)
        {
            Log::error($e->getMessage() . ' - Reset Loyalty Level');
        }

    }
}
