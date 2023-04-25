<?php namespace Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class UpdatePageRequest
 * @package Admin\Http\Requests
 */
class UpdatePageRequest extends BaseFormRequest
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
            'uri' => ['required', Rule::unique('pages_uris', 'uri')->ignore($this->route('id'), 'page_id')],
            'title' => ['required'],
            'meta_description' => ['sometimes'],
            'content' => ['required'],
        ];
    }
}
