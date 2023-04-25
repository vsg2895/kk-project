<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Jakten\Models\School;

class RemoveSegmentDuplicates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'segment:dr {--vs=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Segments duplicates remover';

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
        $target = (int)$this->option('vs');

        /** @var School $school */
        foreach ($this->getSchools() as $school) {
            $segments = $school->prices()->where('vehicle_segment_id', $target);

            if ($segments->count() > 1) {
                $this->output->text("School {$school->id} has more than 1 segments for {$target} segment");
            }
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|School[]
     */
    private function getSchools()
    {
        return School::all();
    }
}
