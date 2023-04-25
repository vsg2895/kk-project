<?php namespace Api\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class StoreRatingRequest
 * @package Api\Http\Requests
 */
class StoreRatingRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->isStudent();
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
            'title' => ['required'],
            'content' => ['required']
        ];
    }
}
