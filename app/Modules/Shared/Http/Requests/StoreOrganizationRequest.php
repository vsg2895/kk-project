<?php namespace Shared\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Class StoreOrganizationRequest
 * @package Shared\Http\Requests
 */
class StoreOrganizationRequest extends BaseFormRequest
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
            'name' => ['required', Rule::unique('organizations', 'name')],
            'org_number' => ['required', Rule::unique('organizations', 'org_number'), 'org_number'],
            'given_name' => ['required'],
            'family_name' => ['required'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'phone_number' => ['required', 'phone_number'],
            'claim' => ['sometimes', 'nullable', Rule::exists('schools', 'id')],
            'school.name' => ['required'],
            'school.phone_number' => ['required', 'phone_number'],
            'school.contact_email' => ['required', 'email'],
            'school.city_id' => ['required', 'digits_between:0,300'],
            'terms' => ['required']
        ];
    }
}
