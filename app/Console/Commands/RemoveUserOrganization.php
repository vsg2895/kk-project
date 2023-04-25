<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Jakten\Models\Organization;
use Jakten\Models\School;

class RemoveUserOrganization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'org:delete {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Organization removal';

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
     * @throws \Exception
     */
    public function handle()
    {
        $organization = (int)$this->option('id');

        if (!$organization) {
            $this->output->error("Organization ID hasn't been provided");
            return;
        }

        /** @var Organization $organization */
        $organization = Organization::find($organization);

        if (!$organization instanceof Organization) {
            $this->output->error("Failed to find an organization");
            return;
        }

        $this->output->text("Deleting Organization Schools");
        $organization->schools()->each(function (School $school) {
            $school->prices()->forceDelete();
            $school->customAddons()->forceDelete();
            $school->comments()->forceDelete();
            $school->segments()->forceDelete();
            $school->claims()->forceDelete();
            $school->courses()->forceDelete();
            $school->forceDelete();
        });

        $this->output->text("Deleting Organization Annotations");
        $organization->comments()->forceDelete();

        $this->output->text("Deleting Organization Users");
        $organization->users()->forceDelete();

        $this->output->text("Deleting Organization Claims");
        $organization->claims()->forceDelete();

        $this->output->text("Deleting Organization");
        $organization->delete();

        $this->output->success("Finished");
    }
}
