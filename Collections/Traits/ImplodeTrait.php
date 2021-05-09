<?php

namespace HKwak\Types\Collections\Traits;

/**
 * Trait ImplodeTrait
 */
trait ImplodeTrait
{
    protected static $stringConvertibleTypes = ['string', 'int', 'float'];

    /**
     * Implodes/Joins all elements of the collection by provided glue. Returns empty string if collection is empty
     *
     * @param string $glue
     *
     * @return string
     */
    public function implode(string $glue): string
    {
        if (in_array(self::ARRAY_TYPE, self::$stringConvertibleTypes)
            || method_exists(self::ARRAY_TYPE, '__toString')) {
            return implode($glue, $this->getArrayCopy());
        }

        return '';
    }
}
