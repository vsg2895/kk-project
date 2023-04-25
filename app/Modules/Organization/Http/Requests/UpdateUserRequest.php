<?php namespace Organization\Http\Requests;

use Jakten\Facades\Auth;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class UpdateUserRequest
 * @package Organization\Http\Requests
 */
class UpdateUserRequest extends BaseFormRequest
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
        $user = Auth::user();

        return [
            'given_name' => ['required', 'string'],
            'family_name' => ['required', 'string'],
            'phone_number' => ['required', 'phone_number'],
            'password_old' => 'required_with:password,password_confirmation|nullable|password_match:' . $user->password,
            'password' => 'required_with:password_old|min:6|max:255|confirmed|nullable',
        ];
    }
}
