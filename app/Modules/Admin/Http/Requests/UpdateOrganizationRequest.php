<?php namespace Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class UpdateOrganizationRequest
 * @package Admin\Http\Requests
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
        $id = $this->route('id');

        return [
            'name' => ['required', 'string'],
            'org_number' => ['required', Rule::unique('organizations', 'org_number')->ignore($id)],
            'logo' => 'sometimes|image|mimes:jpg,jpeg,png|max:5000|dimensions:min_width=128,min_height=80|nullable',
            'address' => ['required', 'string']
        ];
    }
}
