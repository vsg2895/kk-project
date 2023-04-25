<?php
namespace Jakten\Services\Payment\Requests;

use Jakten\Models\Organization;
use Jakten\Models\School;
use Jakten\Models\User;

class CreateKlarnaCompany
{
    /**
     * @var School
     */
    protected $school;
    /**
     * @var Organization
     */
    protected $organization;
    /**
     * @var User
     */
    protected $user;

    /**
     * CreateKlarnaCompany constructor.
     *
     * @param School $school
     */
    public function __construct(School $school)
    {
        $this->school = $school;
        $this->organization = $school->organization;
        $this->user = $this->organization->users->first();
    }

    public function getData()
    {
        return [
            'company' => [
                'company_identification_number' => $this->organization->org_number,
            ],
            'admin_email' => $this->school->contact_email,
            'admin_phone' => $this->school->phone_number,
            'admin_given_name' => $this->user->given_name,
            'admin_family_name' => $this->user->family_name,
            'stores' => [
                'brand' => null,
                'reference' => $this->organization->id,
                'website_url' => $this->school->website,
                'customer_service_url' => $this->school->contact_email,
                'customer_service_phone' => $this->school->phone_number,
                'countries_to_support' => ['SE'],
                'existing_merchant_ids' => [],
                'pricing_plan_id' => '',
            ],
            'callback_url' => route('api::klarna.onboarding.created', ['id' => $this->organization->id]),
            'update_callback_url' => route('api::klarna.onboarding.updated', ['id' => $this->organization->id]),
        ];
    }
}
