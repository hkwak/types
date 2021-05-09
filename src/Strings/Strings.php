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

    public static function stripPolish(string $text): string
    {
        $text = strtolower($text);
        $text = str_replace('/', '', $text);
        $text = str_replace('ś', 's', $text);
        $text = str_replace('ć', 'c', $text);
        $text = str_replace('ó', 'o', $text);
        $text = str_replace('ń', 'n', $text);
        $text = str_replace('ł', 'l', $text);
        $text = str_replace('ę', 'e', $text);
        $text = str_replace('ź', 'z', $text);
        $text = str_replace('ż', 'z', $text);
        $text = str_replace('Ś', 's', $text);
        $text = str_replace('Ć', 'c', $text);
        $text = str_replace('Ó', 'o', $text);
        $text = str_replace('Ń', 'n', $text);
        $text = str_replace('Ł', 'l', $text);
        $text = str_replace('Ę', 'e', $text);
        $text = str_replace('Ź', 'z', $text);
        $text = str_replace('Ż', 'z', $text);
        $text = preg_replace("/[ ]+/", ' ', $text);
        $text = preg_replace('/[_]+/', '-', $text);
        $text = preg_replace("/[^\-a-z0-9\s]+/", '', $text);
        $text = str_replace(' ', '-', $text);
        $text = preg_replace('/-{2,}/', '-', $text);

        return trim($text, '-');
    }

    public static function shortenText($text, $length = 100): string
    {
        $text = str_replace('&nbsp;', '', $text);

        return strlen($text) > 50 ? substr($text, 0, $length)."..." : $text;
    }

    /**
     * Convert a string to a delimited lower case string
     *
     * @param string $string
     * @param string $delimiter
     *
     * @return string
     */
    public static function toDelimitedtLowerCase($string, $delimiter = '-')
    {
        return preg_replace("/[^a-z0-9 ]/", $delimiter, strtolower($string));
    }
}