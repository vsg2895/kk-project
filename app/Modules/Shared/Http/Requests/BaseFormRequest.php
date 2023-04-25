<?php namespace Shared\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Jakten\Helpers\InputParser;

/**
 * Class BaseFormRequest
 * @package Shared\Http\Requests
 */
abstract class BaseFormRequest extends FormRequest
{
    /**
     * @return mixed
     */
    public abstract function rules();

    protected function prepareForValidation()
    {
        $keys = [];

        // determine which form fields are allowed
        foreach ($this->rules() as $key => $value) {
            $keys[] = $key;

            if ((is_array($value) && in_array('confirmed', $value)) || (is_string($value) && strpos($value, "confirmed"))) {
                $keys[] = $key . '_confirmation';
            }
        }

        $input = $this->only($keys);

        // manipulate special form input fields
        if (isset($input['org_number'])) {
            $input['org_number'] = InputParser::orgNumber($input['org_number']);
        }

        $this->replace($input);
    }
}
