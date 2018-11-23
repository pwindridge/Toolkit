<?php

namespace Toolkit\Validation;


class NameValidator extends Validator {

    private $min = 0;
    private $max = 25;

    protected function validate()
    {
        if (strlen($this->value) > $this->max ||
            strlen($this->value) < $this->min)
        {
            $this->error_message =
                "Must be between {$this->min}" .
                " and {$this->max} characters (inclusive)";
        }
    }

    public function set_range(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;

        if (! $this->has_error()) {
            $this->error_message = null;
            $this->validate();
        }
    }
}