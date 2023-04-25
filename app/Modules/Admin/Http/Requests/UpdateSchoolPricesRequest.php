<?php namespace Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateSchoolPricesRequest
 * @package Admin\Http\Requests
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
            'addons.*.price' => ['required_with:addons.*.id', 'numeric', 'min:0', 'max:'.PHP_INT_MAX, 'nullable'],
            'addons.*.description' => ['sometimes:addons.*.id', 'string', 'min:5', 'nullable'],
            'prices.*.amount' => ['required_with:prices.*.quantity', 'numeric', 'min:' . PHP_INT_MIN, 'max:'.PHP_INT_MAX, 'nullable'],
            'prices.*.quantity' => ['sometimes', 'required_with:prices.*.amount', 'integer', 'min:1', 'max:'.PHP_INT_MAX, 'nullable'],
            'customAddons.*.id' => ['sometimes', Rule::exists('custom_addons', 'id'), 'nullable'],
            'customAddons.*.price' => ['required_with:custom_addons.*.id', 'numeric', 'min:0', 'max:'.PHP_INT_MAX, 'nullable'],
            'customAddons.*.description' => ['sometimes:custom_addons.*.id', 'string', 'min:5', 'nullable'],
        ];
    }
}
