<?php namespace Admin\Http\Requests;

use Shared\Http\Requests\BaseFormRequest;

/**
 * Class StoreSchoolCommentRequest
 * @package Admin\Http\Requests
 */
class StoreSchoolCommentRequest extends BaseFormRequest
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
            'message' => ['required', 'string']
        ];
    }
}
