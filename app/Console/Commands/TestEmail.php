<?php

namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Jakten\Mail\PromoteCourses as PromoteCoursesMail;
use Jakten\Models\Course;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email';

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
    public function handle()
    {
        $ok = Mail::send(new PromoteCoursesMail(Course::find(76730), 'vahramnewuser@gmail.com'));

        $this->info('Email sent');
    }
}
