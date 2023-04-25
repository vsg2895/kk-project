<?php namespace Admin\Http\Requests;

use Shared\Http\Requests\BaseFormRequest;

class UpdateCityRequest extends BaseFormRequest
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
            'id' => ['required', 'numeric'],
            'best_schools_id' => ['nullable'],
            'school_description' => ['string', 'nullable'],
            'search_radius' => ['numeric', 'nullable'],
            'desc_trafikskolor' => ['string', 'nullable'],
            'desc_introduktionskurser' => ['string', 'nullable'],
            'desc_riskettan' => ['string', 'nullable'],
            'desc_teorilektion' => ['string', 'nullable'],
            'desc_risktvaan' => ['string', 'nullable'],
            'desc_riskettanmc' => ['string', 'nullable']
        ];
    }
}
