<?php namespace Organization\Http\Requests;

use Auth;
use Illuminate\Validation\Rule;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class StoreUserRequest
 * @package Organization\Http\Requests
 */
class StoreUserRequest extends BaseFormRequest
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
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'given_name' => ['required', 'string'],
            'family_name' => ['required', 'string'],
            'phone_number' => ['required', 'phone_number'],
        ];
    }
}
