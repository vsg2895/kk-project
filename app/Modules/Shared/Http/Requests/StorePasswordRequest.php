<?php namespace Shared\Http\Requests;

use Jakten\Facades\Auth;

/**
 * Class StorePasswordRequest
 * @package Shared\Http\Requests
 */
class StorePasswordRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => ['required', 'min:6', 'max:255', 'confirmed'],
        ];
    }
}
