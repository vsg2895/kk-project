<?php namespace Jakten\Services;

use Carbon\Carbon;
use Roumen\Sitemap\Sitemap;
use Jakten\Models\{City, School, Course, Vehicle, CourseParticipant, Page};

/**
 * Class SitemapService
 * @package Jakten\Services
 */
class SitemapService
{
    /**
     * @var Sitemap
     */
    protected $sitemap;

    /**
     * SitemapService constructor.
     * @param Sitemap $sitemap
     */
    public function __construct(Sitemap $sitemap)
    {
        $this->sitemap = $sitemap;
    }

    /**
     * @param string $format
     * @param string $filename
     * @param null $path
     * @param null $style
     */
    public function buildSitemap($format = 'xml', $filename = 'sitemap', $path = null, $style = null)
    {
        $this->buildSchoolLevel();
        $this->buildSearchCourses();
        $this->buildSearchCourseType();
        $this->buildPage();

        $this->sitemap->store($format, $filename, $path, $style);
    }

    /**
     * buildSearchCourses
     */
    protected function buildSearchCourses()
    {
        $courseTypes = [
            'NN',
            'teorilektion',
            'introduktionskurs',
            'riskettan'
        ];
        $cites = $this->getCites();
        foreach ($cites as $city) {
            foreach ($courseTypes as $courseType) {
                $q = ['slug' => $city->slug];
                if ($courseType != 'NN') {
                    $q['courseType'] = $courseType;
                }
                $url = route('shared::search.courses', $q, true);
                $this->sitemap->add($url);
            }
        }
    }

    /**
     * buildSearchCourseType
     */
    protected function buildSearchCourseType()
    {
        // /s/trafikskolor
        $vehicles = $this->getVehicle();
        foreach ($vehicles as $vehicle) {
            $q = [];
            if ($vehicle->id != 1) {
                $q['vehicle_id'] = $vehicle->id;
            }
            $url = route('shared::schools.index', $q, true);
            $this->sitemap->add($url);
        }

        $courseTypes = [
            'introduktionskurs',
            'riskettan',
            'teorilektion',
            'risktvaan',
            //'mopedkurs',
            'riskettanmc'
        ];

        foreach ($courseTypes as $courseType) {
            $url = route('shared::' . $courseType, [], true);
            $this->sitemap->add($url);
        }

    }

    /**
     * buildPage
     */
    protected function buildPage()
    {
        $pages = $this->getPages();
        foreach ($pages as $page) {
            $q = ['uri' => $page->uri()->first()->uri];

            $url = route('shared::page.show', $q, true);
            $this->sitemap->add($url, $page->updated_at, 0.5);
        }
    }

    /**
     * buildSchoolLevel
     */
    protected function buildSchoolLevel()
    {
        $citys = $this->getCites();
        foreach ($citys as $city) {
            $schools = $this->getSchoolInCity($city);
            if (count($schools)) {
                $vehicles = $this->getVehicle();
                foreach ($vehicles as $vehicle) {
                    $q = ['citySlug' => $city->slug];
                    if ($vehicle->id != 1) {
                        $q['vehicle_id'] = $vehicle->id;
                        $url = route('shared::search.schools', $q, true);
                        $this->sitemap->add($url);
                    }
                }
                $cityUrl = route('shared::search.schools', ['citySlug' => $city->slug], true);
                $this->sitemap->add($cityUrl, $schools[0]->updated_at, 0.5, 'monthly');
                foreach ($schools as $school) {
                    $schoolUrl = route('shared::schools.show',
                        [
                            'citySlug' => $city->slug,
                            'schoolSlug' => $school->slug
                        ], true);

                    $courses = $this->getCourseInCityInSchool($school, $city);

                    $this->sitemap->add($schoolUrl,
                        $school->updated_at,
                        $this->getPriority($courses, $school->updated_at),
                        $this->getFreq($courses, $school->updated_at)
                    );
                    foreach ($courses as $course) {
                        if ($course->seats) {
                            $courseUrl = route('shared::courses.show',
                                [
                                    'citySlug' => $city->slug,
                                    'schoolSlug' => $school->slug,
                                    'courseId' => $course->id,
                                ], true);

                            $this->sitemap->add($courseUrl,
                                $this->getLastBookingOrCourseDate($course),
                                $this->getPriority($course->seats, $course->start_time, 'course'),
                                $this->getFreq($course->seats, $course->start_time, 'course')
                            );
                        }
                    }
                }
            }
        }

    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    protected function getPages()
    {
        return Page::all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    protected function getCites()
    {
        return City::query()
            ->whereNotNull('slug')
            ->where('slug', '!=', 'null')
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    protected function getVehicle()
    {
        return Vehicle::all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    protected function getSchools()
    {
        return School::all();
    }

    /**
     * @param City $city
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    protected function getSchoolInCity(City $city)
    {
        return School::query()
            ->where('city_id', '=', $city->id)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @param School $school
     * @param City $city
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    protected function getCourseInCityInSchool(School $school, City $city)
    {
        return Course::query()
            ->where('school_id', '=', $school->id)
            ->where('city_id', '=', $city->id)
            ->where('start_time', '>', Carbon::now())
            ->get();
    }

    /**
     * @param $input
     * @return int
     */
    private function normalize($input)
    {
        if (is_array($input)) {
            return count($input);
        } elseif (is_numeric($input)) {
            return $input;
        } else {
            return 0;
        }
    }

    /**
     * @param Course $course
     * @return Carbon
     */
    protected function getLastBookingOrCourseDate(Course $course)
    {
        $courseParticipant = CourseParticipant::query()
            ->where('course_id', '=', $course->id)
            ->orderByDesc('created_at')
            ->first(['created_at']);
        if (is_null($courseParticipant)) {
            return $course->updated_at;
        }

        return $courseParticipant->created_at;
    }

    /**
     * @param $count
     * @param null $updated
     * @param string $type
     * @return float
     */
    protected function getPriority($count, $updated = null, $type = 'schools')
    {
        $count = $this->normalize($count);
        if ($type == 'schools') {
            if ($count) {
                return 1.0;
            }
            return 0.5;
        } elseif ($type == 'course') {
            if (Carbon::now()->addWeek(1)->lte($updated)) {
                return 1.0;
            }
            return 0.7;
        } else {
            return 0.5;
        }
    }

    /**
     * @param $count
     * @param Carbon|null $updated
     * @param string $type
     * @return null|string
     */
    protected function getFreq($count, Carbon $updated = null, $type = 'schools')
    {
        $count = $this->normalize($count);
        if ($type == 'schools') {
            if ($count) {
                return 'monthly';
            }
            return 'hourly';
        } elseif ($type == 'course') // $updated -> start_time
        {
            if (Carbon::now()->addWeek(1)->lte($updated)) {
                return 'weekly';
            }
            return 'hourly';
        } else {
            return null;
        }
    }
}
