<?php namespace Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreSchoolImageRequest
 * @package Admin\Http\Requests
 */
class StoreSchoolImageRequest extends FormRequest
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20048',
            'file_name' => 'required',
            'alt_text' => 'required',

        ];
    }
}
