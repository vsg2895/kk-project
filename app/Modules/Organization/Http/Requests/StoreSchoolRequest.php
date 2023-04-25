<?php namespace Organization\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Jakten\Facades\Auth;
use Jakten\Models\Vehicle;

/**
 * Class StoreSchoolRequest
 * @package Organization\Http\Requests
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
        return Auth::user()->isOrganizationUser();
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
            'city_id' => ['required', Rule::exists('cities', 'id')],
            'name' => ['required', 'string'],
            'address' => ['required', 'string'],
            'coaddress' => ['sometimes', 'nullable'],
            'zip' => ['required', 'string'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'postal_city' => ['required', 'string'],
            'phone_number' => ['required', 'string'],
            'contact_email' => ['required', 'email'],
            'booking_email' => ['sometimes', 'email'],
            'website' => ['sometimes'],
            'description' => ['sometimes', 'nullable'],
            'addons.*.id' => ['sometimes', Rule::exists('addons', 'id')],
            'addons.*.price' => ['required_with:addons.*.id', 'numeric', 'min:0', 'max:'.PHP_INT_MAX],
            'addons.*.description' => ['sometimes', 'string', 'min:5'],
            'logo' => 'sometimes|image|mimes:jpg,jpeg,png|max:5000|dimensions:min_width=128,min_height=80',
            'vehicles' => ['array', Rule::in($vehicles)]
        ];
    }
}
