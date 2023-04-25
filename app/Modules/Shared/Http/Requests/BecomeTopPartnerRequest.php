<?php

namespace Shared\Http\Requests;

/**
 * Class StoreOrganizationRequest
 * @package Shared\Http\Requests
 */
class BecomeTopPartnerRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'school_name' => ['required'],
            'school_email' => ['required', 'email'],
        ];
    }
}
