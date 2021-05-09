<?php

namespace HKwak\Types\Collections;

use HKwak\Types\Collections\Traits\ImplodeTrait;
use InvalidArgumentException;

/**
 * Class IntCollection
 *
 * IntCollection represents a collection of integer values
 */
class IntCollection extends AbstractCollection
{
    use ImplodeTrait;

    const ARRAY_TYPE = 'int';

    public function __construct($input = [], $flags = 0, $iteratorClass = 'ArrayIterator')
    {
        foreach ($input as $index => $element) {
            if (is_numeric($element)) {
                $input[$index] = (int) $element;
            } else {
                throw new InvalidArgumentException('Parameter $input of IntCollection::__construct should only contain numeric elements. Provided: '.print_r($input, true));
            }
        }
        parent::__construct($input, $flags, $iteratorClass);
    }

    /**
     * Checking if provided integer value exists in the collection
     *
     * @param int $value
     *
     * @return bool
     */
    public function contains(int $value): bool
    {
        return in_array($value, $this->getArrayCopy());
    }
}
