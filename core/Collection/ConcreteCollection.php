<?php

namespace Toolkit\Collection;


class ConcreteCollection extends Collection {

    /**
     * @param $item
     * @param null $key
     * @throws \Toolkit\Exceptions\InvalidKeyException
     */
    public function add(string $item, $key = null)
    {
        parent::add_item($item, $key);
    }
}