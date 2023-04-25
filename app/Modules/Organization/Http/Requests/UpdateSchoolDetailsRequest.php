<?php namespace Organization\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Jakten\Models\Vehicle;

/**
 * Class UpdateSchoolDetailsRequest
 * @package Organization\Http\Requests
 */
class UpdateSchoolDetailsRequest extends FormRequest
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
            'city_id' => ['required', Rule::exists('cities', 'id')],
            'name' => ['required', 'string'],
            'address' => ['required', 'string'],
            'coaddress' => ['sometimes', 'nullable'],
            'zip' => ['required', 'string'],
            'postal_city' => ['required', 'string'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'phone_number' => ['required', 'phone_number'],
            'contact_email' => ['required', 'email'],
            'booking_email' => ['sometimes', 'email'],
            'website' => ['sometimes'],
            'description' => ['sometimes', 'nullable'],
            'logo' => 'sometimes|image|mimes:jpg,jpeg,png|max:5000|dimensions:min_width=128,min_height=80|nullable',
            'accepts_gift_card' => ['sometimes', 'nullable'],
            'host_digital' => ['sometimes', 'nullable'],
            'vehicles' => ['required', 'array', Rule::in($vehicles)],
            'default_course_description' => ['string', 'nullable'],
            'default_course_confirmation_text' => ['string', 'nullable'],
        ];
    }

    public function prepareForValidation()
    {
        $input = $this->only(array_keys($this->rules()));

        $this->replace($input);
    }
}
