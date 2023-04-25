<?php

namespace Jakten\Console\Commands;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Jakten\Jobs\ActivateCourses;
use Jakten\Models\Course;
use Jakten\Models\Order;
use Jakten\Models\OrderItem;

class ReActivateCourses extends Command
{

    use InteractsWithQueue, Queueable;

    /** @var Collection */
    private $courses;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:reactivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reactivation the courses';

    /** @var int */
    protected $start = 0;

    /** @var int */
    protected $finished = 0;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->start = time();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $coursesId = OrderItem::query()
            ->where('delivered', '=', 0)
            ->where('cancelled', '=', '0')
            ->where('credited', '=', 0)
            ->pluck('course_id')
            ->toArray();

        $this->courses = Course::query()
            ->orderBy('start_time', 'asc')
            ->whereIn('id', array_unique($coursesId))
            ->get();

        $this->output->block("Started courses activation scheduling");
        $this->output->text("Total Courses Count for Re-activation : {$this->courses->count()}");

        $this->courses->each(function (Course $course) {
            try {
                $bookings = $course->bookings->count();
                $this->output->text("Scheduling a job to activate the Course {$course->id} : {$bookings} booking(-s) ({$course->start_time->diffForHumans()})");
                ActivateCourses::dispatch($course);
            } catch (\Exception $exception) {
                $this->output->error($exception->getMessage());
                Log::info($exception->getTraceAsString());
            }
        });

        $this->output->success("Scheduling has been finished");

        $this->finished = time();
        $this->output->comment("Time elapsed : " . number_format($this->finished - $this->start, 2) . "s");
    }
}
