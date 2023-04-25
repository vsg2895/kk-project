<?php namespace Jakten\Services\Schema;

use Jakten\Models\Course;

/**
 * Class CourseInstanceService
 * @package Jakten\Services\Schema
 */
class CourseInstanceService extends SchemaService implements SchemaInterface
{
    /**
     * CourseInstanceService constructor.
     */
    public function __construct()
    {
        $this->properties = [
            "@type" => "CourseInstance",
        ];
    }

    /**
     * Try to parse object.
     *
     * @param Course|mixed $data
     */
    public function tryParse($data)
    {
        if (is_object($data)) {
            if (get_class($data) == "Jakten\Models\Course") {
                $this->parseCourse($data);
            }
        }
    }

    /**
     * Parse Course object.
     *
     * @param Course $data
     */
    private function parseCourse($data)
    {
        $postalAddress = new PlaceService();
        $postalAddress->tryParse($data);

        $offerService = new OfferService();
        $offerService->tryParse($data);

        $provider = new LocalBusinessService();
        $provider->tryParse($data->school);

        $this->courseMode = 'onsite';
        $this->name = $data->segment->label;
        $this->startDate = $data->start_time->toIso8601String();
        $this->location = $postalAddress->get();
        $this->offers = $offerService->get();
        if ($data->length_minutes) {
            $endDate = $data->start_time;
            $endDate->addMinutes($data->length_minutes);
            $this->endDate = $endDate->toIso8601String();
        }
    }
}