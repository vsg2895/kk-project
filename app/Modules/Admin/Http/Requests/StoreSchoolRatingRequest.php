<?php namespace Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Jakten\Facades\Auth;

/**
 * Class StoreSchoolRatingRequest
 * @package Admin\Http\Requests
 */
class StoreSchoolRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->isAdmin();
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
