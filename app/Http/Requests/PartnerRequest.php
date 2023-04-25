<?php

namespace Jakten\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Jakten\Facades\Auth;

class PartnerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = Auth::user();
        return $user->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'partner' => 'required|between:5,30',
            'short_description' => 'required|between:10,100'
        ];
    }
}
