<?php namespace Jakten\Services\Schema;

use Jakten\Models\School;

/**
 * Class LocalBusinessService
 * @package Jakten\Services\Schema
 */
class LocalBusinessService extends SchemaService implements SchemaInterface
{
    /**
     * LocalBusinessService constructor.
     */
    public function __construct()
    {
        $this->properties =
            [
                "@type" => "LocalBusiness",
            ];
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
     * @param School $data
     */
    private function parseSchool($data)
    {
        $postalAddress = new PostalAddressService();
        $postalAddress->tryParse($data);

        $aggregateRating = new AggregateRatingService();
        $aggregateRating->tryParse($data);

        $this->name = $data->name;

        if ($data->booking_email != '') {
            $this->email = $data->booking_email;
        } else {
            $this->email = $data->contact_email;
        }

        $this->aggregateRating = $aggregateRating->get();
        $this->address = $postalAddress->get();
        $urlPart = 'https://www.korkortsjakten.se/';
        $this->logo =  $urlPart . '/images/kkj-logo-new.png';
        $this->image = $urlPart . '/images/kkj-logo-new.png';
        if($data->organization) {
            if ($data->organization->sign_up_status == "COMPLETED") {
                $this->paymentAccepted = "Klarna Checkout";
            }
            
            if (strlen($data->organization->org_number) == 10) {
                $this->taxID = substr($data->organization->org_number, 0, 6) .
                    '-' .
                    substr($data->organization->org_number, -4);
            }

            if(isset($data->logo)) {
                $this->logo =  $urlPart . $data->logo->path;
                $this->image = $urlPart . $data->logo->path;
            }
        }


        if ($data->phone_number != "") {
            $this->telephone = str_replace(" ", "", $data->phone_number);
        }

        if (($data->website != "") && ($data->website != "http://")) {
            $this->url = $data->website;
        }
    }
}
