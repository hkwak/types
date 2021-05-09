<?php

namespace HKwak\Types\Arrays;

/**
 * Interface ArrayableInterface
 *
 * Enforces ability to be converted to an array which represents the object
 */
interface ArrayableInterface
{
    /**
     * Convert the object to an array by accessing all properties and public getter methods.
     *
     * @return array
     */
    public function toArray(): array;
}
