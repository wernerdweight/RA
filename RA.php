<?php

declare(strict_types=1);

namespace WernerDweight\RA;

use WernerDweight\RA\Exception\RAException;

class RA implements \Countable, \ArrayAccess, \Iterator
{
    public const RECURSIVE = true;
    public const REGULAR = false;

    public const AS_VALUES = true;
    public const AS_KEYS = false;

    public const ARRAY_FILTER_VALUE = 0;

    /** @var array $data */
    private $data;

    /**
     * RA constructor.
     * @param array $data
     * @param bool $recursive
     */
    public function __construct(array $data, bool $recursive = self::REGULAR)
    {
        $this->data = $data;
        if (self::RECURSIVE === $recursive) {
            foreach ($this->data as $key => $value) {
                if (true === is_array($value)) {
                    $this->data[$key] = new self($value, self::RECURSIVE);
                }
            }
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($name, $this->data);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->data[$name];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @param array $arrays
     * @return array
     */
    private function convertArgumentsToPlainArrays(array $arrays): array
    {
        return array_map(function ($entry) {
            if ($entry instanceof self) {
                return $entry->toArray();
            } else {
                return $entry;
            }
        }, $arrays);
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
            throw RAException::create(RAException::INVALID_OFFSET, (string)$offset);
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
            throw RAException::create(RAException::INVALID_OFFSET, (string)$offset);
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

    /**
     * @param int $case
     * @return RA
     */
    public function changeKeyCase(int $case = CASE_LOWER): self
    {
        return new self(array_change_key_case($this->data, $case));
    }

    /**
     * @param int $size
     * @param bool $preserveKeys
     * @return RA
     */
    public function chunk(int $size, bool $preserveKeys = false): self
    {
        return new self(
            array_map(
                function(array $chunk) {
                    return new self($chunk);
                },
                array_chunk($this->data, $size, $preserveKeys)
            )
        );
    }

    /**
     * @param string|int $column
     * @param string|int $indexBy
     * @return RA
     */
    public function column($column, $indexBy): self
    {
        return new self(array_column($this->data, $column, $indexBy));
    }

    /**
     * @param array|RA $dataToCombine
     * @param bool $asKeys
     * @return RA
     */
    public function combine($dataToCombine, bool $asKeys = self::AS_VALUES): self
    {
        if ($dataToCombine instanceof self) {
            $dataToCombine = $dataToCombine->toArray();
        }
        return new self(array_combine(
            self::AS_VALUES === $asKeys ? $dataToCombine : $this->data,
            self::AS_VALUES === $asKeys ? $this->data : $dataToCombine
        ));
    }

    /**
     * @return RA
     */
    public function countValues(): self
    {
        return new self(array_count_values($this->data));
    }

    /**
     * @param (array|RA)[] ...$arrays
     * @return RA
     */
    public function diffAssoc(...$args): self
    {
        return new self(array_diff_assoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|RA)[] ...$arrays
     * @return RA
     */
    public function diffKey(...$args): self
    {
        return new self(array_diff_key($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|callable)[] ...$args
     * @return RA
     */
    public function diffUassoc(...$args): self
    {
        return new self(array_diff_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|callable)[] ...$args
     * @return RA
     */
    public function array_diff_ukey(...$args): self
    {
        return new self(array_diff_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|RA)[] ...$arrays
     * @return RA
     */
    public function array_diff(...$args): self
    {
        return new self(array_diff(...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param $value
     * @return RA
     */
    public function array_fill_keys($value): self
    {
        return new self(array_fill_keys($this->data, $value));
    }

    /**
     * @param int $startIndex
     * @param int $num
     * @param $value
     * @return RA
     */
    public function fill(int $startIndex, int $num, $value): self
    {
        $this->data = array_fill($startIndex, $num, $value);
        return $this;
    }

    /**
     * @param callable $callback
     * @param int $flag
     * @return RA
     */
    public function filter(callable $callback, int $flag = self::ARRAY_FILTER_VALUE): self
    {
        return new self(array_filter($this->data, $callback, $flag));
    }

    /**
     * @return RA
     */
    public function flip(): self
    {
        return new self(array_flip($this->data));
    }

    /**
     * @param (array|RA)[] ...$arrays
     * @return RA
     */
    public function intersectAssoc(...$args): self
    {
        return new self(array_intersect_assoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|RA)[] ...$arrays
     * @return RA
     */
    public function array_intersect_key(...$args): self
    {
        return new self(array_intersect_key($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|callable)[] ...$args
     * @return RA
     */
    public function array_intersect_uassoc(...$args): self
    {
        return new self(array_intersect_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|callable)[] ...$args
     * @return RA
     */
    public function array_intersect_ukey(...$args): self
    {
        return new self(array_intersect_ukey($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|RA)[] ...$arrays
     * @return RA
     */
    public function array_intersect(...$args): self
    {
        return new self(array_intersect($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param $key
     * @return bool
     */
    public function keyExists($key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * @param null $searchValue
     * @param bool $strict
     * @return RA
     */
    public function keys($searchValue = null, bool $strict = false): self
    {
        return new self(array_keys($this->data, $searchValue, $strict));
    }

    /**
     * @param callable $callback
     * @return RA
     */
    public function map(callable $callback): self
    {
        return new self(array_map($callback, $this->data));
    }

    /**
     * @param (array|RA)[] ...$arrays
     * @return RA
     */
    public function mergeRecursive(...$args): self
    {
        return new self(array_merge_recursive(...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|RA)[] ...$arrays
     * @return RA
     */
    public function merge(...$args): self
    {
        return new self(array_merge(...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     *
     */
    public function array_pad()
    {
        //— Pad array to the specified length with a value
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_pop()
    {
        //— Pop the element off the end of array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_product()
    {
        //— Calculate the product of values in an array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_push()
    {
        //— Push one or more elements onto the end of array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_rand()
    {
        //— Pick one or more random entries out of an array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_reduce()
    {
        //— Iteratively reduce the array to a single value using a callback function
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_replace_recursive()
    {
        //— Replaces elements from passed arrays into the first array recursively
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_replace()
    {
        //— Replaces elements from passed arrays into the first array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_reverse()
    {
        //— Return an array with elements in reverse order
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_search()
    {
        //— Searches the array for a given value and returns the first corresponding key if successful
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_shift()
    {
        //— Shift an element off the beginning of array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_slice()
    {
        //— Extract a slice of the array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_splice()
    {
        //— Remove a portion of the array and replace it with something else
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_sum()
    {
        //— Calculate the sum of values in an array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_udiff_assoc()
    {
        //— Computes the difference of arrays with additional index check, compares data by a callback function
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_udiff_uassoc()
    {
        //— Computes the difference of arrays with additional index check, compares data and indexes by a callback function
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_udiff()
    {
        //— Computes the difference of arrays by using a callback function for data comparison
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_uintersect_assoc()
    {
        //— Computes the intersection of arrays with additional index check, compares data by a callback function
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_uintersect_uassoc()
    {
        //— Computes the intersection of arrays with additional index check, compares data and indexes by separate callback functions
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_uintersect()
    {
        //— Computes the intersection of arrays, compares data by a callback function
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_unique()
    {
        //— Removes duplicate values from an array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_unshift()
    {
        //— Prepend one or more elements to the beginning of an array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_values()
    {
        //— Return all the values of an array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_walk_recursive()
    {
        //— Apply a user function recursively to every member of an array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array_walk()
    {
        //— Apply a user supplied function to every member of an array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function array()
    {
        //— Create an array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function arsort()
    {
        //— Sort an array in reverse order and maintain index association
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function asort()
    {
        //— Sort an array and maintain index association
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function compact()
    {
        //— Create array containing variables and their values
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function each()
    {
        //— Return the current key and value pair from an array and advance the array cursor
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function end()
    {
        //— Set the internal pointer of an array to its last element
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function extract()
    {
        //— Import variables into the current symbol table from an array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function in_array()
    {
        //— Checks if a value exists in an array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function key_exists()
    {
        //— Alias of array_key_exists
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function krsort()
    {
        //— Sort an array by key in reverse order
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function ksort()
    {
        //— Sort an array by key
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function list()
    {
        //— Assign variables as if they were an array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function natcasesort()
    {
        //— Sort an array using a case insensitive "natural order" algorithm
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function natsort()
    {
        //— Sort an array using a "natural order" algorithm
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function pos()
    {
        //— Alias of current
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function prev()
    {
        //— Rewind the internal array pointer
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function range()
    {
        //— Create an array containing a range of elements
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function reset()
    {
        //— Set the internal pointer of an array to its first element
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function rsort()
    {
        //— Sort an array in reverse order
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function shuffle()
    {
        //— Shuffle an array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function sizeof()
    {
        //— Alias of count
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function sort()
    {
        //— Sort an array
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function uasort()
    {
        //— Sort an array with a user-defined comparison function and maintain index association
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function uksort()
    {
        //— Sort an array by keys using a user-defined comparison function
        throw new \RuntimeException('Not yet implemented');
    }

    /**
     *
     */
    public function usort()
    {
        //— Sort an array by values using a user-defined comparison function
        throw new \RuntimeException('Not yet implemented');
    }

}
