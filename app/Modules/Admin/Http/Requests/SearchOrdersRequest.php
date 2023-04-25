<?php namespace Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SearchOrdersRequest
 * @package Admin\Http\Requests
 */
class SearchOrdersRequest extends FormRequest
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
            'school'     => 'integer',
            'is_kkj_klarna' => ['sometimes', 'string', 'nullable'],
            'start_time' => 'nullable|date|date_format:Y-m-d|before:end_time',
            'end_time'   => 'nullable|date|date_format:Y-m-d|after:start_time',
        ];
    }
}
