<?php namespace Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UspsRequest
 * @package Admin\Http\Requests
 */
class UspsRequest extends FormRequest
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
            'school_id' => ['required', Rule::exists('schools', 'id')],
            'text' => ['required', 'string'],
        ];
    }
}
