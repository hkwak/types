<?php

namespace HKwak\Types\Interfaces;

/**
 * Interface ComparableInterface
 *
 * Provides the ability to compare objects
 */
interface ComparableInterface
{
    /**
     * The method compares the object to another object. The method should return -1 if object is lower that the $object parameter
     * 0 if they are the same and 1 if the object is higher than the $object parameter
     *
     * @param mixed $object
     *
     * @return int
     */
    public function compareTo($object): int;
}
