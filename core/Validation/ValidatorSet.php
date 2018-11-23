<?php

namespace Toolkit\Validation;

use Toolkit\Collection\Collection;


class ValidatorSet extends Collection {

    /**
     * @param $validator
     * @param null $key
     * @throws \Exception
     */
    public function add(Validator $validator, $key = null)
    {
        parent::add_item($validator, $key);
    }

    public function get_errors()
    {
        $errors = [];
        foreach ($this as $key => $validator) {
            if ($validator->has_error()) {
                $errors[$key] = $validator->get_error();
            }
        }
        return $errors;
    }

}