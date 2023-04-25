<?php namespace Organization\Http\Requests;

use Illuminate\Validation\Rule;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class StoreSchoolSegmentPriceRequest
 * @package Organization\Http\Requests
 */
class StoreSchoolSegmentPriceRequest extends BaseFormRequest
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
            'amount' => ['required', 'min:' . PHP_INT_MIN, 'numeric', 'max:'.PHP_INT_MAX],
            'price_id' => ['required', Rule::exists('prices', 'id'),
                Rule::unique('school_segment_prices')
                    ->where('price_id', $this->request->get('price_id'))
                    ->where('school_id', $this->route('schoolId')),
            ],
            'quantity' => ['required', 'min:1', 'integer', 'max:'.PHP_INT_MAX],
            'comment' => ['sometimes'],
            'subject_to_change' => ['required', 'boolean'],

        ];
    }
}
