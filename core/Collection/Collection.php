<?php

namespace Toolkit\Collection;


use \Toolkit\Exceptions\InvalidKeyException;


/**
 * Class Collection
 * @package Toolkit\Collection
 */
abstract class Collection implements \Iterator, \ArrayAccess {

    private $members = [];
    private $count = 0;

    /**
     * Add item to the collection
     *
     * @param $item
     * @param null $key
     * @throws InvalidKeyException
     */
    protected function add_item($item, $key = null)
    {
        if ($key) {
            if ($this->exists($key)) {
                throw new InvalidKeyException("Key already exists");
            } else {
                $this->members[$key] = $item;
            }
        } else {
            $this->members[] = $item;
        }
    }

    /**
     * Remove item from the collection
     *
     * @param $key
     * @throws InvalidKeyException
     */
    public function remove($key)
    {
        if (! $this->exists($key)) {
            throw new InvalidKeyException("Key does not exist");
        }

        unset($this->members[$key]);
    }

    /**
     * Retrieve item value from the collection
     *
     * @param $key
     * @return mixed
     * @throws InvalidKeyException
     */
    public function item($key)
    {
        if (! $this->exists($key)) {
            throw new InvalidKeyException("Key does not exist");
        }

        return $this->members[$key];
    }

    /**
     * Retrieve an array of all collection item keys
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->members);
    }

    /**
     * Return whether item exists in collection (true | false)
     *
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        return isset($this->members[$key]);
    }

    /**
     * Return the number of items in the collection
     *
     * @return int
     */
    public function length()
    {
        return count($this->members);
    }

    /**
     * Increment iterator count by 1
     */
    public function next()
    {
        $this->count++;
    }

    /**
     * Return the current value in iteration
     *
     * @return mixed
     */
    public function current()
    {
        return $this->members[$this->key()];
    }

    /**
     * Return the current key in iteration
     *
     * @return mixed
     */
    public function key()
    {
        return $this->keys()[$this->count];
    }

    /**
     * Reset iterator count to 0
     */
    public function rewind()
    {
        $this->count = 0;
    }

    /**
     * Return whether current iteration is within the collection (true | false)
     *
     * @return bool
     */
    public function valid()
    {
        return $this->count < $this->length();
    }

    /**
     * ArrayAccess returns whether the key exists (true | false)
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    /**
     * ArrayAccess retrieve a value from the collection
     *
     * @param mixed $offset
     * @return mixed
     * @throws InvalidKeyException
     */
    public function offsetGet($offset)
    {
        return $this->item($offset);
    }

    /**
     * Add a value to the collection
     *
     * @param mixed $offset
     * @param mixed $value
     * @throws InvalidKeyException
     */
    public function offsetSet($offset, $value)
    {
        $this->add($value, $offset);
    }

    /**
     * Unset an element in the collection
     *
     * @param mixed $offset
     * @throws InvalidKeyException
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}