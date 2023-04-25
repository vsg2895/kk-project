<?php namespace Organization\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateSchoolPricesRequest
 * @package Organization\Http\Requests
 */
class UpdateSchoolPricesRequest extends FormRequest
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
            'addons.*.id' => ['sometimes', Rule::exists('addons', 'id'), 'nullable'],
            'addons.*.price' => ['required_with:addons.*.id', 'numeric', 'min:0', 'nullable', 'max:'.PHP_INT_MAX],
            'addons.*.description' => ['sometimes:addons.*.id', 'string', 'min:5', 'nullable'],
            'prices.*.amount' => ['required_with:prices.*.quantity', 'numeric', 'nullable', 'min:' . PHP_INT_MIN, 'max:'.PHP_INT_MAX],
            'prices.*.quantity' => ['sometimes', 'required_with:prices.*.amount', 'integer', 'min:1', 'nullable', 'max:'.PHP_INT_MAX],
        ];
    }
}
