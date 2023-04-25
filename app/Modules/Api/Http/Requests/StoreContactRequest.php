<?php namespace Api\Http\Requests;

use Shared\Http\Requests\BaseFormRequest;

/**
 * Class StoreContactRequest
 * @package Api\Http\Requests
 */
class StoreContactRequest extends BaseFormRequest
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
        $rules = [
            'email' => ['required', 'email'],
            'name' => ['required'],
            'title' => ['required'],
            'message' => ['required'],
        ];

        return $rules;
    }
}
