<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

require LIB_PATH . '/password.php';

if (!function_exists('render')) {

    /**
     * @param $template
     * @param array $data
     */
    function render($template, $data = array())
    {
        extract($data, EXTR_SKIP | EXTR_REFS);
        ob_start();
        if (THEME_PATH && is_file($templateFile = THEME_PATH . DS . $template)) {
            require $templateFile;
        } elseif (is_file($templateFile = KAZINDUZI_PATH . '/elements/layouts/' . $template)) {
            require $templateFile;
        }
        ob_end_flush();
    }

}

if (!function_exists('uses')) {

    function uses()
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            $arg = strtolower($arg);
            if (file_exists($filename = KAZINDUZI_PATH . '/library/' . $arg . '.class.php')) {
                require $filename;
            } elseif (file_exists($filename = KAZINDUZI_PATH . '/helpers/' . $arg . '.class.php')) {
                require $filename;
            } else {
                throw new \Exception("File {$filename} not found");
            }
        }
    }

}

if (!function_exists('arrayFirst')) {

    function arrayFirst($array)
    {
        if (is_array($array) && count($array)) {
            return $array[0];
        }
        return array();
    }

}

if (!function_exists('arrayFlatten')) {

    function arrayFlatten($array)
    {
        $flatten = array();
        if (!$array || !is_array($array)) {
            return '';
        }
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $flatten = arrayFlatten($val);
            } else {
                $flatten[$key] = $val;
            }
        }
        return $flatten;
    }

}

if (!function_exists('arrayToObject')) {

    function arrayToObject($array = array())
    {
        return (object) $array;
    }

}

if (!function_exists('redirect')) {

    function redirect($url)
    {
        if (isset($_SESSION)) {
            session_write_close();
        }
        header("Location:" . $url);
        exit;
    }

}

if (!function_exists('stringEndsWith')) {

    function stringEndsWith($string, $end)
    {
        return substr($string, -strlen($end)) == $end;
    }

}

if (!function_exists('makeString')) {

    function makeString($string, $htmlize = false)
    {
        if (!empty($string)) {
            $from = mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true);
            $to = $htmlize ? 'HTML-ENTITIES' : 'UTF-8';
            $string = mb_convert_encoding($string, $to, $from);
        }
        return $string;
    }

}

if (!function_exists('singular')) {

    function singular($str)
    {
        $str = strtolower(trim($str));
        $end = substr($str, -3);
        if ($end === 'ies') {
            $str = substr($str, 0, strlen($str) - 3) . 'y';
        } elseif ($end === 'ses') {
            $str = substr($str, 0, strlen($str) - 2);
        } else {
            $end = substr($str, -1);
            if ($end === 's') {
                $str = substr($str, 0, strlen($str) - 1);
            }
        }
        return $str;
    }

}

if (!function_exists('plural')) {

    function plural($str, $force = false)
    {
        $str = strtolower(trim($str));
        $end = substr($str, -1);
        if ($end == 'y') {
            $vowels = array('a', 'e', 'i', 'o', 'u');
            $str = in_array(substr($str, -2, 1), $vowels) ? $str . 's' : substr($str, 0, -1) . 'ies';
        } elseif ($end === 's') {
            if ($force === true) {
                $str .= 'es';
            }
        } else {
            $str .= 's';
        }
        return $str;
    }

}

if (!function_exists('random_element')) {

    function random_element($array)
    {
        if (!is_array($array)) {
            return $array;
        }
        return $array[array_rand($array)];
    }

}

if (!function_exists('get_request_method')) {

    function get_request_method()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            return 'AJAX';
        } else {
            return $_SERVER['REQUEST_METHOD'];
        }
    }

}

if (!function_exists('get_request_method')) {

    function get_request_method()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            return 'XHR';
        } else {
            return $_SERVER['REQUEST_METHOD'];
        }
    }

}

if (!function_exists('isUTF8')) {

    function isUTF8($string)
    {
        return ($string === '' || preg_match('/^./su', $string));
    }

}

if (!function_exists('escapeHtml')) {

    function escapeHtml($string)
    {
        static $htmlSpecialCharsFlags = ENT_QUOTES;
        if (defined('ENT_SUBSTITUTE')) {
            $htmlSpecialCharsFlags |= ENT_SUBSTITUTE;
        }
        return htmlspecialchars($string, $htmlSpecialCharsFlags, \Kazinduzi::$encoding);
    }

}

if (!function_exists('escapeUrl')) {

    function escapeUrl($string)
    {
        return rawurlencode($string);
    }

}

if (!function_exists('stripslashes_deep')) {

    function stripslashes_deep($values)
    {
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $values[$key] = stripslashes_deep($value);
            }
        } else {
            $values = stripslashes($values);
        }
        return $values;
    }

}

/**
 * Disable magic quotes in runtime if needed
 *
 * @link http://us3.php.net/manual/en/security.magicquotes.disabling.php
 */
if (get_magic_quotes_gpc()) {

    function undoMagicQuotes($array, $deep = true)
    {
        $newArray = array();
        foreach ($array as $key => $value) {
            if (!$deep) {
                $newKey = stripslashes($key);
                if ($newKey !== $key) {
                    unset($array[$key]);
                }
                $key = $newKey;
            }
            $newArray[$key] = is_array($value) ? undoMagicQuotes($value, false) : stripslashes($value);
        }
        return $newArray;
    }

    $_GET = undoMagicQuotes($_GET);
    $_POST = undoMagicQuotes($_POST);
    $_COOKIE = undoMagicQuotes($_COOKIE);
    $_REQUEST = undoMagicQuotes($_REQUEST);
}

if (!function_exists('put')) {

    function put()
    {
        $_PUT = array();
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            $putdata = file_get_contents('php://input');
            $exploded = explode('&', $putdata);
            foreach ($exploded as $pair) {
                $item = explode('=', $pair);
                if (count($item) == 2) {
                    $_PUT[urldecode($item[0])] = urldecode($item[1]);
                }
            }
        }
        return (array) $_PUT;
    }

}

if (!function_exists('delete')) {

    function delete()
    {
        $_DELETE = array();
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            $deletedata = file_get_contents('php://input');
            $exploded = explode('&', $deletedata);
            foreach ($exploded as $pair) {
                $item = explode('=', $pair);
                if (count($item) === 2) {
                    $_DELETE[urldecode($item[0])] = urldecode($item[1]);
                }
            }
        }
        return (array) $_DELETE;
    }

}

if (!function_exists('sanitize_input')) {

    function sanitize_input()
    {
        $_GET = clean_input_data($_GET);
        $_POST = clean_input_data($_POST);
        // Clean $_COOKIE Data
        // Also get rid of specially treated cookies that might be set by a server
        // or silly application, that are of no use to a CI application anyway
        // but that when present will trip our 'Disallowed Key Characters' alarm
        // http://www.ietf.org/rfc/rfc2109.txt
        // note that the key names below are single quoted strings, and are not PHP variables
        unset($_COOKIE['$Version']);
        unset($_COOKIE['$Path']);
        unset($_COOKIE['$Domain']);
        $_COOKIE = clean_input_data($_COOKIE);
    }

}

if (!function_exists('clean_input_data')) {

    function clean_input_data($str)
    {
        if (is_array($str)) {
            $new_array = array();
            foreach ($str as $key => $val) {
                $new_array[clean_input_keys($key)] = clean_input_data($val);
            }
            return $new_array;
        }
        if (get_magic_quotes_gpc()) {
            $str = stripslashes($str);
        }
        // Standardize newlines
        if (strpos($str, "\r") !== false) {
            $str = str_replace(array("\r\n", "\r"), "\n", $str);
        }
        return $str;
    }

}

if (!function_exists('clean_input_keys')) {

    function clean_input_keys($str)
    {
        if (!preg_match("/^[a-z0-9:_\/-]+$/i", $str)) {
            exit('Disallowed Key Characters.');
        }
        return $str;
    }

}

if (!function_exists('str_really_escape')) {

    function str_really_escape($str)
    {
        return str_replace(array('%', '_', '\''), array('&#37;', '&#95;', '&#39;'), $str);
    }

}

if (!function_exists('formatBytes')) {

    function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

}

if (!function_exists('xor_swap')) {

    function xor_swap(&$x, &$y)
    {
        if ($x != $y) {
            $x ^= $y;
            $y ^= $x;
            $x ^= $y;
        }
    }

}

/**
 * Functions to be used for translation (I18N)
 *
 *   __('Welcome back, :user', array(':user' => $username));
 *
 * @see http://php.net/strtr
 */
if (!function_exists('__')) {

    /**
     *
     * @param string $string
     * @param array $values
     * @return string
     */
    function __($string, array $values = null)
    {
        $i18n = new I18n;
        $string = $i18n->translate($string);
        return empty($values) ? $string : strtr($string, $values);
    }

}

if (!function_exists('_')) {

    /**
     *
     * @param string $string
     * @param array $values
     * @param string $lang
     * @return string
     */
    function _($string, array $values = null, $lang = 'en_US')
    {
        return __($string, $values, $lang);
    }

}

if (!function_exists('__h')) {

    function __h($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

}