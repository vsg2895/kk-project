<?php namespace Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class StorePageRequest
 * @package Admin\Http\Requests
 */
class StorePageRequest extends BaseFormRequest
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
            'uri' => ['required', Rule::unique('pages_uris', 'uri')],
            'title' => ['required'],
            'meta_description' => ['sometimes'],
            'content' => ['required'],
        ];
    }
}
