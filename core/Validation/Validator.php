<?php

namespace Toolkit\Validation;


abstract class Validator {

    protected $error_message;
    protected $value;
    protected $required;

    public function __construct(string $value, bool $required = false)
    {
        $this->value = trim($value);
        $this->required = $required;
        if (empty($this->value)) {
            $this->error_message = $this->required ? 'This is a required field' : '';
        } else {
            $this->validate();
        }
    }

    public function has_error()
    {
        return ! empty($this->error_message);
    }

    public function get_error()
    {
        return $this->error_message;
    }

    abstract protected function validate();
}