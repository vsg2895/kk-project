<?php namespace Api\Http\Requests;

use Shared\Http\Requests\BaseFormRequest;

/**
 * Class RateSchoolRequest
 * @package Api\Http\Requests
 */
class RateSchoolRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() && auth()->user()->isStudent();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'value' => ['required', 'between:1,5'],
        ];

        return $rules;
    }
}
