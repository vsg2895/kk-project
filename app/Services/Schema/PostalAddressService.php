<?php namespace Jakten\Services\Schema;

use Jakten\Models\School;
use Jakten\Models\Course;

/**
 * Class PostalAddressService
 * @package Jakten\Services\Schema
 */
class PostalAddressService extends SchemaService implements SchemaInterface
{
    /**
     * PostalAddressService constructor.
     */
    public function __construct()
    {
        $this->properties =
            [
                "@type" => "PostalAddress",
            ];
    }

    /**
     * Try to parse object.
     *
     * @param School|Course|mixed $data
     */
    public function tryParse($data)
    {
        if (is_object($data)) {
            if (get_class($data) == "Jakten\Models\School") {
                $this->parseSchool($data);
            } elseif (get_class($data) == "Jakten\Models\Course") {
                $this->parseCourse($data);
            }
        }
    }

    /**
     * Parse School object.
     *
     * @param School $data
     */
    private function parseSchool($data)
    {
        $this->addressCountry = 'Sweden';
        if ($data->zip != "") {
            $this->postalCode = $data->zip;
        }
        if ($data->postal_city != "") {
            $this->addressLocality = $data->postal_city;
        }
        if ($data->coaddress != "") {
            $this->streetAddress = 'C/O ' . $data->coaddress . ', ';
        }
        if ($data->address != "") {
            $this->streetAddress .= $data->address;
        }
    }

    /**
     * Parse Course object.
     *
     * @param Course $data
     */
    private function parseCourse($data)
    {
        $this->addressCountry = 'Sweden';
        if ($data->zip != "") {
            $this->postalCode = $data->zip;
        }
        if ($data->postal_city != "") {
            $this->addressLocality = $data->postal_city;
        }
        if ($data->address != "") {
            $this->streetAddress .= $data->address;
        }
    }
}