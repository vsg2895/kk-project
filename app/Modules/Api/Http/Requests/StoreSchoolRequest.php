<?php namespace Api\Http\Requests;

use Illuminate\Validation\Rule;
use Jakten\Facades\Auth;
use Jakten\Models\Vehicle;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class StoreSchoolRequest
 * @package Api\Http\Requests
 */
class StoreSchoolRequest extends BaseFormRequest
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
            'zip' => ['required', 'string'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'postal_city' => ['required', 'string'],
            'phone_number' => ['required', 'phone_number'],
            'contact_email' => ['required', 'email'],
            'booking_email' => ['sometimes', 'email'],
            'website' => ['sometimes'],
            'description' => ['sometimes', 'nullable'],
            'vehicles' => ['required', 'array', Rule::in($vehicles)]
        ];
    }
}
