<?php namespace Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Jakten\Helpers\Roles;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class StoreUserRequest
 * @package Admin\Http\Requests
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
        $roleTypes = Roles::getAvailableRoles()->toArray();

        return [
            'organization_id' => ['required_if:type,' . Roles::ROLE_ORGANIZATION_USER, Rule::exists('organizations', 'id')],
            'given_name' => ['required', 'string'],
            'family_name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'phone_number' => ['required', 'phone_number'],
            'role_id' => ['required', Rule::in($roleTypes)],
        ];
    }
}
