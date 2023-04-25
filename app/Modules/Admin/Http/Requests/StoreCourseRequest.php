<?php namespace Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class StoreCourseRequest
 * @package Admin\Http\Requests
 */
class StoreCourseRequest extends BaseFormRequest
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
        $oldPriceMinValue = $this->price + ($this->price * 5 / 100);

        return [
            'vehicle_segment_id' => ['required', Rule::exists('vehicle_segments', 'id')],
            'school_id' => ['required', Rule::exists('schools', 'id')],
            'city_id' => ['required', Rule::exists('cities', 'id')],
            'start_time' => ['required', 'date_format:Y-m-d H:i', 'after:now'],
            'length_minutes' => ['required', 'integer', 'min:1', 'max:'.PHP_INT_MAX],
            'price' => ['required', 'numeric', 'min:1', 'max:'.PHP_INT_MAX],
            'old_price' => ['nullable', 'numeric', 'max:'.PHP_INT_MAX, 'min:' . $oldPriceMinValue],
            'address' => ['required', 'string'],
            'zip' => ['required', 'string'],
            'postal_city' => ['required', 'string'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'address_description' => ['sometimes', 'string', 'nullable'],
            'description' => ['sometimes', 'string', 'nullable'],
            'part' => ['sometimes', 'string', 'nullable'],
            'confirmation_text' => ['sometimes', 'string', 'nullable'],
            'seats' => ['required', 'integer', 'min:1', 'max:'.PHP_INT_MAX],
            'transmission' => ['sometimes', 'string', 'nullable'],
        ];
    }
}
