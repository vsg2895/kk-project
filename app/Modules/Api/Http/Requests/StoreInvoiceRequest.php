<?php namespace Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreInvoiceRequest
 * @package Api\Http\Requests
 */
class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'school_id' => ['required', Rule::exists('schools', 'id')],
            'order_id' => ['sometimes', 'nullable', Rule::exists('orders', 'id')],
            'rows.*.name' => ['required'],
            'rows.*.amount' => ['required'],
            'rows.*.quantity' => ['required'],
        ];

        return $rules;
    }
}
