<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Jakten\Models\Annotation;
use Jakten\Models\School;

class ExportAnnotations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'annotations:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export annotations';

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

        /** @var Collection|School[] $schools */
        $schools = School::all();
        $data = collect();

        $schools->each(function (School $school) use (&$data) {

            $annotations = collect();

            $school->comments()
                ->each(function (Annotation $annotation) use ($school, &$annotations) {
                    $annotations->push([
                        'email' => $annotation->user->email,
                        'type' => $annotation->type,
                        'data' => $annotation->data,
                        'message' => $annotation->message,
                    ]);
                });

            if ($annotations->isNotEmpty()) {
                $data->put($school->name, $annotations);
            }
        });

        file_put_contents(
            public_path('annotations.json'),
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
}
