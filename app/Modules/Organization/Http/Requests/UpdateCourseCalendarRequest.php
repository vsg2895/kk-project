<?php namespace Organization\Http\Requests;

use Illuminate\Validation\Rule;
use Jakten\Facades\Auth;
use Shared\Http\Requests\BaseFormRequest;

/**
 * Class UpdateCourseRequest
 * @package Admin\Http\Requests
 */
class UpdateCourseCalendarRequest extends BaseFormRequest
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
            'start_time' => ['required', 'date_format:Y-m-d H:i', 'after:now'],
            'price' => ['required', 'numeric', 'min:1', 'max:'.PHP_INT_MAX],
            'seats' => ['required', 'integer', 'min:0', 'max:'.PHP_INT_MAX],
        ];
    }
}
