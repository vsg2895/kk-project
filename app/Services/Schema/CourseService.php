<?php namespace Jakten\Services\Schema;

use Jakten\Models\Course;

/**
 * Class CourseService
 * @package Jakten\Services\Schema
 */
class CourseService extends SchemaService implements SchemaInterface
{
    /**
     * @var bool
     */
    public $setProvider = true;

    /**
     * CourseService constructor.
     */
    public function __construct()
    {
        $this->properties = [
            "@type" => "Course",
        ];
    }

    /**
     * Try to parse object.
     *
     * @param Course $data
     */
    public function tryParse($data)
    {
        $courseInstance = new CourseInstanceService();
        $courseInstance->tryParse($data);
        $this->description = $data->segment->label;

        $this->name = $data->segment->label;
        $this->dateCreated = $data->created_at != null ? $data->created_at->toIso8601String() : null;
        $this->dateModified = $data->updated_at != null ? $data->updated_at->toIso8601String() : null;
        $this->datePublished = $data->created_at != null ? $data->created_at->toIso8601String() : null;
        if($this->setProvider) {
            $provider = new LocalBusinessService();
            $provider->tryParse($data->school);
            $this->provider = $provider->get();
        }
        $this->hasCourseInstance = $courseInstance->get();
    }
}
