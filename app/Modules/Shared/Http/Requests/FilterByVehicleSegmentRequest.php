<?php namespace Jakten\Modules\Shared\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UspsRequest
 * @package Admin\Http\Requests
 */
class FilterByVehicleSegmentRequest extends FormRequest
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
            'vehicle_segment_id' => ['nullable', Rule::exists('vehicle_segments', 'id')],
        ];
    }
}
