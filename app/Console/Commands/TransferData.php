<?php namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Jakten\Console\Progress;
use Jakten\Helpers\Participants;
use Jakten\Helpers\Payment;
use Jakten\Helpers\Prices;
use Jakten\Helpers\Roles;
use Jakten\Models\Course;
use Jakten\Models\CourseParticipant;
use Jakten\Models\Order;
use Jakten\Models\OrderItem;
use Jakten\Models\Organization;
use Jakten\Models\School;
use Jakten\Models\SchoolUsp;
use Jakten\Models\User;
use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use Jakten\Repositories\Contracts\SchoolSegmentPriceRepositoryContract;

/**
 * Class TransferData
 * @package Jakten\Console\Commands
 */
class TransferData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kkj:transfer_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfers data from the old database to the new';

    /**
     * @var Progress
     */
    private $progress;

    /**
     * @var SchoolSegmentPriceRepositoryContract
     */
    private $segmentPrices;

    /**
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * Create a new command instance.
     *
     * @param Progress $progress
     * @param SchoolSegmentPriceRepositoryContract $segmentPrices
     * @param SchoolRepositoryContract $schools
     */
    public function __construct(Progress $progress, SchoolSegmentPriceRepositoryContract $segmentPrices, SchoolRepositoryContract $schools)
    {
        parent::__construct();
        $this->progress = $progress;
        $this->segmentPrices = $segmentPrices;
        $this->schools = $schools;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->comment('Transferring "member" schools.');
            $this->migrateOrganizations();
            $this->comment('Transferred!');
            $this->comment('Transferring the other schools.');
            $this->migrateSchools();
            $this->comment('Transferred!');
            $this->comment('Tranferring introduction courses and bookings.');
            $this->migrateIntroductionCourses();
            $this->comment('Transferred!');
            $this->comment('Tranferring risk one courses and bookings.');
            $this->migrateRiskOneCourses();
            $this->comment('Transferred!');
        } catch (\Exception $e) {
            dd($e->getFile(), $e->getLine(), $e->getMessage());
        }
    }

    /**
     * migrateOrganizations
     */
    private function migrateOrganizations()
    {
        $oldSchools = DB::connection('old')->table('trafikskolor')
            ->leftJoin('handledarkurser', 'trafikskolor.id', '=', 'handledarkurser.trafikskola_id')
            ->leftJoin('risk_courses', 'trafikskolor.id', '=', 'risk_courses.trafikskola_id')
            ->where(function ($query) {
                $query->whereNotNull('risk_courses.id')
                    ->orWhereNotNull('handledarkurser.id');
            })
            ->groupBy('trafikskolor.id')
            ->select(['trafikskolor.*'])
            ->get();

        $done = 0;
        foreach ($oldSchools as $oldSchool) {
            $done++;
            $this->progress->showProgress($done, $oldSchools->count());

            $existingUser = DB::table('users')->where('email', $oldSchool->email)->first();
            if ($existingUser) {
                $organization = DB::table('organizations')->where('id', $existingUser->organization_id)->first();
            } else {
                $orgNumber = preg_replace('/\D/', '', $oldSchool->orgnr);
                if ($orgNumber) {
                    $organization = DB::table('organizations')->where('org_number', $orgNumber)->first();
                    if (!$organization) {
                        $organization = new Organization();
                        $organization->org_number = $orgNumber;
                        $organization->name = $oldSchool->namn;
                        $organization->created_at = $oldSchool->created_at;
                        $organization->save();
                    } else {
                        $existingUser = DB::table('users')->where('organization_id', $organization->id)->first();
                    }
                } else {
                    $organization = new Organization();
                    $organization->name = $oldSchool->namn;
                    $organization->created_at = $oldSchool->created_at;
                    $organization->save();
                }
            }

            $school = $this->createSchool($oldSchool, $organization);

            if (!$existingUser) {
                $nameParts = explode(' ', $oldSchool->representant);
                $firstnameIndex = count($nameParts) - 1;
                $user = new User();
                $user->email = $oldSchool->email;
                $user->role_id = Roles::ROLE_ORGANIZATION_USER;
                $user->confirmed = true;
                $user->organization_id = $organization->id;
                $user->phone_number = $school->phone_number;

                $lastname = '';
                foreach ($nameParts as $index => $namePart) {
                    $namePart = preg_replace('/\PL/u', '', $namePart);
                    if ($index != $firstnameIndex) {
                        $lastname .= $namePart . ' ';
                    } else {
                        $user->given_name = $namePart;
                    }
                }

                $user->family_name = trim($lastname);
                $user->save();
                $user->password = $user->email;
                $user->save();
            }
            $this->addPrices($school, $oldSchool);
            $this->addVehicles($school, $oldSchool);
        }
    }

    /**
     * @param School $school
     * @param $oldSchool
     * @throws \Exception
     */
    private function addPrices(School $school, $oldSchool)
    {
        $this->segmentPrices->reset();
        $segmentPrices = $this->segmentPrices->forSchool($school)->query()->with('segment')->get();

        foreach ($segmentPrices as $segmentPrice) {

                switch ($segmentPrice->segment->name) {
                    case Prices::DRIVING_LESSON_CAR:
                        $segmentPrice->amount = $oldSchool->bil_korlektion_pris ?: NULL;
                        $segmentPrice->quantity = $oldSchool->bil_korlektion_min ?: NULL;
                        break;
                    case Prices::ENROLLMENT_CAR:
                        $segmentPrice->amount = $oldSchool->bil_inskrivning_pris ?: NULL;
                        $segmentPrice->quantity = $oldSchool->bil_inskrivning_pris ? 1 : NULL;
                        break;
                    case Prices::RISK_ONE_CAR:
                        $segmentPrice->amount = $oldSchool->bil_risk1_pris ?: NULL;
                        $segmentPrice->quantity = $oldSchool->bil_risk1_pris ? 1 : NULL;
                        break;
                    case Prices::RISK_TWO_CAR:
                        if ($oldSchool->bil_risk2_pris) {
                            $segmentPrice->amount = $oldSchool->bil_risk2_pris ?: NULL;
                            $segmentPrice->quantity = $oldSchool->bil_risk2_pris ? 1: NULL;
                            $segmentPrice->comment = null;
                        }

                        break;
                    case Prices::BORROW_CAR_DRIVING_TEST:
                        $segmentPrice->amount = $oldSchool->bil_uppkorning_pris ?: NULL;
                        $segmentPrice->quantity = $oldSchool->bil_uppkorning_pris ? 1 : NULL;
                        break;
                    case Prices::ENROLLMENT_MC:
                        $segmentPrice->amount = $oldSchool->mc_inskrivning_pris ?: NULL;
                        $segmentPrice->quantity = $oldSchool->mc_inskrivning_pris ? 1 : NULL;
                        break;
                    case Prices::DRIVING_LESSON_MC:
                        $segmentPrice->amount = $oldSchool->mc_korlektion_pris ?: NULL;
                        $segmentPrice->quantity = $oldSchool->mc_korlektion_pris ? $oldSchool->mc_korlektion_min : NULL;
                        break;
                    case Prices::RISK_ONE_MC:
                        $segmentPrice->amount = $oldSchool->mc_risk1_pris ?: NULL;
                        $segmentPrice->quantity = $oldSchool->mc_risk1_pris ? 1 : NULL;
                        break;
                    case Prices::RISK_TWO_MC:
                        $segmentPrice->amount = $oldSchool->mc_risk2_pris ?: NULL;
                        $segmentPrice->quantity = $oldSchool->mc_risk2_pris ? 1 : NULL;
                        break;
                    case Prices::BORROW_MC_DRIVING_TEST:
                        $segmentPrice->amount = $oldSchool->mc_uppkorning_pris ?: NULL;
                        $segmentPrice->quantity = $oldSchool->mc_uppkorning_pris ? 1 : NULL;
                        break;
                    case Prices::DRIVING_TEST_WARM_UP_MC:
                        $lessonTime = $oldSchool->mc_uppkorning_uppvarmning ? $oldSchool->mc_uppkorning_uppvarmning : NULL;
                        $price = null;
                        if ($lessonTime && $oldSchool->mc_korlektion_pris && $oldSchool->mc_korlektion_min) {
                            $lessonMinutePrice = ($oldSchool->mc_korlektion_pris/$oldSchool->mc_korlektion_min);
                            $price = $lessonMinutePrice * $lessonTime;
                            $price = -1 * abs($price);
                        }

                        $segmentPrice->amount = $price;
                        $segmentPrice->quantity = !is_null($price) ? 1 : NULL;
                        break;

                    case Prices::MOPED_PACKAGE:
                        $segmentPrice->amount = $oldSchool->moped_paket ?: NULL;
                        $segmentPrice->quantity = $oldSchool->moped_paket ? 1 : NULL;
                        break;
                }


            $segmentPrice->save();
        }
    }

    /**
     * @throws \Exception
     */
    private function migrateSchools()
    {
        $existingSchools = $this->schools->query()->get()->pluck('id')->all();
        $oldSchools = DB::connection('old')->table('trafikskolor')->whereNotIn('id', $existingSchools)->get();

        $done = 0;
        foreach ($oldSchools as $oldSchool) {
            $done++;
            $this->progress->showProgress($done, $oldSchools->count());
            $school = $this->createSchool($oldSchool);
            $this->addPrices($school, $oldSchool);
            $this->addVehicles($school, $oldSchool);
        }
    }

    /**
     * @param $oldSchool
     * @param null $organization
     *
     * @return School
     */
    private function createSchool($oldSchool, $organization = null)
    {
        $school = new School();
        if ($organization) {
            $school->organization_id = $organization->id;
        }
        $school->id = $oldSchool->id;
        $school->name = $oldSchool->namn;
        $school->city_id = $oldSchool->stadID == 0 ? 10000 : $oldSchool->stadID;
        $school->latitude = $oldSchool->lat;
        $school->longitude = $oldSchool->lng;
        $school->address = $oldSchool->adress;
        $zip = intval(preg_replace('/\D/', '', $oldSchool->postnr));
        if ($zip) {
            $school->zip = $zip ?: null;
        }
        $school->postal_city = $oldSchool->postort;
        $school->phone_number = $oldSchool->telefon;
        $school->contact_email = $oldSchool->email;
        $school->booking_email = $oldSchool->booking_email;
        $school->website = $oldSchool->url;
        $school->description = $oldSchool->profil;
        $school->created_at = $oldSchool->created_at;
        $school->save();

        if ($oldSchool->fordelar) {
            $strings = explode('&&', $oldSchool->fordelar);
            foreach ($strings as $string) {
                $string = trim($string);
                if ($string) {
                    if (strlen($string) < 255) {
                        $usp = new SchoolUsp();
                        $usp->text = $string;
                        $usp->school_id = $school->id;
                        $usp->save();
                    } else {
                        $school->description .= $string;
                        $school->save();
                    }
                }
            }
        }

        return $school;
    }

    /**
     * migrateRiskOneCourses
     */
    private function migrateRiskOneCourses()
    {
        $oldCourses = DB::connection('old')->table('risk_courses')->get();
        $segment = DB::table('vehicle_segments')->where('name', Prices::RISK_ONE_CAR)->first();

        $done = 0;
        foreach ($oldCourses as $oldCourse) {
            $done++;
            $this->progress->showProgress($done, $oldCourses->count());

            $oldBookings = DB::connection('old')->table('bokningar')->where('handledarkurs_id', $oldCourse->id)->get();
            $seatsTaken = 0;
            foreach ($oldBookings as $oldBooking) {
                $seatsTaken += $oldBooking->elever;
            }
            
            $school = DB::table('schools')->where('id', $oldCourse->trafikskola_id)->first();
            if ($school) {
                $course = new Course();
                $course->vehicle_segment_id = $segment->id;
                $course->school_id = $oldCourse->trafikskola_id;
                $course->city_id = $oldCourse->stad_id;
                $course->start_time = $oldCourse->tidpunkt;
                $course->length_minutes = $oldCourse->langd;
                $course->price = $oldCourse->pris;
                $course->address = $oldCourse->plats;
                $course->address_description = $oldCourse->platsbeskrivning;
                $course->description = $oldCourse->beskrivning;
                $course->confirmation_text = $oldCourse->meddelande;
                $course->seats = $oldCourse->platser - $seatsTaken;
                $course->created_at = $oldCourse->created_at;
                $course->save();

                $oldBookings = DB::connection('old')->table('risk_course_bookings')->where('risk_course_id', $oldCourse->id)->get();

                foreach ($oldBookings as $oldBooking) {
                    $user = DB::table('users')->where('email', $oldBooking->epost)->first();
                    if (!$user) {
                        $user = new User();
                        $user->email = $oldBooking->epost;
                        $user->phone_number = $oldBooking->telefon;
                        $user->role_id = Roles::ROLE_STUDENT;
                        $user->confirmed = true;
                        $user->created_at = $oldBooking->created_at;
                        $data = $user->toArray();
                        $data['password'] = uniqid();
                        unset($data['name']);
                        $user->id = DB::table('users')->insertGetId($data);
                    }

                    $order = new Order();
                    $order->user_id = $user->id;
                    $order->school_id = $course->school_id;
                    $order->handled = true;
                    $order->payment_method = Payment::DEPRECATED;
                    $order->created_at = $oldBooking->created_at;
                    $order->invoice_sent = true;
                    $data = $order->attributesToArray();
                    unset($data['invoice_amount']);
                    unset($data['paid']);
                    unset($data['can_be_cancelled']);
                    unset($data['order_value']);
                    $order->id = DB::table('orders')->insertGetId($data);

                    $oldParticipants = json_decode($oldBooking->referenser, true);

                    if (isset($oldParticipants['elev']) && count($oldParticipants['elev'])) {
                        foreach ($oldParticipants['elev'] as $oldStudent) {
                            $orderItem = new OrderItem();
                            $orderItem->school_id = $order->school_id;
                            $orderItem->order_id = $order->id;
                            $orderItem->course_id = $course->id;
                            $orderItem->amount = $course->price;
                            $orderItem->quantity = 1;
                            $orderItem->type = $course->segment->name;
                            $orderItem->provision = 0;
                            $orderItem->delivered = 1;
                            $orderItem->created_at = $oldBooking->created_at;
                            $orderItem->save();

                            $nameParts = explode(' ', isset($oldStudent['namn']) ? $oldStudent['namn'] : 'Uppgift Saknas');
                            $participant = new CourseParticipant();
                            $participant->order_item_id = $orderItem->id;
                            $participant->course_id = $course->id;
                            $participant->given_name = $nameParts[0];
                            $participant->family_name = isset($nameParts[1]) ? $nameParts[1] : '';
                            $participant->social_security_number = isset($oldStudent['personnummer']) ? $oldStudent['personnummer'] : '';
                            $participant->type = Participants::PARTICIPANT_STUDENT;
                            $participant->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * migrateIntroductionCourses
     */
    private function migrateIntroductionCourses()
    {
        $oldCourses = DB::connection('old')->table('handledarkurser')->get();

        $segment = DB::table('vehicle_segments')->where('name', Prices::INTRODUCTION_CAR)->first();
        $done = 0;
        foreach ($oldCourses as $oldCourse) {
            $done++;
            $this->progress->showProgress($done, $oldCourses->count());
            
            $oldBookings = DB::connection('old')->table('bokningar')->where('handledarkurs_id', $oldCourse->id)->get();
            $seatsTaken = 0;
            foreach ($oldBookings as $oldBooking) {
                $seatsTaken += $oldBooking->handledare + $oldBooking->elever;
            }

            $school = DB::table('schools')->where('id', $oldCourse->trafikskola_id)->first();
            if ($school) {
                $course = new Course();
                $course->vehicle_segment_id = $segment->id;
                $course->school_id = $oldCourse->trafikskola_id;
                $course->city_id = $oldCourse->stad_id;
                $course->start_time = $oldCourse->tidpunkt;
                $course->length_minutes = $oldCourse->langd;
                $course->price = $oldCourse->pris;
                $course->address = $oldCourse->plats;
                $course->address_description = $oldCourse->platsbeskrivning;
                $course->description = $oldCourse->beskrivning;
                $course->confirmation_text = $oldCourse->meddelande;
                $course->seats = $oldCourse->platser - $seatsTaken;
                $course->created_at = $oldCourse->created_at;
                $course->save();

                $oldBookings = DB::connection('old')->table('bokningar')->where('handledarkurs_id', $oldCourse->id)->get();

                foreach ($oldBookings as $oldBooking) {
                    $user = DB::table('users')->where('email', $oldBooking->epost)->first();
                    if (!$user) {
                        $user = new User();
                        $user->email = $oldBooking->epost;
                        $user->phone_number = $oldBooking->telefon;
                        $user->role_id = Roles::ROLE_STUDENT;
                        $user->confirmed = true;
                        $user->created_at = $oldBooking->created_at;
                        $data = $user->attributesToArray();
                        $data['password'] = uniqid();
                        unset($data['name']);
                        $user->id = DB::table('users')->insertGetId($data);
                    }

                    $order = new Order();
                    $order->user_id = $user->id;
                    $order->school_id = $course->school_id;

                    $order->payment_method = Payment::DEPRECATED;
                    $order->created_at = $oldBooking->created_at;
                    $order->handled = $order->created_at->isFuture() ? false : true;
                    $order->invoice_sent = true;
                    $data = $order->attributesToArray();
                    unset($data['invoice_amount']);
                    unset($data['paid']);
                    unset($data['can_be_cancelled']);
                    unset($data['order_value']);
                    $order->id = DB::table('orders')->insertGetId($data);

                    $oldParticipants = json_decode($oldBooking->referenser, true);
                    if (isset($oldParticipants['handledare']) && count($oldParticipants['handledare'])) {
                        foreach ($oldParticipants['handledare'] as $oldTutor) {
                            $orderItem = new OrderItem();
                            $orderItem->school_id = $order->school_id;
                            $orderItem->order_id = $order->id;
                            $orderItem->course_id = $course->id;
                            $orderItem->amount = $course->price;
                            $orderItem->quantity = 1;
                            $orderItem->type = $course->segment->name;
                            $orderItem->provision = 0;
                            $orderItem->created_at = $oldBooking->created_at;
                            $orderItem->delivered = $orderItem->created_at->isFuture() ? false : true;
                            $orderItem->save();

                            $nameParts = explode(' ', isset($oldTutor['namn']) ? $oldTutor['namn'] : 'Uppgift Saknas');
                            $participant = new CourseParticipant();
                            $participant->order_item_id = $orderItem->id;
                            $participant->course_id = $course->id;
                            $participant->given_name = $nameParts[0];
                            $participant->family_name = isset($nameParts[1]) ? $nameParts[1] : '';
                            $participant->social_security_number = isset($oldTutor['personnummer']) ? $oldTutor['personnummer'] : '';
                            $participant->type = Participants::PARTICIPANT_TUTOR;
                            $participant->save();
                        }
                    }

                    if (isset($oldParticipants['elev']) && count($oldParticipants['elev'])) {
                        foreach ($oldParticipants['elev'] as $oldStudent) {
                            $orderItem = new OrderItem();
                            $orderItem->school_id = $order->school_id;
                            $orderItem->order_id = $order->id;
                            $orderItem->course_id = $course->id;
                            $orderItem->amount = $course->price;
                            $orderItem->quantity = 1;
                            $orderItem->type = $course->segment->name;
                            $orderItem->provision = 0;
                            $orderItem->delivered = 1;
                            $orderItem->created_at = $oldBooking->created_at;
                            $orderItem->save();

                            $nameParts = explode(' ', isset($oldStudent['namn']) ? $oldStudent['namn'] : 'Uppgift Saknas');
                            $participant = new CourseParticipant();
                            $participant->order_item_id = $orderItem->id;
                            $participant->course_id = $course->id;
                            $participant->given_name = $nameParts[0];
                            $participant->family_name = isset($nameParts[1]) ? $nameParts[1] : '';
                            $participant->social_security_number = isset($oldStudent['personnummer']) ? $oldStudent['personnummer'] : '';
                            $participant->type = Participants::PARTICIPANT_STUDENT;
                            $participant->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $school
     * @param $oldSchool
     */
    private function addVehicles($school, $oldSchool)
    {
        DB::table('schools_vehicles')->where('school_id', $school->id)->delete();

        if ($oldSchool->bil_korlektion_pris) {
            DB::table('schools_vehicles')->insert(['school_id' => $school->id, 'vehicle_id' => 1]);
        }

        if ($oldSchool->mc_korlektion_pris) {
            DB::table('schools_vehicles')->insert(['school_id' => $school->id, 'vehicle_id' => 2]);
        }

        if ($oldSchool->moped_paket) {
            DB::table('schools_vehicles')->insert(['school_id' => $school->id, 'vehicle_id' => 3]);
        }
    }
}
