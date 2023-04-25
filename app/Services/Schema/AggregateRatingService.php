<?php namespace Jakten\Services\Schema;

use Jakten\Models\School;

/**
 * Class AggregateRatingService
 * @package Jakten\Services\Schema
 */
class AggregateRatingService extends SchemaService implements SchemaInterface
{
    /**
     * AggregateRatingService constructor.
     */
    public function __construct()
    {
        $this->properties = [
            "@type" => "AggregateRating",
        ];
    }

    /**
     * Try to parse object.
     *
     * @param School|Array|mixed $data
     */
    public function tryParse($data)
    {
        if (is_object($data)) {
            if (get_class($data) == "Jakten\Models\School") {
                $this->parseSchool($data);
            }
        } elseif (is_array($data)) {
            $this->parseArray($data);
        }
    }

    /**
     * @param School $data
     */
    private function parseSchool($data)
    {
        if ($data->average_rating !== null) {
            $this->ratingValue = $data->average_rating;
            $this->reviewCount = $data->rating_count;
        }
    }

    /**
     * @param array $data
     */
    private function parseArray($data)
    {
        if ( array_key_exists("average_rating",$data) && ($data["average_rating"] !== null)) {
            $this->ratingValue = $data["average_rating"];
            $this->reviewCount = $data["rating_count"];
        }
    }
}