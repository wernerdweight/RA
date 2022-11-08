<?php
declare(strict_types=1);

namespace WernerDweight\RA;

use WernerDweight\RA\Exception\RAException;

/**
 * Class RA.
 *
 * @implements \ArrayAccess<string|int, mixed>
 * @implements \Iterator<string|int, mixed>
 */
final class RA implements \Countable, \ArrayAccess, \Iterator
{
    /** @var bool */
    public const RECURSIVE = true;
    /** @var bool */
    public const REGULAR = false;

    /** @var bool */
    public const AS_VALUES = true;
    /** @var bool */
    public const AS_KEYS = false;

    /** @var int */
    public const ARRAY_FILTER_VALUE = 0;

    /** @var mixed[] */
    private $data = [];

    // helpers

    /**
     * @param mixed[] $arrays
     *
     * @return mixed[]
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
     * @param mixed $offset
     *
     * @return bool
     *
     * @throws RAException
     */
    public function getBool($offset): bool
    {
        /** @var bool $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return bool|null
     *
     * @throws RAException
     */
    public function getBoolOrNull($offset): ?bool
    {
        /** @var null|bool $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return int
     *
     * @throws RAException
     */
    public function getInt($offset): int
    {
        /** @var int $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return int|null
     *
     * @throws RAException
     */
    public function getIntOrNull($offset): ?int
    {
        /** @var null|int $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return float
     *
     * @throws RAException
     */
    public function getFloat($offset): float
    {
        /** @var float $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return float|null
     *
     * @throws RAException
     */
    public function getFloatOrNull($offset): ?float
    {
        /** @var null|float $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return string
     *
     * @throws RAException
     */
    public function getString($offset): string
    {
        /** @var string $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return string|null
     *
     * @throws RAException
     */
    public function getStringOrNull($offset): ?string
    {
        /** @var null|string $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return mixed[]
     *
     * @throws RAException
     */
    public function getArray($offset): array
    {
        /** @var mixed[] $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return mixed[]|null
     *
     * @throws RAException
     */
    public function getArrayOrNull($offset): ?array
    {
        /** @var null|mixed[] $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return RA
     *
     * @throws RAException
     */
    public function getRA($offset): self
    {
        /** @var RA $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return RA|null
     *
     * @throws RAException
     */
    public function getRAOrNull($offset): ?self
    {
        /** @var null|RA $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return callable
     *
     * @throws RAException
     */
    public function getCallable($offset): callable
    {
        /** @var callable $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return callable|null
     *
     * @throws RAException
     */
    public function getCallableOrNull($offset): ?callable
    {
        /** @var null|callable $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return iterable<mixed>
     *
     * @throws RAException
     */
    public function getIterable($offset): iterable
    {
        /** @var iterable<mixed> $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return iterable<mixed>|null
     *
     * @throws RAException
     */
    public function getIterableOrNull($offset): ?iterable
    {
        /** @var null|iterable<mixed> $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @param mixed $offset
     *
     * @return RA
     *
     * @throws RAException
     */
    public function increment($offset): self
    {
        $currentValue = $this->get($offset);
        if (true !== is_int($currentValue) && true !== is_float($currentValue)) {
            throw new RAException(RAException::INVALID_INCREMENT_TYPE);
        }
        $this->set($offset, ++$currentValue);
        return $this;
    }

    /**
     * @param mixed $offset
     *
     * @return RA
     *
     * @throws RAException
     */
    public function decrement($offset): self
    {
        $currentValue = $this->get($offset);
        if (true !== is_int($currentValue) && true !== is_float($currentValue)) {
            throw new RAException(RAException::INVALID_INCREMENT_TYPE);
        }
        $this->set($offset, --$currentValue);
        return $this;
    }

    // magical

    /**
     * RA constructor.
     *
     * @param mixed[] $data
     * @param bool    $recursive
     */
    public function __construct(array $data = [], bool $recursive = self::REGULAR)
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
     * @param mixed  $value
     */
    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * @param string $name
     *
     * @return mixed
     *
     * @throws RAException
     */
    public function __get(string $name)
    {
        if (true !== $this->offsetExists($name)) {
            throw new RAException(RAException::INVALID_OFFSET, $name);
        } else {
            return $this->data[$name];
        }
    }

    /**
     * @param string $name
     *
     * @throws RAException
     */
    public function __unset(string $name): void
    {
        if (true !== $this->offsetExists($name)) {
            throw new RAException(RAException::INVALID_OFFSET, $name);
        } else {
            unset($this->data[$name]);
        }
    }

    /**
     * @param bool $recursive
     *
     * @return mixed[]
     */
    public function toArray(bool $recursive = self::REGULAR): array
    {
        if (self::RECURSIVE === $recursive) {
            $data = [];
            foreach ($this->data as $key => $value) {
                if ($value instanceof self) {
                    $data[$key] = $value->toArray($recursive);
                } else {
                    $data[$key] = $value;
                }
            }
            return $data;
        } else {
            return $this->data;
        }
    }

    // main

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset
     *
     * @return bool true on success or false on failure
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset
     *
     * @return mixed
     *
     * @throws RAException
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (true !== $this->offsetExists($offset)) {
            throw new RAException(RAException::INVALID_OFFSET, (string)$offset);
        } else {
            return $this->data[$offset];
        }
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return RA
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     *
     * @return RA
     *
     * @throws RAException
     */
    public function offsetUnset($offset): void
    {
        if (true !== $this->offsetExists($offset)) {
            throw new RAException(RAException::INVALID_OFFSET, (string)$offset);
        } else {
            unset($this->data[$offset]);
        }
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Return the current element.
     *
     * @return mixed
     */
    public function current(): mixed
    {
        return current($this->data);
    }

    /**
     * Move forward to next element.
     *
     * @return mixed
     */
    public function next(): void
    {
        next($this->data);
    }

    /**
     * Return the key of the current element.
     *
     * @return mixed
     */
    public function key(): mixed
    {
        return key($this->data);
    }

    /**
     * Checks if current position is valid.
     *
     * @return bool true on success or false on failure
     */
    public function valid(): bool
    {
        $key = $this->key();
        return null !== $key && false !== $key;
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @return RA
     */
    public function rewind(): void
    {
        reset($this->data);
    }

    /**
     * @param mixed[] ...$items
     *
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
     *
     * @return RA
     */
    public function changeKeyCase(int $case = CASE_LOWER): self
    {
        return new self(array_change_key_case($this->data, $case));
    }

    /**
     * @param int  $size
     * @param bool $preserveKeys
     *
     * @return RA
     */
    public function chunk(int $size, bool $preserveKeys = false): self
    {
        return new self(
            array_map(
                function (array $chunk) {
                    return new self($chunk);
                },
                array_chunk($this->data, $size, $preserveKeys)
            )
        );
    }

    /**
     * @param string|int $column
     * @param string|int $indexBy
     *
     * @return RA
     */
    public function column($column, $indexBy): self
    {
        return new self(array_column($this->data, $column, $indexBy));
    }

    /**
     * @param mixed[]|RA $dataToCombine
     * @param bool       $asKeys
     *
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function combine($dataToCombine, bool $asKeys = self::AS_VALUES): self
    {
        if ($dataToCombine instanceof self) {
            $dataToCombine = $dataToCombine->toArray();
        }
        $combined = \Safe\array_combine(
            self::AS_VALUES === $asKeys ? $dataToCombine : $this->data,
            self::AS_VALUES === $asKeys ? $this->data : $dataToCombine
        );
        return new self($combined);
    }

    /**
     * @return RA
     */
    public function countValues(): self
    {
        return new self(array_count_values($this->data));
    }

    /**
     * @param (RA)[] ...$args
     *
     * @return RA
     */
    public function diffAssoc(...$args): self
    {
        return new self(array_diff_assoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA)[] ...$args
     *
     * @return RA
     */
    public function diffKey(...$args): self
    {
        return new self(array_diff_key($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     *
     * @return RA
     */
    public function diffUassoc(...$args): self
    {
        return new self(array_diff_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     *
     * @return RA
     */
    public function diffUkey(...$args): self
    {
        return new self(array_diff_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA)[] ...$args
     *
     * @return RA
     */
    public function diff(...$args): self
    {
        return new self(array_diff($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param mixed $value
     *
     * @return RA
     */
    public function fillKeys($value): self
    {
        return new self(array_fill_keys($this->data, $value));
    }

    /**
     * @param int   $startIndex
     * @param int   $num
     * @param mixed $value
     *
     * @return RA
     */
    public function fill(int $startIndex, int $num, $value): self
    {
        $this->data = array_fill($startIndex, $num, $value);
        return $this;
    }

    /**
     * @param callable $callback
     * @param int      $flag
     *
     * @return RA
     */
    public function filter(callable $callback, int $flag = self::ARRAY_FILTER_VALUE): self
    {
        return new self(array_filter($this->data, $callback, $flag));
    }

    /**
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function flip(): self
    {
        return new self(\Safe\array_flip($this->data));
    }

    /**
     * @param (RA)[] ...$args
     *
     * @return RA
     */
    public function intersectAssoc(...$args): self
    {
        return new self(array_intersect_assoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA)[] ...$args
     *
     * @return RA
     */
    public function intersectKey(...$args): self
    {
        return new self(array_intersect_key($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     *
     * @return RA
     */
    public function intersectUassoc(...$args): self
    {
        return new self(array_intersect_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     *
     * @return RA
     */
    public function intersectUkey(...$args): self
    {
        return new self(array_intersect_ukey($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA)[] ...$args
     *
     * @return RA
     */
    public function intersect(...$args): self
    {
        return new self(array_intersect($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param mixed|null $searchValue
     * @param bool       $strict
     *
     * @return RA
     */
    public function keys($searchValue = null, bool $strict = false): self
    {
        $args[] = $this->data;
        if (null !== $searchValue) {
            $args[] = $searchValue;
            $args[] = $strict;
        }
        return new self(array_keys(...$args));
    }

    /**
     * @param callable $callback
     * @param (RA)[]   ...$args
     *
     * @return RA
     */
    public function map(callable $callback, ...$args): self
    {
        return new self(array_map($callback, $this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA)[] ...$args
     *
     * @return RA
     */
    public function mergeRecursive(...$args): self
    {
        return new self(array_merge_recursive($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA)[] ...$args
     *
     * @return RA
     */
    public function merge(...$args): self
    {
        return new self(array_merge($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param int   $size
     * @param mixed $value
     *
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
     *
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
     * @param callable   $callback
     * @param mixed|null $initial
     *
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->data, $callback, $initial);
    }

    /**
     * @param mixed[] ...$args
     *
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function replaceRecursive(...$args): self
    {
        return new self(
            (array)\Safe\array_replace_recursive($this->data, ...$this->convertArgumentsToPlainArrays($args))
        );
    }

    /**
     * @param mixed[]...$args
     *
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function replace(...$args): self
    {
        return new self((array)\Safe\array_replace($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @return RA
     */
    public function reverse(): self
    {
        return new self(array_reverse($this->data));
    }

    /**
     * @param mixed $needle
     *
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
     * @param int      $offset
     * @param int|null $length
     * @param bool     $preserveKeys
     *
     * @return RA
     */
    public function slice(int $offset, ?int $length = null, $preserveKeys = false): self
    {
        return new self(array_slice($this->data, $offset, $length, $preserveKeys));
    }

    /**
     * @param int        $offset
     * @param int|null   $length
     * @param mixed|null $replacement
     *
     * @return RA
     */
    public function splice(int $offset, ?int $length = null, $replacement = null): self
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
     * @param (RA|array|callable) ...$args
     *
     * @return RA
     */
    public function udiffAssoc(...$args): self
    {
        return new self(array_udiff_assoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|array|callable) ...$args
     *
     * @return RA
     */
    public function udiffUassoc(...$args): self
    {
        return new self(array_udiff_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     *
     * @return RA
     */
    public function udiff(...$args): self
    {
        return new self(array_udiff($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     *
     * @return RA
     */
    public function uintersectAssoc(...$args): self
    {
        return new self(array_uintersect_assoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     *
     * @return RA
     */
    public function uintersectUassoc(...$args): self
    {
        return new self(array_uintersect_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     *
     * @return RA
     */
    public function uintersect(...$args): self
    {
        return new self(array_uintersect($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param int $sortFlags
     *
     * @return RA
     */
    public function unique(int $sortFlags = SORT_STRING): self
    {
        return new self(array_unique($this->data, $sortFlags));
    }

    /**
     * @param mixed[] ...$args
     *
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
     * @param callable   $callback
     * @param mixed|null $payload
     *
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function walkRecursive(callable $callback, $payload = null): self
    {
        \Safe\array_walk_recursive($this->data, $callback, $payload);
        return $this;
    }

    /**
     * @param callable   $callback
     * @param mixed|null $payload
     *
     * @return RA
     */
    public function walk(callable $callback, $payload = null): self
    {
        array_walk($this->data, $callback, $payload);
        return $this;
    }

    /**
     * @param int $sortFlags
     *
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function arsort(int $sortFlags = SORT_REGULAR): self
    {
        \Safe\arsort($this->data, $sortFlags);
        return $this;
    }

    /**
     * @param int $sortFlags
     *
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function asort(int $sortFlags = SORT_REGULAR): self
    {
        \Safe\asort($this->data, $sortFlags);
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
     *
     * @return bool
     */
    public function contains($needle): bool
    {
        return in_array($needle, $this->data, true);
    }

    /**
     * @param int $sortFlags
     *
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function krsort(int $sortFlags = SORT_REGULAR): self
    {
        \Safe\krsort($this->data, $sortFlags);
        return $this;
    }

    /**
     * @param int $sortFlags
     *
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function ksort(int $sortFlags = SORT_REGULAR): self
    {
        \Safe\ksort($this->data, $sortFlags);
        return $this;
    }

    /**
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function natcasesort(): self
    {
        \Safe\natcasesort($this->data);
        return $this;
    }

    /**
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function natsort(): self
    {
        \Safe\natsort($this->data);
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
     * @param mixed $start
     * @param mixed $end
     * @param int   $step
     *
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
     *
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function rsort(int $sortFlags = SORT_REGULAR): self
    {
        \Safe\rsort($this->data, $sortFlags);
        return $this;
    }

    /**
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function shuffle(): self
    {
        \Safe\shuffle($this->data);
        return $this;
    }

    /**
     * @param int $sortFlags
     *
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function sort(int $sortFlags = SORT_REGULAR): self
    {
        \Safe\sort($this->data, $sortFlags);
        return $this;
    }

    /**
     * @param callable $callback
     *
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function uasort(callable $callback): self
    {
        \Safe\uasort($this->data, $callback);
        return $this;
    }

    /**
     * @param callable $callback
     *
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function uksort(callable $callback): self
    {
        \Safe\uksort($this->data, $callback);
        return $this;
    }

    /**
     * @param callable $callback
     *
     * @return RA
     *
     * @throws \Safe\Exceptions\ArrayException
     */
    public function usort(callable $callback): self
    {
        \Safe\usort($this->data, $callback);
        return $this;
    }

    /**
     * @param string $delimiter
     *
     * @return string
     *
     * @throws \Safe\Exceptions\StringsException
     */
    public function implode(string $delimiter = '', bool $recursive = self::REGULAR): string
    {
        if (self::RECURSIVE === $recursive) {
            /** @var string[] $data */
            $data = [];
            foreach ($this->data as $key => $value) {
                if ($value instanceof self) {
                    $data[$key] = \Safe\sprintf('{%s}', $value->implode($delimiter, $recursive));
                } else {
                    $data[$key] = $value;
                }
            }
            return implode($delimiter, $data);
        }
        return implode($delimiter, $this->data);
    }

    // Aliases

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function keyExists($key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * @param mixed $needle
     *
     * @return bool
     */
    public function has($needle): bool
    {
        return $this->contains($needle);
    }

    /**
     * @param mixed $needle
     *
     * @return bool
     */
    public function hasValue($needle): bool
    {
        return $this->contains($needle);
    }

    /**
     * @param mixed $key
     *
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
     *
     * @return mixed
     *
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
     * @param mixed $offset
     * @param mixed $value
     *
     * @return RA
     */
    public function set($offset, $value): self
    {
        $this->offsetSet($offset, $value);
        return $this;
    }

    /**
     * @param mixed $offset
     *
     * @return RA
     *
     * @throws RAException
     */
    public function unset($offset): self
    {
        $this->offsetUnset($offset);
        return $this;
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
    public function getCurrentKey()
    {
        return $this->key();
    }

    /**
     * @param mixed[] ...$items
     *
     * @return RA
     */
    public function append(...$items): self
    {
        return $this->push(...$items);
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
     *
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

    /**
     * @param string $delimiter
     * @param bool   $recursive
     *
     * @return string
     */
    public function join(string $delimiter = '', bool $recursive = self::REGULAR): string
    {
        return $this->implode($delimiter, $recursive);
    }
}
