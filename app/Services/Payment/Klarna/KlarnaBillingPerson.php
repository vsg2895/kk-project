<?php
namespace Jakten\Services\Payment\Klarna;

use Jakten\Helpers\Roles;

class KlarnaBillingPerson
{
    public $data;

    /**
     * KlarnaBillingPerson constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getEmail()
    {
        return $this->data['email'];
    }

    /**
     * @return array
     */
    public function toUserData()
    {
        return [
            'given_name' => $this->data['given_name'],
            'family_name' => $this->data['family_name'],
            'email' => $this->data['email'],
            'phone_number' => $this->data['phone'],
            'street_address' => $this->data['street_address'],
            'postal_code' => $this->data['postal_code'],
            'city' => $this->data['city'],
            'role_id' => Roles::ROLE_STUDENT,
        ];
    }
}
