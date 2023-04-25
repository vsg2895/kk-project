<?php
namespace Jakten\Services\Payment\Klarna;

class KlarnaOnboardingResponse
{
    public $data;
    public $success;
    public $store;
    public $id;
    public $organization_id;
    public $merchant_id;
    public $shared_secret;
    public $rejection_reason;

    /**
     * KlarnaBillingPerson constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->id = $data['signup_id'];
        $this->success = $data['success'];
        if ($this->succeeded()) {
            $this->store = $data['stores'][0];
            $this->organization_id = $this->store['reference'];
            $this->merchant_id = $this->store['merchant_id'];
            $this->shared_secret = $this->store['shared_secret'];
        } else {
            $this->rejection_reason = $data['rejection_reason'];
        }
    }

    /**
     * @return bool
     */
    public function succeeded()
    {
        return $this->success;
    }
}
