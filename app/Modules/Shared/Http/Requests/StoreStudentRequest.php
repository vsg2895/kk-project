<?php namespace Shared\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Class StoreStudentRequest
 * @package Shared\Http\Requests
 */
class StoreStudentRequest extends BaseFormRequest
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
            'given_name' => ['required'],
            'family_name' => ['required'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'phone_number' => ['required', 'phone_number'],
            'terms' => ['required']
        ];
    }
}
