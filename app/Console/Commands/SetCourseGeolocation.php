<?php namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Jakten\Console\Progress;
use Jakten\Repositories\Contracts\CourseRepositoryContract;

/**
 * Class SetCourseGeolocation
 * @package Jakten\Console\Commands
 */
class SetCourseGeolocation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kkj:course_address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var CourseRepositoryContract
     */
    private $courses;

    /**
     * @var Progress
     */
    private $progress;

    /**
     * Create a new command instance.
     *
     * @param CourseRepositoryContract $courses
     */
    public function __construct(CourseRepositoryContract $courses, Progress $progress)
    {

        ini_set('memory_limit', '2048M');

        parent::__construct();
        $this->courses = $courses;
        $this->progress = $progress;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $courses = $this->courses->inFuture()
            ->query()->where(function($query) {
                $query->whereNull('latitude')->orWhereNull('longitude');
            })
            ->get();

        $total = $courses->count();
        $done = 0;

        foreach ($courses as $course) {
            $school = $course->school;
            $course->latitude = $school->latitude;
            $course->longitude = $school->longitude;
            $course->zip = $school->zip;
            $course->postal_city = $school->postal_city;
            $course->save();
            $done++;
            $this->progress->showProgress($done, $total);
        }
    }
}
