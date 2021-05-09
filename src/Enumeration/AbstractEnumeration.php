<?php

namespace HKwak\Types\Enumeration;

/**
 * Class AbstractEnumeration
 *
 * Improved version of AbstractEnumeration class which provides values() method
 */
abstract class AbstractEnumeration extends \Eloquent\Enumeration\AbstractEnumeration
{
    /**
     * Getting all values of the Enum type
     *
     * @return array
     */
    public static function values(): array
    {
        return array_map(
            function (AbstractEnumeration $element) {
                return $element->value();
            },
            self::members()
        );
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
