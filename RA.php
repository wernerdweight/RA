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

    // helpers

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

    // magical

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


    // main

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
     * @param mixed[] ...$items
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
     * @param (array|RA)[] ...$args
     * @return RA
     */
    public function diffAssoc(...$args): self
    {
        return new self(array_diff_assoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|RA)[] ...$args
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
    public function diffUkey(...$args): self
    {
        return new self(array_diff_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|RA)[] ...$args
     * @return RA
     */
    public function diff(...$args): self
    {
        return new self(array_diff(...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param $value
     * @return RA
     */
    public function fillKeys($value): self
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
     * @param (array|RA)[] ...$args
     * @return RA
     */
    public function intersectAssoc(...$args): self
    {
        return new self(array_intersect_assoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|RA)[] ...$args
     * @return RA
     */
    public function intersectKey(...$args): self
    {
        return new self(array_intersect_key($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|callable)[] ...$args
     * @return RA
     */
    public function intersectUassoc(...$args): self
    {
        return new self(array_intersect_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|callable)[] ...$args
     * @return RA
     */
    public function intersectUkey(...$args): self
    {
        return new self(array_intersect_ukey($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|RA)[] ...$args
     * @return RA
     */
    public function intersect(...$args): self
    {
        return new self(array_intersect($this->data, ...$this->convertArgumentsToPlainArrays($args)));
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
     * @param (array|RA)[] ...$args
     * @return RA
     */
    public function mergeRecursive(...$args): self
    {
        return new self(array_merge_recursive(...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|RA)[] ...$args
     * @return RA
     */
    public function merge(...$args): self
    {
        return new self(array_merge(...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param int $size
     * @param $value
     * @return RA
     */
    public function pad(int $size, $value): self
    {
        return new self(array_pad($this->data, $size, $value));
    }

    /**
     * @return float|int
     */
    public function product()
    {
        return array_product($this->data);
    }

    /**
     * @param int $length
     * @return mixed|RA
     */
    public function random(int $length = 1)
    {
        $keys = array_rand($this->data, $length);
        if (true === is_array($keys)) {
            return new self(array_map(function ($key) {
                return $this->data[$key];
            }, $keys));
        } else {
            return $this->data[$keys];
        }
    }

    /**
     * @param callable $callback
     * @param mixed|null $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->data, $callback, $initial);
    }

    /**
     * @param array ...$args
     * @return RA
     */
    public function replaceRecursive(...$args): self
    {
        return new self(array_replace_recursive($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param array ...$args
     * @return RA
     */
    public function replace(...$args): self
    {
        return new self(array_replace($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @return RA
     */
    public function reverse(): self
    {
        return new self(array_reverse($this->data));
    }

    /**
     * @param $needle
     * @return mixed|null
     */
    public function search($needle)
    {
        $key = array_search($needle, $this->data, true);
        if (false !== $key) {
            return $this->data[$key];
        } else {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->data);
    }

    /**
     * @param int $offset
     * @param int|null $length
     * @param bool $preserveKeys
     * @return RA
     */
    public function slice(int $offset, int $length = null, $preserveKeys = false): self
    {
        return new self(array_slice($this->data, $offset, $length, $preserveKeys));
    }

    /**
     * @param int $offset
     * @param int|null $length
     * @param mixed|null $replacement
     * @return RA
     */
    public function splice(int $offset, int $length = null, $replacement = null): self
    {
        if (null === $length) {
            $length = $this->count();
        }
        if (null === $replacement) {
            $replacement = [];
        }
        return new self(array_splice($this->data, $offset, $length, $replacement));
    }

    /**
     * @return float|int
     */
    public function sum()
    {
        return array_sum($this->data);
    }

    /**
     * @param (array|callable) ...$args
     * @return RA
     */
    public function udiffAssoc(...$args): self
    {
        return new self(array_udiff_assoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|callable) ...$args
     * @return RA
     */
    public function udiffUassoc(...$args): self
    {
        return new self(array_udiff_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|callable)[] ...$args
     * @return RA
     */
    public function udiff(...$args): self
    {
        return new self(array_udiff($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|callable)[] ...$args
     * @return RA
     */
    public function uintersectAssoc(...$args): self
    {
        return new self(array_uintersect_assoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|callable)[] ...$args
     * @return RA
     */
    public function uintersectUassoc(...$args): self
    {
        return new self(array_uintersect_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (array|callable)[] ...$args
     * @return RA
     */
    public function uintersect(...$args): self
    {
        return new self(array_uintersect($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param int $sortFlags
     * @return RA
     */
    public function unique(int $sortFlags = SORT_STRING): self
    {
        return new self(array_unique($this->data, $sortFlags));
    }

    /**
     * @param mixed[] ...$args
     * @return RA
     */
    public function unshift(...$args): self
    {
        array_unshift($this->data, ...$args);
        return $this;
    }

    /**
     * @return RA
     */
    public function values(): self
    {
        return new self(array_values($this->data));
    }

    /**
     * @param callable $callback
     * @param mixed|null $payload
     * @return RA
     */
    public function walkRecursive(callable $callback, $payload = null): self
    {
        array_walk_recursive($this->data, $callback, $payload);
        return $this;
    }

    /**
     * @param callable $callback
     * @param mied|null $payload
     * @return RA
     */
    public function walk(callable $callback, $payload = null): self
    {
        array_walk($this->data, $callback, $payload);
        return $this;
    }

    /**
     * @param int $sortFlags
     * @return RA
     */
    public function arsort(int $sortFlags = SORT_REGULAR): self
    {
        arsort($this->data, $sortFlags);
        return $this;
    }

    /**
     * @param int $sortFlags
     * @return RA
     */
    public function asort(int $sortFlags = SORT_REGULAR): self
    {
        arsort($this->data, $sortFlags);
        return $this;
    }

    /**
     * @return mixed
     */
    public function end()
    {
        return end($this->data);
    }

    /**
     * @param mixed $needle
     * @return bool
     */
    public function contains($needle): bool
    {
        return is_array($needle, $this->data, true);
    }

    /**
     * @param int $sortFlags
     * @return RA
     */
    public function krsort(int $sortFlags = SORT_REGULAR): self
    {
        krsort($this->data, $sortFlags);
        return $this;
    }

    /**
     * @param int $sortFlags
     * @return RA
     */
    public function ksort(int $sortFlags = SORT_REGULAR): self
    {
        ksort($this->data, $sortFlags);
        return $this;
    }

    /**
     * @return RA
     */
    public function natcasesort(): self
    {
        natcasesort($this->data);
        return $this;
    }

    /**
     * @return RA
     */
    public function natsort(): self
    {
        natsort($this->data);
        return $this;
    }

    /**
     * @return mixed
     */
    public function prev()
    {
        return prev($this->data);
    }

    /**
     * @param $start
     * @param $end
     * @param int $step
     * @return RA
     */
    public function range($start, $end, $step = 1): self
    {
        $this->data = range($start, $end, $step);
        return $this;
    }

    /**
     * @return mixed
     */
    public function reset()
    {
        return reset($this->data);
    }

    /**
     * @param int $sortFlags
     * @return RA
     */
    public function rsort(int $sortFlags = SORT_REGULAR): self
    {
        rsort($this->data, $sortFlags);
        return $this;
    }

    /**
     * @return RA
     */
    public function shuffle(): self
    {
        shuffle($this->data);
        return $this;
    }

    /**
     * @param int $sortFlags
     * @return RA
     */
    public function sort(int $sortFlags = SORT_REGULAR): self
    {
        sort($this->data, $sortFlags);
        return $this;
    }

    /**
     * @param callable $callback
     * @return RA
     */
    public function uasort(callable $callback): self
    {
        uasort($this->data, $callback);
        return $this;
    }

    /**
     * @param callable $callback
     * @return RA
     */
    public function uksort(callable $callback): self
    {
        uksort($this->data, $callback);
        return $this;
    }

    /**
     * @param callable $callback
     * @return RA
     */
    public function usort(callable $callback): self
    {
        usort($this->data, $callback);
        return $this;
    }

    // Aliases

    /**
     * @param mixed $key
     * @return bool
     */
    public function keyExists($key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * @param mixed $needle
     * @return bool
     */
    public function has($needle): bool
    {
        return $this->contains($needle);
    }

    /**
     * @param mixed $needle
     * @return bool
     */
    public function hasValue($needle): bool
    {
        return $this->contains($needle);
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function hasKey($key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * @return mixed
     */
    public function pos()
    {
        return $this->current();
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return $this->count();
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return $this->count();
    }

    /**
     * @param mixed $offset
     * @return mixed
     * @throws RAException
     */
    public function get($offset = null)
    {
        if (null === $offset) {
            return $this->current();
        } else {
            return $this->offsetGet($offset);
        }
    }

    /**
     * @param $offset
     * @param $value
     * @return RA
     */
    public function set($offset, $value): self
    {
        return $this->offsetSet($offset, $value);
    }

    /**
     * @param $offset
     * @return RA
     * @throws RAException
     */
    public function unset($offset): self
    {
        return $this->offsetUnset($offset);
    }

    /**
     * @return mixed
     */
    public function previous()
    {
        return $this->prev();
    }

    /**
     * @return mixed
     */
    public function getCurrentIndex()
    {
        return $this->key();
    }

    /**
     * @return mixed
     */
    public function getCurrenKey()
    {
        return $this->key();
    }

    /**
     * @param mixed[] ...$items
     * @return RA
     */
    public function append(...$items): self
    {
        return $this->push($items);
    }

    /**
     * @return RA
     */
    public function aggregateValues(): self
    {
        return $this->countValues();
    }

    /**
     * @return RA
     */
    public function getKeys(): self
    {
        return $this->keys();
    }

    /**
     * @return float|int
     */
    public function getProduct()
    {
        return $this->product();
    }

    /**
     * @return mixed
     */
    public function getRandomEntry()
    {
        return $this->random();
    }

    /**
     * @return mixed
     */
    public function getRandomValue()
    {
        return $this->random();
    }

    /**
     * @return mixed
     */
    public function getRandomItem()
    {
        return $this->random();
    }

    /**
     * @param mixed $needle
     * @return mixed|null
     */
    public function find($needle)
    {
        return $this->search($needle);
    }

    /**
     * @return float|int
     */
    public function getSum()
    {
        return $this->sum();
    }

    /**
     * @return RA
     */
    public function getValues(): self
    {
        return $this->values();
    }

    /**
     * @return RA
     */
    public function entries(): self
    {
        return $this->values();
    }

    /**
     * @return RA
     */
    public function getEntries(): self
    {
        return $this->values();
    }

    /**
     * @return RA
     */
    public function items(): self
    {
        return $this->values();
    }

    /**
     * @return RA
     */
    public function getItems(): self
    {
        return $this->values();
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return $this->reset();
    }

    /**
     * @return mixed
     */
    public function last()
    {
        return $this->end();
    }

}
