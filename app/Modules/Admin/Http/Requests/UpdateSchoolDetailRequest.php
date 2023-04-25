<?php namespace Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Jakten\Models\Vehicle;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class UpdateSchoolDetailRequest
 * @package Admin\Http\Requests
 */
class UpdateSchoolDetailRequest extends BaseFormRequest
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
        $vehicles = Vehicle::get()->pluck('id')->all();

        return [
            'organization_id' => ['sometimes', Rule::exists('organizations', 'id'), 'nullable'],
            'city_id' => ['required', Rule::exists('cities', 'id')],
            'name' => ['required', 'string'],
            'address' => ['required', 'string'],
            'zip' => ['required', 'string'],
            'postal_city' => ['required', 'string'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'phone_number' => ['required', 'phone_number'],
            'contact_email' => ['required'],
            'booking_email' => ['sometimes'],
            'website' => ['sometimes'],
            'description' => ['sometimes'],
            'logo' => 'sometimes|image|mimes:jpg,jpeg,png|max:5000|dimensions:min_width=128,min_height=80|nullable',
            'accepts_gift_card' => ['sometimes', 'nullable'],
            'vehicles' => ['array', Rule::in($vehicles)],
            'default_course_description' => ['string', 'nullable'],
            'default_course_confirmation_text' => ['string', 'nullable'],
            'not_member' => ['sometimes'],
            'top_deal' => ['sometimes'],
            'show_left_seats' => ['sometimes'],
            'left_seats' => ['sometimes'],
            'host_digital' => ['sometimes'],
            'bankgiro_number' => ['required', 'string'],
            'organization_number' => ['required', 'string'],
            'moms_reg_nr' => ['required', 'string'],
            'top_partner' => ['sometimes'],
            'connected_to' => ['sometimes'],
            'loyalty_fixed_amount' => ['required', 'numeric'],
        ];
    }
}
