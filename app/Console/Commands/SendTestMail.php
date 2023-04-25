<?php namespace Jakten\Console\Commands;

use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;
use Jakten\Mail\TestMail;

/**
 * Class SendTestMail
 * @package Jakten\Console\Commands
 */
class SendTestMail extends Command
{
    /**
     * @var string $signature
     */
    protected $signature = 'mail:test
                            {email : The email that should receive the test mail}';
    /**
     * @var string $description
     */
    protected $description = 'Sends a test mail to the specificed email address.';

    /**
     * SendTestMail constructor.
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
        $email = $this->argument('email');

        Mail::send(new TestMail($email));

        $this->info('Tried to send mail to ' . $email . '.');
    }
}
