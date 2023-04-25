<?php
namespace Jakten\Services\Payment\Klarna;

use Jakten\Models\Organization;
use Jakten\Models\School;
use Jakten\Models\User;

class KlarnaNativeOnboarding
{
    public $data;
    /**
     * @var School
     */
    public $school;
    /**
     * @var Organization
     */
    public $organization;
    /**
     * @var User
     */
    public $user;

    /**
     * KlarnaNativeOnboardingPerson constructor.
     *
     * @param Organization $organization
     */
    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
        $this->school = $organization->schools->first();
        $this->user = $organization->users->first();
        $this->buildData();
    }

    public function buildData()
    {
        $orgNumber = substr_replace($this->organization->org_number, '-', 6, 0);
        $this->data = [
            'company' => [
                'company_identification_number' => $orgNumber,
            ],
            'admin_email' => $this->user->email,
            'admin_phone' => $this->user->phone_number,
            'admin_mobile_phone' => null,
            'admin_given_name' => $this->user->given_name,
            'admin_family_name' => $this->user->family_name,
            'stores' => [
                [
                    'brand' => $this->organization->name,
                    'reference' => $this->organization->id,
                    'website_url' => isset($this->school) ? route('shared::schools.show', ['citySlug' => $this->school->city->slug, 'schoolSlug' => $this->school->slug]) : null,
                    'customer_service_email' => isset($this->school) ? $this->school->contact_email : null,
                    'customer_service_phone' => isset($this->school) ? $this->school->phone_number : null,
                    'countries_to_support' => ['SE'],
                    'existing_merchant_ids' => [],
                    'pricing_plan_id' => 'L800',
                ],
            ],
            'callback_url' => route('public::klarna.onboarding', ['organizationId' => $this->organization->id]),
            'update_callback_url' => route('public::klarna.onboarding.update', ['organizationId' => $this->organization->id]),
        ];
    }
}
