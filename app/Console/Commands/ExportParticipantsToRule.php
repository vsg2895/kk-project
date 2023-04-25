<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Jakten\Models\AddonParticipant;
use Jakten\Repositories\CourseParticipantsRepository;
use Jakten\Services\RuleAPIService;

class ExportParticipantsToRule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rule:participants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export last hour participants to Rule';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(
        RuleAPIService $ruleAPIService,
        CourseParticipantsRepository $courseParticipantsRepo,
        AddonParticipant $addonParticipant
    )
    {
        $threeMonthsBefore = Carbon::now()->subMonths(3)->format('Y-m-d H:i:s');

        //RULE throttling is 2000 request / 10 mins
        //2022-11-10 15:50:10
        //export course participants
        $participants = $courseParticipantsRepo->query()->where('created_at', '>', $threeMonthsBefore)->skip(4500)->take(2000)->get();

        foreach ($participants as $participant) {
            echo $participant->id . '--';
            $ruleAPIService->addSubscriber($participant, 'student', null);
        }
        echo $participants->count() . '--';


        //export addon participants
        $participants = $addonParticipant::where('created_at', '>', $threeMonthsBefore)->get();

        foreach ($participants as $participant) {
            echo $participant->id . '--';
            $ruleAPIService->addSubscriber($participant, 'student_addon', null);
        }

        echo $participants->count() . '--';
        dd(1);

        $this->info('Course participants exported to Rule successfully');
        Log::info('Command rule:participants-> Course participants exported to Rule successfully');
    }
}
