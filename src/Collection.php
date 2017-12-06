<?php

namespace Toolkit;


class Collection implements \ArrayAccess {

    private $members;

    /**
     * @param $item
     * @param null $key
     * @throws InvalidKeyException if the key already exists in the collection
     */
    public function addItem($item, $key = null)
    {
        if (is_null($key)) {
            $this->members[] = $item;
        } else {
            if ($this->exists($key)) {
                throw new InvalidKeyException('Key already exists.');
            }
            $this->members[$key] = $item;
        }
    }

    /**
     * @param $key
     * @return mixed
     * @throws InvalidKeyException if the provided key does not exist in the collection
     */
    public function getItem($key)
    {
        if (! $this->exists($key)) {
            throw new InvalidKeyException();
        }
        return $this->members[$key];
    }

    /**
     * @param mixed $key
     * @throws InvalidKeyException if the provided key does not exist in the collection
     */
    public function removeItem($key)
    {
        if (! $this->exists($key)) {
            throw new InvalidKeyException();
        }
        unset($this->members[$key]);
    }

    public function exists($key)
    {
        return isset($this->members[$key]);
    }

    public function keys()
    {
        if ($this->length() > 0) {
            return array_keys($this->members);
        } else {
            return array();
        }
    }

    public function length()
    {
        return count($this->members);
    }

    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     * @throws InvalidKeyException if offset is not in the collection
     */
    public function offsetGet($offset)
    {
        return $this->getItem($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->addItem($value, $offset);
    }

    /**
     * @param mixed $offset
     * @throws InvalidKeyException if offset is not in the collection
     */
    public function offsetUnset($offset)
    {
        $this->removeItem($offset);
    }
}