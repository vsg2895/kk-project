<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Jakten\Models\Annotation;
use Jakten\Models\School;
use Jakten\Models\SchoolAnnotation;
use Jakten\Models\User;

class ImportAnnotations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'annotations:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Annotations';

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
        $annotations = collect(json_decode(file_get_contents(public_path('annotations.json')), 1));
        $annotations->each(function ($annotations, $schoolName) {
            /** @var School $school */
            $school = School::query()
                ->where('name', $schoolName)
                ->first();

            if ($school) {
                $this->output->comment("Adding comment to \"{$schoolName} ({$school->name})\" (ID {$school->id})");

                collect($annotations)
                    ->each(function ($annotation) use ($school) {
                        try {
                            /** @var User $user */
                            $user = User::query()
                                ->where('email', $annotation['email'])
                                ->withTrashed()
                                ->first();

                            if ($user) {
                                $annotation['school_id'] = $school->id;
                                $annotation['user_id'] = $user->id;

                                unset($annotation['email']);

                                $annotation = Annotation::query()
                                    ->create($annotation);

                                if ($annotation instanceof Annotation) {
                                    SchoolAnnotation::create([
                                        'school_id' => $school->id,
                                        'annotation_id' => $annotation->id
                                    ]);
                                }
                            } else {
                                $this->output->error("Cannot find user by email of '{$annotation['email']}'");
                            }
                        } catch (\Exception $exception) {
                            $this->output->error($exception->getMessage());
                        }
                    });
            } else {
                $this->output->error("Can't find School '{$schoolName}'");
            }
        });
    }
}
