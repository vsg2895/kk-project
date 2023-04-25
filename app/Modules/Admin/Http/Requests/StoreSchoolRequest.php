<?php namespace Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Jakten\Models\Vehicle;

/**
 * Class StoreSchoolRequest
 * @package Admin\Http\Requests
 */
class StoreSchoolRequest extends FormRequest
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
            'organization_id' => ['sometimes', Rule::exists('organizations', 'id')],
            'city_id' => ['required', Rule::exists('cities', 'id')],
            'name' => ['required', 'string', Rule::unique('schools', 'name')],
            'address' => ['required', 'string'],
            'zip' => ['required', 'string'],
            'postal_city' => ['required', 'string'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'phone_number' => ['required', 'phone_number'],
            'contact_email' => ['required', 'email'],
            'booking_email' => ['sometimes', 'email'],
            'website' => ['sometimes'],
            'description' => ['sometimes', 'nullable'],
            'addons.*.id' => ['sometimes', Rule::exists('addons', 'id')],
            'addons.*.price' => ['required_with:addons.*.id', 'numeric', 'min:0', 'max:'.PHP_INT_MAX],
            'addons.*.description' => ['sometimes', 'string', 'min:5'],
            'logo' => 'sometimes|image|mimes:jpg,jpeg,png|max:5000|dimensions:min_width=128,min_height=80',
            'vehicles' => ['array', Rule::in($vehicles)],
            'bankgiro_number' => ['required', 'string'],
            'organization_number' => ['required', 'string'],
            'moms_reg_nr' => ['required', 'string'],
        ];
    }
}
