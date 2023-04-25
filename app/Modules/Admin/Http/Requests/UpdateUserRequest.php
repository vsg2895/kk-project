<?php namespace Admin\Http\Requests;

use Auth;
use Illuminate\Validation\Rule;
use Jakten\Helpers\Roles;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class UpdateUserRequest
 * @package Admin\Http\Requests
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
        $roleTypes = Roles::getAvailableRoles()->toArray();
        $userId = $this->route('id');
        $user = Auth::user();

        $rules = [
            'organization_id' => ['required_if:type,' . Roles::ROLE_ORGANIZATION_USER, Rule::exists('organizations', 'id')],
            'given_name' => ['required', 'string'],
            'family_name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'phone_number' => ['required', 'phone_number'],
            'role_id' => ['required', Rule::in($roleTypes)],
        ];

        if ($userId == $user->id) {
            $rules['password_old'] = 'required_with:password,password_confirmation|nullable|password_match:' . $user->password;
            $rules['password'] = 'required_with:password_old|nullable|min:6|max:255|confirmed';
        }

        return $rules;
    }
}
