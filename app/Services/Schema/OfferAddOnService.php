<?php namespace Jakten\Services\Schema;

use Jakten\Models\School;

/**
 * Class OfferAddOnService
 * @package Jakten\Services\Schema
 */
class OfferAddOnService extends SchemaService implements SchemaInterface
{
    /**
     * OfferAddOnService constructor.
     */
    public function __construct()
    {
        $this->properties = [];
    }

    /**
     * Try to parse object.
     *
     * @param School|mixed $data
     */
    public function tryParse($data)
    {
        if (is_object($data)) {
            if (get_class($data) == "Jakten\Models\School") {
                $this->parseSchool($data);
            }
        }
    }

    /**
     * Parse School object.
     *
     * @param School $data
     */
    public function parseSchool($data)
    {
        foreach ($data->addons as $addon)
        {
            $offer = [
                "@type" => "Offer",
                ];

            if ($this->filter($addon->pivot->price) !== null) {
                $offer["price"] = $this->filter($addon->pivot->price);
                $offer["priceCurrency"] = "SEK";
            }

            if ($this->filter($addon->pivot->description) !== null) {
                $offer["description"] = $this->filter($addon->pivot->description);
            }

            if ($this->filter($addon->name) !== null) {
                $offer["name"] = $this->filter($addon->name);
            }

            $this->properties[] = $offer;
        }
    }

    /**
     * Filter value remove empty arrays and non values.
     *
     * @param mixed $value
     * @return null|mixed
     */
    private function filter($value)
    {
        if (is_array($value)) {
            if (count($value) >= 1) {
                return $value;
            }
        } elseif (($value != "") && ($value != null)) {
            return $value;
        }
        return null;
    }
}