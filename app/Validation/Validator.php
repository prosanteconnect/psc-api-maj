<?php

namespace App\Validation;

use Illuminate\Support\Arr;
use Illuminate\Validation\Validator as BaseValidator;

class Validator extends BaseValidator
{
    /**
     * Determine if the data passes the validation rules.
     *
     * @return bool
     */
    public function passes(): bool
    {
        // Perform the usual rules validation, but at this step ignore the
        // return value as we still have to validate the allowance of the fields
        // The error messages count will be recalculated later and returned.
        parent::passes();

        // Compute the difference between the request data as a dot notation
        // array and the attributes which have a rule in the current validator instance
        $extraAttributes = array_diff_key(
            Arr::dot($this->data),
            $this->rules
        );

        // We'll spin through each key that hasn't been stripped in the
        // previous filtering. Most likely the fields will be top level
        // forbidden values or array/object values, as they get mapped with
        // indexes other than asterisks (the key will differ from the rule
        // and won't match at earlier stage).
        // We have to do a deeper check if a rule with that array/object
        // structure has been specified.
        foreach ($extraAttributes as $attribute => $value) {
            if (empty($this->getExplicitKeys($attribute))) {
                $this->addFailure($attribute, 'forbidden_attribute', ['value' => $value]);
            }
        }

        return $this->messages->isEmpty();
    }
}
