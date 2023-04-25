<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use Jakten\Repositories\Contracts\SchoolSegmentPriceRepositoryContract;

class SchoolSegmentFeeManual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:fees {--segmentId=} {--fee=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle(SchoolSegmentPriceRepositoryContract $schoolPrice, SchoolRepositoryContract $school)
    {
        //todo add here also more info (see 2023_01_12_131919_add_new_course_to_vehicle_segments_table migration)
//        $this->error('!!!Be sure you will not break fees after running this command');
        $segmentId = $this->option('segmentId');
        if ($segmentId < 0 || $segmentId > 40) {
            $this->error('Select valid segment id');
        } else {
            $schoolIds = $school->query()->pluck('id')->toArray();
            $insertData = [];
            foreach ($schoolIds as $schoolId) {
                $this->line('Importing - ' . $schoolId);
                $insertData[] = [
                    'school_id' => $schoolId,
                    'vehicle_segment_id' => $segmentId,
                    'fee' => $this->option('fee'),
                ];
            }

            $schoolPrice->query()->insert($insertData);
            $this->info('DONE!');
        }
    }
}
