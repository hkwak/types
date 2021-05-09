<?php

namespace HKwak\Types\Strings;

/**
 * Class StringType
 *
 * Provides useful functions to operate on String
 */
class Strings
{
    /**
     * Converts given string from camel to snake notation
     *
     * @param string $str
     *
     * @return string
     */
    public function toSnakeCase(string $str): string
    {
        $str[0] = strtolower($str[0]);

        return preg_replace_callback(
            '/([A-Z])/',
            function (string $char) {
                return "_".strtolower($char[1]);
            },
            $str
        );
    }

    /**
     * Converts given string from snake to camel notation
     *
     * @param string $str
     *
     * @param bool $capitaliseFirst
     *
     * @return string
     */
    public function toCameCase(string $str, $capitaliseFirst = false): string
    {
        if ($capitaliseFirst) {
            $str[0] = strtoupper($str[0]);
        }

        return preg_replace_callback(
            '/_([a-z])/',
            function (string $char) {
                return strtoupper($char[1]);
            },
            $str
        );
    }
}