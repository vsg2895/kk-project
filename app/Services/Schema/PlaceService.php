<?php namespace Jakten\Services\Schema;

/**
 * Class PlaceService
 * @package Jakten\Services\Schema
 */
class PlaceService extends SchemaService implements SchemaInterface
{

    /**
     * PlaceService constructor.
     */
    public function __construct()
    {
        $this->properties =
            [
                "@type" => "Place",
            ];
    }

    /**
     * Try to parse object.
     *
     * @param Course|School|mixed $data
     */
    public function tryParse($data)
    {
        if (is_object($data)) {
            if (get_class($data) == "Jakten\Models\Course") {
                $this->parseCourse($data);
            }elseif (get_class($data) == "Jakten\Models\School") {
                $this->parseSchool($data);
            }
        }
    }

    /**
     * @param School $data
     */
    private function parseCourse($data)
    {
        $postalAddress = new PostalAddressService();
        $postalAddress->tryParse($data);
        if($data->address_description){
            $this->name = $data->address_description;
        }else{
            $this->name = $data->address;
        }
        $this->address = $postalAddress->get();
    }

    /**
     * @param School $data
     */
    private function parseSchool($data)
    {
        $postalAddress = new PostalAddressService();
        $postalAddress->tryParse($data);
        if($data->address_description){
            $this->name = $data->address_description;
        }else{
            $this->name = $data->address;
        }
        $this->address = $postalAddress->get();
    }
}