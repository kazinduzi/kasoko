<?php

namespace Helpers;

/**
 * Description of url
 *
 * @author Emmanuel_Leonie
 */
use library\Currency;

class Str
{

    protected static $currency;

    /**
     *
     * @param string $str
     * @return string
     */
    public static function title($str)
    {
        return self::toAscii($str);
    }

    /**
     *
     * @param string $str
     * @param string $replace
     * @return string
     */
    public static function toAscii($str, $replace = array())
    {
        if (!empty($replace)) {
            $str = str_replace((array) $replace, ' ', $str);
        }
        if (function_exists('iconv')) {
            $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        } else {
            $str = self::normalize($str);
        }
        $str = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
        return $str;
    }

    /**
     * Normalize the string
     *
     * @param string $str
     * @return string
     */
    public static function normalize($str)
    {
        $table = array(
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
            'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r',
        );
        return strtr($str, $table);
    }

    /**
     * Returns a part of UTF-8 string
     *
     * @param string $s
     * @param int $start
     * @param int $length
     * @return string
     */
    public static function substr($s, $start, $length = null)
    {
        if ($length === null) {
            $length = self::length($s);
        }
        return function_exists('mb_substr') ? mb_substr($s, $start, $length, 'UTF-8') : iconv_substr($s, $start, $length, 'UTF-8'); // MB is much faster
    }

    /**
     * Returns UTF-8 string length
     *
     * @param string $str
     * @return int
     */
    public static function length($str)
    {
        if ('UTF-8' === strtoupper(Kazinduzi::$encoding)) {
            return mb_strlen($str, Kazinduzi::$encoding);
        }
        return strlen(utf8_decode($str)); // fastest way
    }

    /**
     * Slugify the string
     *
     * @param string $str
     * @param string $replace
     * @param string $delimiter
     * @param integer $maxLength
     * @return string
     */
    public static function slugify($str, $replace = array(), $delimiter = '-', $maxLength = 200)
    {
        if (!empty($replace)) {
            $str = str_replace((array) $replace, ' ', $str);
        }
        $str = self::toAscii($str);
        $str = preg_replace("%[^-/+|\w ]%", '', $str);
        $str = strtolower(substr($str, 0, $maxLength));
        $str = preg_replace("/[\/_|+ -]+/", $delimiter, $str);
        return trim($str, '-');
    }

    /**
     * Alternate normalize the string
     *
     * @param string $str
     * @return string
     */
    public static function _normalize_($str)
    {
        // standardize line endings to unix-like
        $str = str_replace("\r\n", "\n", $str); // DOS
        $str = strtr($str, "\r", "\n"); // Mac
        // remove control characters; leave \t + \n
        $str = preg_replace('#[\x00-\x08\x0B-\x1F\x7F]+#', '', $str);
        // right trim
        $str = preg_replace("#[\t ]+$#m", '', $str);
        // leading and trailing blank lines
        $str = trim($str, "\n");
        return $str;
    }

    /**
     * Does a haystack contains a needle?
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function contains($haystack, $needle)
    {
        return strpos($haystack, $needle) !== false;
    }

    /**
     * Reverse string
     *
     * @param string $s
     * @return string
     */
    public static function reverse($s)
    {
        return @iconv('UTF-32LE', 'UTF-8', strrev(@iconv('UTF-8', 'UTF-32BE', $s)));
    }

    /**
     * Capitalize string
     *
     * @param string $s
     * @return string
     */
    public static function capitalize($s)
    {
        return mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Generate random string
     *
     * @param integer $length
     * @param bool $special_chars
     * @param bool $extra_special_chars
     * @return string
     */
    public static function random($length = 10, $special_chars = true, $extra_special_chars = false)
    {
        $seed = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        if ($special_chars) {
            $seed .= '!@#$%^&*()';
        }
        if ($extra_special_chars) {
            $seed .= '-_ []{}<>~`+=,.;:/?|';
        }
        $seedLen = strlen($seed);
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            if ($i % 5 === 0) {
                $rand = lcg_value();
                $rand2 = microtime(true);
            }
            $rand *= $seedLen;
            $str .= $seed[($rand + $rand2) % $seedLen];
            $rand -= (int) $rand;
        }
        return $str;
    }

    /**
     *
     * @param double /integer $amount
     * @param string $currency
     * @return string
     */
    public static function currency_format($amount)
    {
        if (null === static::$currency) {
            static::$currency = Currency::getInstance()->getCurrent();
        }
        $amount *= static::$currency->rate;

        switch (static::$currency->getCode()) {
            case 'BIF':
                $symbol = 'BIF';
                break;
            case 'GBP':
                $symbol = '&pound;';
                break;
            case 'USD':
                $symbol = '&dollar;';
                break;
            case 'EUR':
            default:
                $symbol = '&euro;';
                break;
        }
        return sprintf($symbol . ' %0.2f', $amount);
    }

    /**
     *
     * @param string $dangerous_filename
     * @param string $platform
     * @return string
     */
    public static function sanitizeFilename($dangerous_filename, $platform = 'Unix')
    {
        if (in_array(strtolower($platform), array('unix', 'linux'))) {
            $dangerous_characters = array(" ", '"', "'", "&", "/", "\\", "?", "#");
        } else {
            return $dangerous_filename;
        }
        return str_replace($dangerous_characters, '_', $dangerous_filename);
    }

    /**
     * Truncate a string
     *
     * @param string $string
     * @param integer $limit
     * @param string $break
     * @param string $pad
     * @return string
     */
    public static function truncate($string, $limit, $break = ".", $pad = "...")
    {
        // return with no change if string is shorter than $limit
        if (strlen($string) <= $limit) {
            return $string;
        }
        // is $break present between $limit and the end of the string?
        if (false !== $breakpoint = strpos($string, $break, $limit)) {
            if ($breakpoint < strlen($string) - 1) {
                $string = substr($string, 0, $breakpoint) . $pad;
            }
        }
        return $string;
    }

    /**
     * Compare two strings to avoid timing attacks
     *
     * C function memcmp() internally used by PHP, exits as soon as a difference
     * is found in the two buffers. That makes possible of leaking
     * timing information useful to an attacker attempting to iteratively guess
     * the unknown string (e.g. password).
     *
     * @param  string $expected
     * @param  string $actual
     * @return bool
     */
    public static function compareStrings($expected, $actual)
    {
        // Prevent issues if string length is 0
        $expected .= chr(0);
        $actual .= chr(0);
        $lenExpected = strlen($expected);
        $lenActual = strlen($actual);
        // Set the result to the difference between the lengths
        $result = $lenExpected - $lenActual;
        // Note that we ALWAYS iterate over the user-supplied length
        // This is to prevent leaking length information
        for ($i = 0; $i < $lenActual; $i++) {
            // Using % here is a trick to prevent notices
            // It's safe, since if the lengths are different
            // $result is already non-0
            $result |= (ord($expected[$i % $lenExpected]) ^ ord($actual[$i]));
        }

        // They are only identical strings if $result is exactly 0...
        return $result === 0;
    }

}
