<?php namespace Jakten\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Jakten\Helpers\Roles;
use Jakten\Mail\ContactCreated;
use Jakten\Mail\OrderCancelled;
use Jakten\Mail\OrderCreated;
use Jakten\Mail\OrganizationCreated;
use Jakten\Mail\UserCreated;
use Jakten\Models\ConfirmationToken;
use Jakten\Models\ContactRequest;
use Jakten\Models\Course;
use Jakten\Models\Order;
use Jakten\Models\OrderItem;
use Jakten\Models\Organization;
use Jakten\Models\User;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class SendEmails
 * @package Jakten\Console\Commands
 */
class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kkj:send_emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var PasswordBroker
     */
    private $broker;

    /**
     * Create a new command instance.
     *
     * @param PasswordBroker $broker
     */
    public function __construct(PasswordBroker $broker)
    {
        parent::__construct();
        $this->broker = $broker;
    }

    /**
     * Execute the console command.
     *
     * @param KKJTelegramBotService $telegramBotService
     * @return mixed
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(KKJTelegramBotService $telegramBotService)
    {
        if ($this->confirm('Sending to ' . config('mail.from.address') . ', ok?')) {

            DB::beginTransaction();

            $this->comment('Send OrganizationCreated');
            $organization = new Organization(['name' => 'Test AB', 'org_number' => '5555555555']);
            $organization->save();

            $user = new User();
            $user->email = config('mail.from.address');
            $user->given_name = 'Förnamn';
            $user->family_name = 'Efternamn';
            $user->organization_id = $organization->id;
            $user->phone_number = '+4676654321';
            $user->role_id = Roles::ROLE_ORGANIZATION_USER;
            $user->save();

            $confirmationToken = new ConfirmationToken();
            $confirmationToken->email = config('mail.from.address');
            $confirmationToken->token = '2u3g489fsdh9';
            $confirmationToken->save();
            Mail::send(new OrganizationCreated($user));

            $this->comment('Send UserCreated');
            Mail::send(new UserCreated($user, $confirmationToken));

            $this->comment('Sending ContactCreated email');
            $contactRequest = new ContactRequest();
            $contactRequest->email = config('mail.from.address');
            $contactRequest->message = 'Test contact request.';
            $contactRequest->title = 'Test subject';
            $contactRequest->name = 'Förnamn Efternamn';
            $contactRequest->save();
            Mail::send(new ContactCreated($contactRequest));

            $this->comment('Sending OrderCreated email');
            $order = new Order();
            $order->payment_method = 'MANUAL';
            $order->user_id = $user->id;
            $order->save();
            $course = new Course();
            $course->vehicle_segment_id = 1;
            $course->address_description = 'Beskrivning av adressen där lektionen kommer att ske.';
            $course->confirmation_text = 'Bekräftelse meddelande som körskolorna själva kan ange';
            $course->description = 'Beskrivning av kursen, t.ex. vad man ska ha med sig eller innehåller i kursen';
            $course->city_id = 1;
            $course->school_id = 1;
            $course->price = 500;
            $course->seats = 10;
            $course->address = 'Testvägen 1';
            $course->length_minutes = 40;
            $course->start_time = Carbon::now();
            $course->save();
            $orderItem = new OrderItem(['course_id' => $course->id, 'name' => 'Introduktionskurs', 'quantity' => 1, 'amount' => 500, 'order_id' => $order->id, 'type' => 'INTRODUCTION_CAR']);
            $orderItem->save();
            Mail::send(new OrderCreated($order));
            Mail::send(new OrderCancelled($order));

            $this->comment('Sending PasswordReset email');
            $this->broker->sendResetLink(['email' => $user->email]);

            DB::rollback();
        }

    }
}
