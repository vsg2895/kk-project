<?php namespace Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Class StoreOrderRequest
 * @package Api\Http\Requests
 */
class StoreOrderRequest extends FormRequest
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
            'students.*.given_name' => ['sometimes', 'min:2'],
            'students.*.family_name' => ['sometimes', 'min:2'],
            'students.*.social_security_number' => ['sometimes', 'org_number'],
            'tutors.*.given_name' => ['sometimes', 'min:2'],
            'tutors.*.family_name' => ['sometimes', 'min:2'],
            'tutors.*.social_security_number' => ['sometimes', 'org_number'],
        ];
        
        if (!Auth::check()) {
            $rules['new_user.given_name'] = ['required'];
            $rules['new_user.family_name'] = ['required'];
            $rules['new_user.phone_number'] = ['required', 'phone_number'];
            $rules['new_user.email'] = ['required', 'email', Rule::unique('users', 'email')];
        }

        return $rules;
    }
}
