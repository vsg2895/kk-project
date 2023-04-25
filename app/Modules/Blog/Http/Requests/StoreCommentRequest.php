<?php namespace Blog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreCommentRequest
 *
 * @package Blog\Http\Requests
 */
class StoreCommentRequest extends FormRequest
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
            'text'    => 'required|string|max:255',
            'post_id' => 'required|integer|exists:posts,id',
        ];
    }
}
