<?php

namespace HKwak\Types\Collections;

use InvalidArgumentException;

/**
 * Class CollectionFactory
 */
class CollectionFactory
{
    /**
     * Creates the Collection object , specified as $collectionClassName , populated by provided $result
     *
     * @param string $collectionClassName
     * @param array $result |null
     *
     * @throws InvalidArgumentException
     *
     * @return mixed ( Collection subclass )
     */
    public static function createFromArray(string $collectionClassName, array $result = null)
    {
        if (!is_subclass_of($collectionClassName, AbstractCollection::class)) {
            throw new InvalidArgumentException('Parameter $collectionClassName should represents the TypedArray subclass');
        }
        $collection = new $collectionClassName();
        if (is_null($result)) {
            return $collection;
        }

        /**
         * @var AbstractCollection $collectionClassName
         */
        /**
         * @var AbstractCollection $collection
         */
        $elementName = $collectionClassName::ARRAY_TYPE;
        foreach ($result as $data) {
            // checking for primitive types
            $firstElement = reset($data);
            if ($elementName === 'int' && is_numeric($firstElement)) {
                $collection->append((int) $firstElement);
            } elseif ($elementName === 'string' && is_string($firstElement)) {
                $collection->append($firstElement);
            } elseif (class_exists($elementName) && is_array($data)) {
                $collection->append(new $elementName($data));
            } else {
                throw new InvalidArgumentException(
                    'Parameter $collectionClassName should point at the collection of objects, strings or integers. '
                    .PHP_EOL.$collectionClassName.':'.$elementName
                );
            }
        }

        return $collection;
    }
}