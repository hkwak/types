<?php

namespace HKwak\Types\Collections;

use ArrayObject;
use HKwak\Types\Collections\Exceptions\OutOfRangeException;
use InvalidArgumentException;
use RQuadling\TypedArray\TypedArray;

/**
 * Class AbstractCollection
 */
class AbstractCollection extends TypedArray
{
    public function __toString(): string
    {
        return implode(',', $this->getArrayCopy());
    }

    /**
     * @param AbstractCollection $collection
     *
     * @return AbstractCollection
     */
    public function merge(AbstractCollection $collection): self
    {
        $this->exchangeArray(array_merge($this->getArrayCopy(), $collection->getArrayCopy()));

        return $this;
    }

    /**
     * Returns the unique elements of the collection
     *
     * @return AbstractCollection
     */
    public function unique(): self
    {
        $this->exchangeArray(array_unique($this->getArrayCopy()));

        return $this;
    }

    /**
     * Getting keys of the collection
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->getArrayCopy());
    }

    /**
     * Remove a portion of the array and optionally replace it with something else.
     *
     * @param int $offset
     * @param int|null $length
     * @param mixed|null $replacement
     *
     * @return static
     * @see array_splice()
     *
     */
    public function splice(int $offset, int $length = null, $replacement = null)
    {
        $data = $this->getArrayCopy();

        if (is_null($length) && is_null($replacement)) {
            $result = array_splice($data, $offset);
        } else {
            $result = array_splice($data, $offset, $length, $replacement);
        }
        $this->exchangeArray($data);

        return new static($result);
    }

    /**
     * Extract a slice of the array.
     *
     * @param int $offset
     * @param int|null $length
     * @param bool $preserveKeys
     *
     * @return static
     * @see array_slice()
     *
     */
    public function slice(int $offset, int $length = null, bool $preserveKeys = false)
    {
        return new static(array_slice($this->getArrayCopy(), $offset, $length, $preserveKeys));
    }

    /**
     * Sort an array.
     *
     * @param int $sortFlags
     *
     * @return bool
     * @see sort()
     *
     */
    public function sort($sortFlags = SORT_REGULAR)
    {
        $data = $this->getArrayCopy();
        $result = sort($data, $sortFlags);
        $this->exchangeArray($data);

        return $result;
    }

    /**
     * Apply a user supplied function to every member of an array
     *
     * @param callable $callback
     * @param mixed|null $userData
     *
     * @return bool Returns true on success, otherwise false
     *
     * @see array_walk
     *
     * @see array_walk()
     */
    public function walk(callable $callback, $userData = null)
    {
        $data = $this->getArrayCopy();
        $result = array_walk($data, $callback, $userData);
        $this->exchangeArray($data);

        return $result;
    }

    /**
     * @param mixed $columnKey
     *
     * @return array
     * @see array_column
     *
     */
    public function column($columnKey): array
    {
        $data = $this->getArrayCopy();

        return array_column($data, $columnKey);

    }

    /**
     * @param callable $mapper Will be called as $mapper(mixed $item)
     *
     * @return ArrayObject A collection of the results of $mapper(mixed $item)
     */
    public function map(callable $mapper): ArrayObject
    {
        $data = $this->getArrayCopy();
        $result = array_map($mapper, $data);

        return new ArrayObject($result);
    }

    /**
     * Applies the callback function $callable to each item in the collection.
     *
     * @param callable $callable
     */
    public function each(callable $callable)
    {
        foreach ($this as &$item) {
            $callable($item);
        }
        unset($item);
    }

    /**
     * Inserting the provided element at the index. If index is negative, it will be calculated from the end of the Array Object
     *
     * @param int $index
     * @param mixed $element
     */
    public function insert(int $index, $element)
    {
        $data = $this->getArrayCopy();
        if ($index < 0) {
            $index = $this->count() + $index;
        }

        $data = array_merge(array_slice($data, 0, $index + 1, true), [$element], array_slice($data, $index + 1, null, true));
        $this->exchangeArray($data);
    }

    /**
     * Removing the Element at the specified index. If index is negative, it will be calculated from the end of the Collection where -1 means the last element
     *
     * @param int $index
     */
    public function remove(int $index)
    {
        $data = $this->getArrayCopy();
        if ($index < 0) {
            $index = $this->count() + $index;
        }

        $data = array_merge(array_slice($data, 0, max(0, $index), true), array_slice($data, $index + 1, null, true));
        $this->exchangeArray($data);
    }

    /**
     * Returns the item in the collection at $index.
     *
     * @param int $index
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     * @throws OutOfRangeException
     */
    public function at(int $index)
    {
        $this->validateIndex($index);

        return $this[$index];
    }

    /**
     * Validates a number to be used as an index
     *
     * @param int $index The number to be validated as an index
     *
     * @throws OutOfRangeException
     * @throws InvalidArgumentException
     */
    private function validateIndex(int $index)
    {
        $exists = $this->indexExists($index);

        if (!$exists) {
            throw new OutOfRangeException('Index out of bounds of collection');
        }
    }

    /**
     * Returns true if $index is within the collection's range and returns false
     * if it is not.
     *
     * @param int $index
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function indexExists(int $index)
    {
        if ($index < 0) {
            throw new InvalidArgumentException('Index must be a non-negative integer');
        }

        return $index < $this->count();
    }

    /**
     * Finding the first element in the Array, for which $callback returns true
     *
     * @param callable $callback
     *
     * @return mixed|null Element Found in the Array or null
     */
    public function find(callable $callback)
    {
        foreach ($this as $element) {
            if ($callback($element)) {
                return $element;
            }
        }

        return null;
    }

    /**
     * Finding the Index of the first element for which $callback returns true
     *
     * @param callable $callback
     *
     * @return int|mixed|null|string
     */
    public function findIndex(callable $callback)
    {
        foreach ($this as $index => $element) {
            if ($callback($element)) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Filtering the array by retrieving only these elements for which callback returns true
     *
     * @param callable $callback
     * @param int $flag Use ARRAY_FILTER_USE_KEY to pass key as the only argument to $callback instead of value.
     *                  Use ARRAY_FILTER_USE_BOTH pass both value and key as arguments to $callback instead of value.
     *
     * @return static
     *
     * @see array_filter
     */
    public function filter(callable $callback, int $flag = 0)
    {
        $data = $this->getArrayCopy();
        $result = array_filter($data, $callback, $flag);

        return new static($result);
    }

    /**
     * @return self
     */
    public function values(): self
    {
        $data = $this->getArrayCopy();

        return new static(array_values($data));
    }

    /**
     * Reset the array pointer to the first element and return the element.
     *
     * @return mixed|null
     */
    public function first()
    {
        if ($this->count() === 0) {
            return null;
        }

        return reset($this);
    }

    /**
     * Reset the array pointer to the last element and return the element.
     *
     * @return mixed|null
     */
    public function last()
    {
        if ($this->count() === 0) {
            return null;
        }

        return end($this);
    }

    /**
     * Apply a user supplied function to every member of an array
     *
     * @param bool $preserveKeys
     *
     * @return static
     * @see array_reverse
     *
     */
    public function reverse(bool $preserveKeys = false)
    {
        return new static(array_reverse($this->getArrayCopy(), $preserveKeys));
    }
}
