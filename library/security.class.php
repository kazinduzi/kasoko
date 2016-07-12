<?php

defined('KAZINDUZI_PATH') || exit('No direct script access allowed');

/**
 * Kazinduzi Framework (http://framework.kazinduzi.com/)
 *
 * @author    Emmanuel Ndayiragije <endayiragije@gmail.com>
 * @link      http://kazinduzi.com
 * @copyright Copyright (c) 2010-2013 Kazinduzi. (http://www.kazinduzi.com)
 * @license   http://kazinduzi.com/page/license MIT License
 * @package   Kazinduzi
 */
class Security
{

    /**
     * @var  string  keyname used for token storage
     */
    public static $token_name = 'security_token';

    /**
     * @var integer expiration time for the security token
     */
    public static $token_expiration = 7200;

    /**
     * Check if the given token matches the currently stored security token.
     * @param   string   token to check
     * @return  boolean
     * @uses    Security::token
     */
    public static function check($token)
    {
        return self::compareStrings(self::token(), $token);
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
        $expected = (string)$expected;
        $actual = (string)$actual;
        $lenExpected = strlen($expected);
        $lenActual = strlen($actual);
        $len = min($lenExpected, $lenActual);
        $result = 0;
        for ($i = 0; $i < $len; $i++) {
            $result |= ord($expected[$i]) ^ ord($actual[$i]);
        }
        $result |= $lenExpected ^ $lenActual;
        return ($result === 0);
    }

    /**
     * Generate and store a unique token which can be used to help prevent
     * $token = Security::token();
     * You can insert this token into your forms as a hidden field.
     * This provides a basic, but effective, method of preventing CSRF attacks.     *
     * @param   boolean  force a new token to be generated?
     * @return  string
     * @uses    Session::instance
     */
    public static function token($new = false)
    {
        $session = Session::instance();
        $token = $session->get(self::$token_name);
        $isExpired = (time() - (int)$session->get('token_expiration_time')) > 0 ? true : false;
        if ($new === true || !$token || $isExpired) {
            $token = self::generateToken();
            $session->set(self::$token_name, $token);
            $session->set('token_expiration_time', time() + self::$token_expiration);
        }
        return $token;
    }

    /**
     * Generate the secure random string
     *
     * @return string
     */
    protected static function generateToken()
    {
        if (version_compare(PHP_VERSION, '5.3.4', '>=') && function_exists('openssl_random_pseudo_bytes')) {
            return base64_encode(openssl_random_pseudo_bytes(32));
        } else {
            return sha1(uniqid(mt_rand(), true));
        }
    }

    /**
     * Remove image tags from a string.
     * @param   string  string to sanitize
     * @return  string
     */
    public static function removeImageTags($str)
    {
        return preg_replace('/<img[^>]+\>/is', '$1', $str);
    }

    /**
     * Encodes PHP tags in a string.
     * @param   string  string to sanitize
     * @return  string
     */
    public static function encodePHPTags($str)
    {
        return str_replace(array('<?', '?>'), array('&lt;?', '?&gt;'), $str);
    }

    /**
     * BCrypt hashing pas
     * @param string $password
     * @return string
     */
    public static function bcryptPassword($password)
    {
        $Crypt = new Bcrypt();
        $Crypt->setSalt('MyKazinduziIsGreat');
        return $Crypt->hash($password);
    }

}
