<?php
declare(strict_types=1);

namespace WernerDweight\RA;

use WernerDweight\RA\Exception\RAException;

/**
 * Class RA.
 *
 * @implements \ArrayAccess<string|int, mixed>
 * @implements \Iterator<string|int, mixed>
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ShortClassName)
 */
final class RA implements \Countable, \ArrayAccess, \Iterator
{
    /**
     * @var bool
     */
    public const RECURSIVE = true;

    /**
     * @var bool
     */
    public const REGULAR = false;

    /**
     * @var bool
     */
    public const AS_VALUES = true;

    /**
     * @var bool
     */
    public const AS_KEYS = false;

    /**
     * @var int
     */
    public const ARRAY_FILTER_VALUE = 0;

    /**
     * @var mixed[]
     */
    private $data = [];

    // magical

    /**
     * RA constructor.
     *
     * @param mixed[] $data
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
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * @return mixed
     *
     * @throws RAException
     */
    public function __get(string $name)
    {
        if (true !== $this->offsetExists($name)) {
            throw new RAException(RAException::INVALID_OFFSET, $name);
        }
        return $this->data[$name];
    }

    /**
     * @throws RAException
     */
    public function __unset(string $name): void
    {
        if (true !== $this->offsetExists($name)) {
            throw new RAException(RAException::INVALID_OFFSET, $name);
        }
        unset($this->data[$name]);
    }

    /**
     * @throws RAException
     */
    public function getBool(int|string|null $offset): bool
    {
        /** @var bool $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @throws RAException
     */
    public function getBoolOrNull(int|string|null $offset): ?bool
    {
        /** @var null|bool $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @throws RAException
     */
    public function getInt(int|string|null $offset): int
    {
        /** @var int $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @throws RAException
     */
    public function getIntOrNull(int|string|null $offset): ?int
    {
        /** @var null|int $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @throws RAException
     */
    public function getFloat(int|string|null $offset): float
    {
        /** @var float $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @throws RAException
     */
    public function getFloatOrNull(int|string|null $offset): ?float
    {
        /** @var null|float $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @throws RAException
     */
    public function getString(int|string|null $offset): string
    {
        /** @var string $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @throws RAException
     */
    public function getStringOrNull(int|string|null $offset): ?string
    {
        /** @var null|string $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @return mixed[]
     *
     * @throws RAException
     */
    public function getArray(int|string|null $offset): array
    {
        /** @var mixed[] $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @return mixed[]|null
     *
     * @throws RAException
     */
    public function getArrayOrNull(int|string|null $offset): ?array
    {
        /** @var null|mixed[] $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @throws RAException
     */
    public function getRA(int|string|null $offset): self
    {
        /** @var RA $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @throws RAException
     */
    public function getRAOrNull(int|string|null $offset): ?self
    {
        /** @var null|RA $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @throws RAException
     */
    public function getCallable(int|string|null $offset): callable
    {
        /** @var callable $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @throws RAException
     */
    public function getCallableOrNull(int|string|null $offset): ?callable
    {
        /** @var null|callable $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @return iterable<mixed>
     *
     * @throws RAException
     */
    public function getIterable(int|string|null $offset): iterable
    {
        /** @var iterable<mixed> $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @return iterable<mixed>|null
     *
     * @throws RAException
     */
    public function getIterableOrNull(int|string|null $offset): ?iterable
    {
        /** @var null|iterable<mixed> $value */
        $value = $this->get($offset);
        return $value;
    }

    /**
     * @throws RAException
     */
    public function increment(int|string|null $offset): self
    {
        $currentValue = $this->get($offset);
        if (true !== is_int($currentValue) && true !== is_float($currentValue)) {
            throw new RAException(RAException::INVALID_INCREMENT_TYPE);
        }
        $this->set($offset, ++$currentValue);
        return $this;
    }

    /**
     * @throws RAException
     */
    public function decrement(int|string|null $offset): self
    {
        $currentValue = $this->get($offset);
        if (true !== is_int($currentValue) && true !== is_float($currentValue)) {
            throw new RAException(RAException::INVALID_INCREMENT_TYPE);
        }
        $this->set($offset, --$currentValue);
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function toArray(bool $recursive = self::REGULAR): array
    {
        if (self::RECURSIVE === $recursive) {
            $data = [];
            foreach ($this->data as $key => $value) {
                $data[$key] = $value instanceof self
                    ? $value->toArray($recursive)
                    : $value;
            }
            return $data;
        }
        return $this->data;
    }

    // main

    /**
     * @param int|string $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * @param  int|string  $offset
     * @throws RAException
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (true !== $this->offsetExists($offset)) {
            throw new RAException(RAException::INVALID_OFFSET, (string)$offset);
        }
        return $this->data[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    /**
     * @param  int|string  $offset
     * @throws RAException
     */
    public function offsetUnset(mixed $offset): void
    {
        if (true !== $this->offsetExists($offset)) {
            throw new RAException(RAException::INVALID_OFFSET, (string)$offset);
        }
        unset($this->data[$offset]);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function current(): mixed
    {
        return current($this->data);
    }

    public function next(): void
    {
        next($this->data);
    }

    public function key(): int|string|null
    {
        return key($this->data);
    }

    public function valid(): bool
    {
        $key = $this->key();
        return null !== $key;
    }

    public function rewind(): void
    {
        reset($this->data);
    }

    /**
     * @param mixed[] ...$items
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

    public function changeKeyCase(int $case = CASE_LOWER): self
    {
        return new self(array_change_key_case($this->data, $case));
    }

    /**
     * @param int<1, max> $size
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
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
     */
    public function column($column, $indexBy): self
    {
        return new self(array_column($this->data, $column, $indexBy));
    }

    /**
     * @param mixed[]|RA $dataToCombine
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

    public function countValues(): self
    {
        return new self(array_count_values($this->data));
    }

    /**
     * @param (RA)[] ...$args
     */
    public function diffAssoc(...$args): self
    {
        return new self(array_diff_assoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA)[] ...$args
     */
    public function diffKey(...$args): self
    {
        return new self(array_diff_key($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     */
    public function diffUassoc(...$args): self
    {
        return new self(array_diff_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     */
    public function diffUkey(...$args): self
    {
        return new self(array_diff_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA)[] ...$args
     */
    public function diff(...$args): self
    {
        return new self(array_diff($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param mixed $value
     */
    public function fillKeys($value): self
    {
        return new self(array_fill_keys($this->data, $value));
    }

    /**
     * @param mixed $value
     */
    public function fill(int $startIndex, int $num, $value): self
    {
        $this->data = array_fill($startIndex, $num, $value);
        return $this;
    }

    public function filter(callable $callback, int $flag = self::ARRAY_FILTER_VALUE): self
    {
        return new self(array_filter($this->data, $callback, $flag));
    }

    /**
     * @throws \Safe\Exceptions\ArrayException
     */
    public function flip(): self
    {
        return new self(\Safe\array_flip($this->data));
    }

    /**
     * @param (RA)[] ...$args
     */
    public function intersectAssoc(...$args): self
    {
        return new self(array_intersect_assoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA)[] ...$args
     */
    public function intersectKey(...$args): self
    {
        return new self(array_intersect_key($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     */
    public function intersectUassoc(...$args): self
    {
        return new self(array_intersect_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     */
    public function intersectUkey(...$args): self
    {
        return new self(array_intersect_ukey($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA)[] ...$args
     */
    public function intersect(...$args): self
    {
        return new self(array_intersect($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function keys(mixed $searchValue = null, bool $strict = false): self
    {
        $args = [];
        $args[] = $this->data;
        if (null !== $searchValue) {
            $args[] = $searchValue;
            $args[] = $strict;
        }
        return new self(array_keys(...$args));
    }

    /**
     * @param (RA)[]   ...$args
     */
    public function map(callable $callback, ...$args): self
    {
        return new self(array_map($callback, $this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA)[] ...$args
     */
    public function mergeRecursive(...$args): self
    {
        return new self(array_merge_recursive($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA)[] ...$args
     */
    public function merge(...$args): self
    {
        return new self(array_merge($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param mixed $value
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
     * @return mixed|RA
     */
    public function random(int $length = 1): mixed
    {
        $keys = array_rand($this->data, $length);
        if (true === is_array($keys)) {
            return new self(array_map(function ($key) {
                return $this->data[$key];
            }, $keys));
        }
        return $this->data[$keys];
    }

    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->data, $callback, $initial);
    }

    /**
     * @param mixed[] ...$args
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
     * @throws \Safe\Exceptions\ArrayException
     */
    public function replace(...$args): self
    {
        return new self((array)\Safe\array_replace($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    public function reverse(): self
    {
        return new self(array_reverse($this->data));
    }

    public function search(mixed $needle): mixed
    {
        $key = array_search($needle, $this->data, true);
        if (false !== $key) {
            return $this->data[$key];
        }
        return null;
    }

    public function shift(): mixed
    {
        return array_shift($this->data);
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): self
    {
        return new self(array_slice($this->data, $offset, $length, $preserveKeys));
    }

    public function splice(int $offset, ?int $length = null, mixed $replacement = null): self
    {
        if (null === $length) {
            $length = $this->count();
        }
        if (null === $replacement) {
            $replacement = [];
        }
        return new self(array_splice($this->data, $offset, $length, $replacement));
    }

    public function sum(): float|int
    {
        return array_sum($this->data);
    }

    /**
     * @param  RA|array<mixed>|callable ...$args
     * @return $this
     */
    public function udiffAssoc(self|array|callable ...$args): self
    {
        /** @var array<mixed[]> $convertedArgs */
        $convertedArgs = $this->convertArgumentsToPlainArrays($args);
        return new self(array_udiff_assoc($this->data, ...$convertedArgs));
    }

    /**
     * @param RA|array<mixed>|callable ...$args
     */
    public function udiffUassoc(self|array|callable ...$args): self
    {
        /** @var array<mixed[]> $convertedArgs */
        $convertedArgs = $this->convertArgumentsToPlainArrays($args);
        return new self(array_udiff_uassoc($this->data, ...$convertedArgs));
    }

    /**
     * @param (RA|callable)[] ...$args
     */
    public function udiff(...$args): self
    {
        return new self(array_udiff($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     */
    public function uintersectAssoc(...$args): self
    {
        return new self(array_uintersect_assoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     */
    public function uintersectUassoc(...$args): self
    {
        return new self(array_uintersect_uassoc($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    /**
     * @param (RA|callable)[] ...$args
     */
    public function uintersect(...$args): self
    {
        return new self(array_uintersect($this->data, ...$this->convertArgumentsToPlainArrays($args)));
    }

    public function unique(int $sortFlags = SORT_STRING): self
    {
        return new self(array_unique($this->data, $sortFlags));
    }

    /**
     * @param mixed[] ...$args
     */
    public function unshift(...$args): self
    {
        array_unshift($this->data, ...$args);
        return $this;
    }

    public function values(): self
    {
        return new self(array_values($this->data));
    }

    public function walkRecursive(callable $callback, mixed $payload = null): self
    {
        \Safe\array_walk_recursive($this->data, $callback, $payload);
        return $this;
    }

    public function walk(callable $callback, mixed $payload = null): self
    {
        array_walk($this->data, $callback, $payload);
        return $this;
    }

    /**
     * @throws \Safe\Exceptions\ArrayException
     */
    public function arsort(int $sortFlags = SORT_REGULAR): self
    {
        \Safe\arsort($this->data, $sortFlags);
        return $this;
    }

    /**
     * @throws \Safe\Exceptions\ArrayException
     */
    public function asort(int $sortFlags = SORT_REGULAR): self
    {
        \Safe\asort($this->data, $sortFlags);
        return $this;
    }

    public function end(): mixed
    {
        return end($this->data);
    }

    /**
     * @param mixed $needle
     */
    public function contains($needle): bool
    {
        return in_array($needle, $this->data, true);
    }

    /**
     * @throws \Safe\Exceptions\ArrayException
     */
    public function krsort(int $sortFlags = SORT_REGULAR): self
    {
        \Safe\krsort($this->data, $sortFlags);
        return $this;
    }

    /**
     * @throws \Safe\Exceptions\ArrayException
     */
    public function ksort(int $sortFlags = SORT_REGULAR): self
    {
        \Safe\ksort($this->data, $sortFlags);
        return $this;
    }

    public function natcasesort(): self
    {
        natcasesort($this->data);
        return $this;
    }

    public function natsort(): self
    {
        natsort($this->data);
        return $this;
    }

    public function prev(): mixed
    {
        return prev($this->data);
    }

    public function range(float|int|string $start, float|int|string $end, int $step = 1): self
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

    public function rsort(int $sortFlags = SORT_REGULAR): self
    {
        rsort($this->data, $sortFlags);
        return $this;
    }

    /**
     * @throws \Safe\Exceptions\ArrayException
     */
    public function shuffle(): self
    {
        \Safe\shuffle($this->data);
        return $this;
    }

    /**
     * @throws \Safe\Exceptions\ArrayException
     */
    public function sort(int $sortFlags = SORT_REGULAR): self
    {
        \Safe\sort($this->data, $sortFlags);
        return $this;
    }

    public function uasort(callable $callback): self
    {
        uasort($this->data, $callback);
        return $this;
    }

    public function uksort(callable $callback): self
    {
        uksort($this->data, $callback);
        return $this;
    }

    /**
     * @throws \Safe\Exceptions\ArrayException
     */
    public function usort(callable $callback): self
    {
        \Safe\usort($this->data, $callback);
        return $this;
    }

    /**
     * @throws \Safe\Exceptions\StringsException
     */
    public function implode(string $delimiter = '', bool $recursive = self::REGULAR): string
    {
        if (self::RECURSIVE === $recursive) {
            /** @var string[] $data */
            $data = [];
            foreach ($this->data as $key => $value) {
                $data[$key] = $value instanceof self
                    ? \Safe\sprintf('{%s}', $value->implode($delimiter, $recursive))
                    : $value;
            }
            return implode($delimiter, $data);
        }
        return implode($delimiter, $this->data);
    }

    // Aliases

    public function keyExists(int|string $key): bool
    {
        return $this->offsetExists($key);
    }

    public function has(mixed $needle): bool
    {
        return $this->contains($needle);
    }

    public function hasValue(mixed $needle): bool
    {
        return $this->contains($needle);
    }

    public function hasKey(int|string $key): bool
    {
        return $this->offsetExists($key);
    }

    public function pos(): mixed
    {
        return $this->current();
    }

    public function size(): int
    {
        return $this->count();
    }

    public function length(): int
    {
        return $this->count();
    }

    /**
     * @throws RAException
     */
    public function get(int|string $offset = null): mixed
    {
        if (null === $offset) {
            return $this->current();
        }
        return $this->offsetGet($offset);
    }

    public function set(int|string|null $offset, mixed $value): self
    {
        $this->offsetSet($offset, $value);
        return $this;
    }

    /**
     * @throws RAException
     */
    public function unset(int|string $offset): self
    {
        $this->offsetUnset($offset);
        return $this;
    }

    public function previous(): mixed
    {
        return $this->prev();
    }

    public function getCurrentIndex(): mixed
    {
        return $this->key();
    }

    public function getCurrentKey(): mixed
    {
        return $this->key();
    }

    /**
     * @param mixed[] ...$items
     */
    public function append(...$items): self
    {
        return $this->push(...$items);
    }

    public function aggregateValues(): self
    {
        return $this->countValues();
    }

    public function getKeys(): self
    {
        return $this->keys();
    }

    public function getProduct(): float|int
    {
        return $this->product();
    }

    public function getRandomEntry(): mixed
    {
        return $this->random();
    }

    public function getRandomValue(): mixed
    {
        return $this->random();
    }

    public function getRandomItem(): mixed
    {
        return $this->random();
    }

    public function find(mixed $needle): mixed
    {
        return $this->search($needle);
    }

    public function getSum(): float|int
    {
        return $this->sum();
    }

    public function getValues(): self
    {
        return $this->values();
    }

    public function entries(): self
    {
        return $this->values();
    }

    public function getEntries(): self
    {
        return $this->values();
    }

    public function items(): self
    {
        return $this->values();
    }

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

    public function join(string $delimiter = '', bool $recursive = self::REGULAR): string
    {
        return $this->implode($delimiter, $recursive);
    }

    // helpers

    /**
     * @template T
     * @param array<int|string, T> $arrays
     *
     * @return array<array<mixed>|T>
     */
    private function convertArgumentsToPlainArrays(array $arrays): array
    {
        return array_map(function ($entry) {
            if ($entry instanceof self) {
                return $entry->toArray();
            }
            return $entry;
        }, $arrays);
    }
}
