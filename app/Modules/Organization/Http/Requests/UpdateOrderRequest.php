<?php namespace Organization\Http\Requests;

use Jakten\Facades\Auth;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class UpdateOrderRequest
 * @package Organization\Http\Requests
 */
class UpdateOrderRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->isAdmin() || Auth::user()->isOrganizationUser();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'items' => ['array'],
            'invoice_sent' => ['boolean'],
        ];
    }
}
