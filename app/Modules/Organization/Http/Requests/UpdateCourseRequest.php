<?php namespace Organization\Http\Requests;

use Illuminate\Validation\Rule;
use Jakten\Facades\Auth;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class UpdateCourseRequest
 * @package Organization\Http\Requests
 */
class UpdateCourseRequest extends BaseFormRequest
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
        $schools = Auth::user()->organization->schools->pluck('id')->all();
        $oldPriceMinValue = $this->price + ($this->price * 5 / 100);

        return [
            'school_id' => ['required', Rule::in($schools)],
            'city_id' => ['required', Rule::exists('cities', 'id')],
            'length_minutes' => ['required', 'integer', 'min:1', 'max:'.PHP_INT_MAX],
            'price' => ['required', 'numeric', 'max:'.PHP_INT_MAX],
            'old_price' => ['nullable', 'numeric', 'max:'.PHP_INT_MAX, 'min:' . $oldPriceMinValue],
            'address' => ['required', 'string'],
            'zip' => ['required', 'string'],
            'postal_city' => ['required', 'string'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'address_description' => ['sometimes', 'string', 'nullable'],
            'description' => ['sometimes', 'string', 'nullable'],
            'confirmation_text' => ['sometimes', 'string', 'nullable'],
            'seats' => ['required', 'integer', 'min:0', 'max:'.PHP_INT_MAX],
        ];
    }
}
