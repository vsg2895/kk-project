<?php namespace Jakten\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Jakten\Events\NewOrder;
use Jakten\Helpers\Participants;
use Jakten\Helpers\Roles;
use Jakten\Jobs\PromoteCourses;
use Jakten\Jobs\ReminderCourses;
use Jakten\Mail\OrderCreated;
use Jakten\Models\ConfirmationToken;
use Jakten\Models\Course;
use Jakten\Models\CourseParticipant;
use Jakten\Models\Order;
use Jakten\Models\OrderItem;
use Jakten\Models\Organization;
use Jakten\Models\User;
use Jakten\Services\KKJTelegramBotService;

/**
 * Class SendEmails
 * @package Jakten\Console\Commands
 */
class SendReminderPromoteEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kkj:send_pr_emails';

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
        DB::beginTransaction();

        $order = new Order();
        $order->payment_method = 'MANUAL';
        $order->user_id = 11286;
        $order->save();

        $course = new Course();
        $course->vehicle_segment_id = \Jakten\Models\VehicleSegment::INTRODUKTIONSKURS;
        $course->address_description = 'Beskrivning av adressen där lektionen kommer att ske.';
        $course->confirmation_text = 'Bekräftelse meddelande som körskolorna själva kan ange';
        $course->description = 'Beskrivning av kursen, t.ex. vad man ska ha med sig eller innehåller i kursen';
        $course->city_id = 1;
        $course->school_id = 1;
        $course->price = 500;
        $course->seats = 10;
        $course->address = 'Testvägen 1';
        $course->length_minutes = 1;
        $course->start_time = Carbon::now()->addMinute(3)->format('Y-m-d H:i');
        $course->save();

        $orderItem = new OrderItem(['course_id' => $course->id, 'name' => 'Introduktionskurs', 'quantity' => 1, 'amount' => 500, 'order_id' => $order->id, 'type' => 'INTRODUCTION_CAR']);
        $orderItem->save();

        $participant = new CourseParticipant();
        $participant->order_item_id = $orderItem->id;
        $participant->course_id = $course->id;
        $participant->given_name = 'Test';
        $participant->family_name = 'Test';
        $participant->email = 'landauthompson@gmail.com';
        $participant->social_security_number = '981028-9430';
        $participant->type = Participants::PARTICIPANT_TUTOR;
        $participant->save();

        $course2 = new Course();
        $course2->vehicle_segment_id = 13;
//        $course2->vehicle_segment_id = \Jakten\Models\VehicleSegment::INTRODUKTIONSKURS;
        $course2->address_description = 'Beskrivning av adressen där lektionen kommer att ske.';
        $course2->confirmation_text = 'Bekräftelse meddelande som körskolorna själva kan ange';
        $course2->description = 'Beskrivning av kursen, t.ex. vad man ska ha med sig eller innehåller i kursen';
        $course2->city_id = 1;
        $course2->school_id = 1;
        $course2->price = 500;
        $course2->seats = 10;
        $course2->address = 'Testvägen 1';
        $course2->length_minutes = 1;
        $course2->start_time = Carbon::now()->addMinute(3)->format('Y-m-d H:i');
        $course2->save();

        $order2Item = new OrderItem(['course_id' => $course2->id, 'name' => 'Introduktionskurs', 'quantity' => 1, 'amount' => 500, 'order_id' => $order->id, 'type' => 'INTRODUCTION_CAR']);
        $order2Item->save();

        $participant2 = new CourseParticipant();
        $participant2->order_item_id = $order2Item->id;
        $participant2->course_id = $course2->id;
        $participant2->given_name = 'Test';
        $participant2->family_name = 'Test';
        $participant2->email = 'landauthompson@gmail.com';
        $participant2->social_security_number = '981028-9430';
        $participant2->type = Participants::PARTICIPANT_TUTOR;
        $participant2->save();

        DB::commit();

        $order->fresh();
        $order->refresh();

//        $this->comment('Sending Reminder email');
//
//        ReminderCourses::dispatch($course);

        $this->comment('Sending Promote email');

        PromoteCourses::dispatch($course);

//        $this->comment('Sending Reminder email');
//
//        ReminderCourses::dispatch($course2);

        $this->comment('Sending Promote email');

        PromoteCourses::dispatch($course2);

    }
}
