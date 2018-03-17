<?php

declare(strict_types=1);

namespace WernerDweight\RA;

use WernerDweight\RA\Exception\RAException;

class RA implements \Countable, \ArrayAccess, \Iterator
{
    /** @var array $data */
    private $data;

    /**
     * RA constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Whether a offset exists
     * @param mixed $offset
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * Offset to retrieve
     * @param mixed $offset
     * @return mixed
     * @throws RAException
     */
    public function offsetGet($offset)
    {
        if (true !== $this->offsetExists($offset)) {
            throw RAException::create(RAException::INVALID_OFFSET);
        } else {
            return $this->data[$offset];
        }
    }

    /**
     * Offset to set
     * @param mixed $offset
     * @param mixed $value
     * @return RA
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
        return $this;
    }

    /**
     * Offset to unset
     * @param mixed $offset
     * @return RA
     * @throws RAException
     */
    public function offsetUnset($offset)
    {
        if (true !== $this->offsetExists($offset)) {
            throw RAException::create(RAException::INVALID_OFFSET);
        } else {
            unset($this->data[$offset]);
            return $this;
        }
    }

    /**
     * Count elements of an object
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Return the current element
     * @return mixed
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * Move forward to next element
     * @return mixed
     */
    public function next()
    {
        return next($this->data);
    }

    /**
     * Return the key of the current element
     * @return mixed
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * Checks if current position is valid
     * @return boolean true on success or false on failure.
     */
    public function valid(): bool
    {
        $key = $this->key();
        return null !== $key && false !== $key;
    }

    /**
     * Rewind the Iterator to the first element
     * @return RA
     */
    public function rewind(): self
    {
        reset($this->data);
        return $this;
    }

    /**
     * @param array ...$items
     * @return RA
     */
    public function push(...$items): self
    {
        array_push($this->data, ...$items);
        return $this;
    }

    /**
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->data);
    }
}
