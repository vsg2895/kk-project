<?php namespace Jakten\Services\Schema;

use Jakten\Models\Course;

/**
 * Class OfferService
 * @package Jakten\Services\Schema
 */
class OfferService extends SchemaService implements SchemaInterface
{
    /**
     * OfferService constructor.
     */
    public function __construct()
    {
        $this->properties =
            [
                "@type" => "Offer",
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
        } elseif (is_array($data)) {
            $this->parseArray($data);
        }
    }

    /**
     * @param Course $data
     */
    public function parseArray($data)
    {
        if(array_key_exists("price", $data)) {
            $this->price = $data["price"];
            $this->priceCurrency = "SEK";
        }

        if(array_key_exists("seats", $data)) {
            $this->availability = $data["seats"];
        }

        if(array_key_exists("itemOffered", $data)) {
            $product = new ProductService();
            $product->tryParse($data["itemOffered"]);
            $this->itemOffered = $product->get();
        }
    }

    /**
     * @param Course $data
     */
    public function parseCourse($data)
    {
        $addOn = new OfferAddOnService();
        $addOn->tryParse($data->school);
        $this->price = $data->price;
        $this->validFrom = $data->created_at->toIso8601String();
        $this->availability = $data->seats;
        $this->priceCurrency = "SEK";
        $this->addOn = $addOn->get();
    }
}