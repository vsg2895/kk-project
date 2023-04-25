<?php namespace Organization\Http\Requests;

use Jakten\Facades\Auth;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class UpdateOrganizationRequest
 * @package Organization\Http\Requests
 */
class UpdateOrganizationRequest extends BaseFormRequest
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
            'name' => ['required', 'string'],
            'org_number' => ['required', 'unique:organizations,org_number,' . Auth::user()->organization->id, 'org_number'],
            'logo' => 'sometimes|image|mimes:jpg,jpeg,png|max:5000|dimensions:min_width=128,min_height=80|nullable',
            'address' => ['required', 'string']
        ];
    }
}
