<?php namespace Admin\Http\Requests;

use Illuminate\Validation\Rule;
use Jakten\Models\Course;
use Shared\Http\Requests\BaseFormRequest;

class UpdateOrderRequest extends BaseFormRequest
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

    protected function prepareForValidation()
    {
        $this->offsetUnset('*');
        if (is_null($this->course_id)) {
            $this->request->add(['validCourse' => true]);
        } else {
            $courseSchoolId = Course::where('id', '=', $this->course_id)->withTrashed()->pluck('school_id')->first();
            $this->request->add(['validCourse' => !is_null($courseSchoolId) && $courseSchoolId == $this->school_id ? true : null]);
        }

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
//        dd($this->request);
        return [
            'school_id' => ['required', Rule::exists('schools', 'id')],
            'course_id' => ['nullable'],
            'validCourse' => ['required', 'boolean'],
            'partisipants' => ['bail', 'nullable', 'array'],
            'partisipants.*.id' => ['bail', 'nullable', Rule::exists('course_participants', 'id')],
            'partisipants.*.name' => ['bail', 'nullable', 'string'],
            'partisipants.*.ssn' => ['bail', 'nullable'],
            'partisipants.*.email' => ['bail', 'nullable', 'email'],

            'addons' => ['bail', 'nullable', 'array'],
            'addons.*.id' => ['bail', 'nullable', Rule::exists('course_participants', 'id')],
            'addons.*.name' => ['bail', 'nullable', 'string'],
            'addons.*.ssn' => ['bail', 'nullable'],
            'addons.*.email' => ['bail', 'nullable', 'email'],
        ];
    }
}
