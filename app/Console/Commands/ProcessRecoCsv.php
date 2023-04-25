<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Jakten\Models\School;

class ProcessRecoCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reco:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processing Reco\'s CSV file with teh schools';

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
        $this->output->text("Initializing CSV parsing process...");
        $this->processCSV(public_path() . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'mapping-schools-reco.csv');
    }

    /**
     * @param string $path
     */
    private function processCSV(string $path): void
    {
        $csv = explode("\r", file($path)[0]);

        array_map(function ($record) use (&$line) {
            if ($record) {
                try {
                    list($id, , , , , , , , , , $recoId, $recoUrl) = explode(',', preg_replace('/"(.+?)"/', '', $record));

                    /** @var School $school */
                    $school = School::find($id);

                    if ($school instanceof School) {
                        $school->update(['reco_id' => (int)$recoId, 'reco_url' => $recoUrl]);
                    } else {
                        Log::error("School #{$id} is not found");
                    }

                } catch (\Exception $exception) {
                }
            }
        }, $csv);
        $this->output->newLine(2);
    }
}
