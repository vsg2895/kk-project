<?php namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Jakten\Repositories\Contracts\CourseRepositoryContract;
use Jakten\Repositories\Contracts\SchoolRepositoryContract;
use SKAgarwal\GoogleApi\Exceptions\GooglePlacesApiException;
use SKAgarwal\GoogleApi\PlacesApi;

/**
 * Class FetchLatLng
 * @package Jakten\Console\Commands
 */
class FetchLatLng extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kkj:fetch_lat_lng';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tries to fetch lat lng for schools/courses';

    /**
     * @var PlacesApi
     */
    private $placesApi;

    /**
     * @var SchoolRepositoryContract
     */
    private $schools;

    /**
     * @var CourseRepositoryContract
     */
    private $courses;

    /**
     * Create a new command instance.
     *
     * @param SchoolRepositoryContract $schools
     * @param CourseRepositoryContract $courses
     * @internal param PlacesApi $placesApi
     */
    public function __construct(SchoolRepositoryContract $schools, CourseRepositoryContract $courses)
    {
        parent::__construct();
        $this->schools = $schools;
        $this->placesApi = new PlacesApi(config('google.places.key'));
        $this->courses = $courses;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $which = $this->choice('Välj typ', ['Skolor', 'Kurser'], false);

        if ($which) {
            if($which === 'Skolor') {
                $this->setForSchools();
            } else {
                $this->setForCourses();
            }
        } else {
            $this->error('Ogitligt val');
        }
    }

    /**
     * SetForSchool
     */
    private function setForSchools()
    {
        try {
            $schools = $this->schools->query()->whereNull('latitude')->orWhereNull('longitude')->get();

            foreach ($schools as $school) {
                $this->findLocation($school);
            }
        } catch (\Exception $e) {
            dd($e->getLine(), $e->getFile());
        }
    }

    /**
     * @param Model $object
     * @param $googleData
     * @throws GooglePlacesApiException
     */
    private function setData(Model $object, $googleData)
    {
        $response = $this->placesApi->placeDetails($googleData['place_id']);

        $geometry = $response->get('result')['geometry'];
        $lat = $geometry['location']['lat'];
        $lng = $geometry['location']['lng'];
        $object->latitude = $lat;
        $object->longitude = $lng;
        $object->save();
        $this->comment('Setting to ' . $googleData['description']);

    }

    /**
     * setForCourses
     */
    private function setForCourses()
    {
        $courses = $this->courses->inFuture()
            ->query()->where(function($query) {
                $query->whereNull('latitude')->orWhereNull('longitude');
            })
            ->get();

        foreach ($courses as $course) {
            $this->findLocation($course);
        }
    }

    /**
     * @param $object
     * @return bool
     */
    private function findLocation($object)
    {
        $this->info('Finding for ' . $object->address . ' ' . $object->postal_city);
        try {
            $results = $this->placesApi->placeAutocomplete($object->address . ' ' . $object->postal_city);

            if ($results['status'] === 'OK') {
                $results = new Collection($results['predictions']);
                if ($results->count() > 1) {
                    $choices = $results->pluck('description')
                        ->toArray();
                    $which = $this->choice('Välj location', $choices, false);

                    if ($which) {
                        $result = $results->where('description', $which)->first();
                        $this->setData($object, $result);
                    } else {
                        $this->error('Ogiltigt val.');

                        return false;
                    }
                } else {
                    $result = $results[0];
                    $this->setData($object, $result);
                }

            }
        } catch (GooglePlacesApiException $exception) {
            $this->error('Found no results for ' . $object->address . ' ' . $object->postal_city);
        }
    }
}
