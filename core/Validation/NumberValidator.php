<?php
/**
 * Created by PhpStorm.
 * User: phil
 * Date: 02/11/2018
 * Time: 21:02
 */

namespace Toolkit\Validation;


class NumberValidator extends Validator {

    private $min = 21;
    private $max = 44;

    protected function validate()
    {
        $options = [
            'options' => [
                'min_range' => $this->min,
                'max_range' => $this->max
            ]
        ];

        if (! filter_var($this->value, FILTER_VALIDATE_INT, $options)) {

            $this->error_message = "Integer value from {$this->min} to {$this->max}";
        }
    }

    public function set_range(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;

        if ((! $this->has_error())
            || $this->error_message != 'This is a required field')
        {
            $this->error_message = null;
            $this->validate();
        }
    }
}