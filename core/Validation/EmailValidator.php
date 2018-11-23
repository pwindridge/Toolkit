<?php

namespace Toolkit\Validation;


class EmailValidator extends Validator {

    protected function validate()
    {
        if (! filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            $this->error_message = 'Not a valid email address';
        }
    }
}