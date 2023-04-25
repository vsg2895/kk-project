<?php namespace Student\Http\Requests;

use Shared\Http\Requests\BaseFormRequest;

/**
 * Class UpdateRatingRequest
 * @package Student\Http\Requests
 */
class UpdateRatingRequest extends BaseFormRequest
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
            'rating' => ['required', 'integer', 'between:1,5'],
        ];
    }
}
