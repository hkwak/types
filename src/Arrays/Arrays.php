<?php

namespace HKwak\Types\Arrays;

use Serializable;

/**
 * Class Arrays
 *
 * Provides some additional tools to operate on arrays
 */
class Arrays
{
    /**
     * Converting the associative array to the flat array so each non-scalar element will be serialised and each non-serializable or null element will be removed be dropped
     *
     * @param array $data
     *
     * @return array
     */
    public static function toFlat(array $data): array
    {
        return array_filter(
            array_map(
                function ($element) {
                    if (is_scalar($element)) {
                        return (string) $element;
                    } elseif ($element instanceof Serializable) {
                        return serialize($element);
                    } else {
                        return null;
                    }
                },
                $data
            )
        );
    }
}
