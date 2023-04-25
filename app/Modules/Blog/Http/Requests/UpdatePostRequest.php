<?php namespace Blog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePostRequest
 *
 * @package Blog\Http\Requests
 */
class UpdatePostRequest extends FormRequest
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
            'title'                       => 'required|string|max:255',
            'content'                     => 'required|string|max:40000',
            'footer_content'              => 'nullable|string|max:40000',
            'button_text'                 => 'nullable|string|max:100',
            'link'                        => 'nullable|string|max:255',
            'alt_text'                    => 'max:255',
            'status'                      => 'integer',
            'preview_img_filename'        => 'file|image|mimes:jpeg,png',
            'image'                       => 'file|mimes:jpeg,png',
            'video'                       => 'string|url',
        ];
    }
}
