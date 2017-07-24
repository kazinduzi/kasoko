<?php
/**
 * Kazinduzi Framework (http://framework.kazinduzi.com/)
 *
 * @author    Emmanuel Ndayiragije <endayiragije@gmail.com>
 * @link      http://kazinduzi.com
 * @copyright Copyright (c) 2010-2013 Kazinduzi. (http://www.kazinduzi.com)
 * @license   http://kazinduzi.com/page/license MIT License
 * @package   Kazinduzi
 */
namespace library\Cookie;

/**
 * Simple class to handle the cookies:
 * - read a cookie values
 * - edit an existing cookie and save it
 * - create a new cookie, set values, expiration date, etc. and save it
 *
 */
class Cookie
{

    const SAMESITE_LAX = 'Lax';
    const SAMESITE_STRICT = 'Strict';

    protected $name;
    protected $value;
    protected $expire;
    protected $path;
    protected $domain;
    protected $httpOnly;
    protected $secureOnly;
    private $raw;
    private $sameSite;

    /**
     * Cookie constructor.
     *
     * @param $name     The name of the cookie which is also the key for future accesses via `$_COOKIE[...]`
     * @param null $value
     * @param int $expire
     * @param string $path
     * @param null $domain
     * @param bool|false $secureOnly
     * @param bool|true $httpOnly
     * @param bool|false $raw
     * @param string $sameSite
     */
    public function __construct($name, $value = null, $expire = 0, $path = '/', $domain = null, $secureOnly = false, $httpOnly = true, $raw = false, $sameSite = self::SAMESITE_LAX)
    {
        // from PHP source code
        if (preg_match("/[=,; \t\r\n\013\014]/", $name)) {
            throw new \InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $name));
        }

        if (empty($name)) {
            throw new \InvalidArgumentException('The cookie name cannot be empty.');
        }

        // convert expiration time to a Unix timestamp
        if ($expire instanceof \DateTimeInterface) {
            $expire = $expire->format('U');
        } elseif (!is_numeric($expire)) {
            $expire = strtotime($expire);
			
            if (false === $expire || -1 === $expire) {
                throw new \InvalidArgumentException('The cookie expiration time is not valid.');
            }
        }

        $this->name = $name;
        $this->value = $value;
        $this->domain = $domain;
        $this->expire = $expire;
        $this->path = empty($path) ? '/' : $path;
        $this->secureOnly = (bool)$secureOnly;
        $this->httpOnly = (bool)$httpOnly;
        $this->raw = (bool)$raw;

        if (!in_array($sameSite, array(self::SAMESITE_LAX, self::SAMESITE_STRICT, null), true)) {
            throw new \InvalidArgumentException('The "sameSite" parameter value is not valid.');
        }

        $this->sameSite = $sameSite;
    }

    /**
     * Sets a new cookie in a way compatible to PHP's `setcookie(...)` function
     *
     * @param string $name the name of the cookie which is also the key for future accesses via `$_COOKIE[...]`
     * @param mixed|null $value the value of the cookie that will be stored on the client's machine
     * @param int $expire the Unix timestamp indicating the time that the cookie will expire, i.e. usually `time() + $seconds`
     * @param string|null $path the path on the server that the cookie will be valid for (including all sub-directories), e.g. an empty string for the current directory or `/` for the root directory
     * @param string|null $domain the domain that the cookie will be valid for (including all subdomains)
     * @param bool $secureOnly indicates that the cookie should be sent back by the client over secure HTTPS connections only
     * @param bool $httpOnly indicates that the cookie should be accessible through the HTTP protocol only and not through scripting languages
     * @param string|null $sameSite indicates that the cookie should not be sent along with cross-site requests (either `null`, `Lax` or `Strict`)
     * @return bool whether the cookie header has successfully been sent (and will *probably* cause the client to set the cookie)
     */
    public static function setcookie($name, $value = null, $expire = 0, $path = null, $domain = null, $secureOnly = false, $httpOnly = false, $sameSite = null)
    {
        $cookieHeader = new static($name, $value, $expire, $path, $domain, $secureOnly, $httpOnly, $sameSite);
        return self::addHttpHeader($cookieHeader->__toString());
    }

    /**
     * Parses the given cookie header and returns an equivalent cookie instance
     *
     * @param string $cookieHeader the cookie header to parse
     * @return Cookie|null the cookie instance or `null`
     */
    public static function parse($cookieHeader)
    {
        if (empty($cookieHeader)) {
            return null;
        }

        if (preg_match('/^Set-Cookie: (.*?)=(.*?)(?:; (.*?))?$/i', $cookieHeader, $matches)) {
            if (count($matches) >= 4) {
                $attributes = explode('; ', $matches[3]);

                $cookie = new self($matches[1]);
                $cookie->setPath(null);
                $cookie->setHttpOnly(false);
                $cookie->setValue($matches[2]);

                foreach ($attributes as $attribute) {
                    if (strcasecmp($attribute, 'HttpOnly') === 0) {
                        $cookie->setHttpOnly(true);
                    } elseif (strcasecmp($attribute, 'Secure') === 0) {
                        $cookie->setSecure(true);
                    } elseif (stripos($attribute, 'Expires=') === 0) {
                        $cookie->setExpiryTime((int)strtotime(substr($attribute, 8)));
                    } elseif (stripos($attribute, 'Domain=') === 0) {
                        $cookie->setDomain(substr($attribute, 7), true);
                    } elseif (stripos($attribute, 'Path=') === 0) {
                        $cookie->setPath(substr($attribute, 5));
                    }
                }

                return $cookie;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }    

    /**
     * Sets the value for the cookie
     *
     * @param mixed $value the value of the cookie that will be stored on the client's machine
     * @return static this instance for chaining
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
	/**
     * Sets the path for the cookie
     *
     * @param string $path the path on the server that the cookie will be valid for (including all sub-directories), e.g. an empty string for the current directory or `/` for the root directory
     * @return static this instance for chaining
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Sets whether the cookie should be accessible through HTTP only
     *
     * @param bool $httpOnly indicates that the cookie should be accessible through the HTTP protocol only and not through scripting languages
     * @return static this instance for chaining
     */
    public function setHttpOnly($httpOnly)
    {
        $this->httpOnly = $httpOnly;

        return $this;
    }

    /**
     * Sets whether the cookie should be sent over HTTPS only
     *
     * @param bool $secureOnly indicates that the cookie should be sent back by the client over secure HTTPS connections only
     * @return static this instance for chaining
     */
    public function setSecure($secureOnly)
    {
        $this->secureOnly = $secureOnly;

        return $this;
    }

    /**
     * Sets the expiry time for the cookie
     *
     * @param int $expire the Unix timestamp indicating the time that the cookie will expire, i.e. usually `time() + $seconds`
     * @return static this instance for chaining
     */
    public function setExpiresTime($expire)
    {
        $this->expire = $expire;

        return $this;
    }

    /**
     * Sets the domain for the cookie
     *
     * @param string $domain the domain that the cookie will be valid for (including all subdomains)
     * @param bool $keepWww whether a leading `www` subdomain must be preserved or not
     * @return static this instance for chaining
     */
    public function setDomain($domain, $keepWww = false)
    {
        $this->domain = $this->normalizeDomain($domain, $keepWww);

        return $this;
    }

    /**
     * Sets the expiry time for the cookie based on the specified maximum age
     *
     * @param int $maxAge the maximum age for the cookie in seconds
     * @return static this instance for chaining
     */
    public function setMaxAge($maxAge)
    {
        $this->expire = time() + $maxAge;

        return $this;
    }

    /**
     * Sets the same-site for the cookie
     *
     * @param string|null $sameSite indicates that the cookie should not be sent along with cross-site requests (either `null`, `Lax` or `Strict`)
     * @return static this instance for chaining
     */
    public function setSameSite($sameSite)
    {
        $this->sameSite = $sameSite;

        return $this;
    }

    /**
     * Gets the name of the cookie.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the value of the cookie.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Gets the domain that the cookie is available to.
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Gets the time the cookie expires.
     *
     * @return int
     */
    public function getExpiresTime()
    {
        return $this->expire;
    }

    /**
     * Gets the path on the server in which the cookie will be available on.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Checks whether the cookie should only be transmitted over a secure HTTPS connection from the client.
     *
     * @return bool
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * Checks whether the cookie will be made accessible only through the HTTP protocol.
     *
     * @return bool
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * Whether this cookie is about to be cleared.
     *
     * @return bool
     */
    public function isCleared()
    {
        return $this->expire < time();
    }

    /**
     * Checks if the cookie value should be sent with no url encoding.
     *
     * @return bool
     */
    public function isRaw()
    {
        return $this->raw;
    }

    /**
     * Gets the SameSite attribute.
     *
     * @return string|null
     */
    public function getSameSite()
    {
        return $this->sameSite;
    }

    /**
     * @param $header
     * @return bool
     */
    private function addHttpHeader($header)
    {
        if (!headers_sent()) {
            if (!empty($header)) {
                header($header, false);
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->buildCookieHeaderString();
	}

    /**
     * Saves the cookie
     *
     * @return bool whether the cookie header has successfully been sent (and will *probably* cause the client to set the cookie)
     */
    public function save()
    {
        return $this->addHttpHeader($this->__toString());
    }

    /**
     * Deletes the cookie
     *
     * @return bool whether the cookie header has successfully been sent (and will *probably* cause the client to delete the cookie)
     */
    public function delete()
    {
        // create a temporary copy of this cookie so that it isn't corrupted
        $copiedCookie = clone $this;

        // set the copied cookie's value to an empty string which internally sets the required options for a deletion
        $copiedCookie->setValue('');

        // save the copied "deletion" cookie
        return $copiedCookie->save();
    }

    /**
     * Builds the HTTP header that can be used to set a cookie with the specified options
     *     
     * @return string the HTTP header
     */
    private function buildCookieHeaderString()
    {
        $forceShowExpiry = false;

        if (is_null($this->value) || $this->value === false || $this->value === '') {
            $this->value = 'deleted';
            $this->expire = 0;
            $forceShowExpiry = true;
        }

        $maxAgeStr = $this->formatMaxAge($this->expire, $forceShowExpiry);
        $expireStr = $this->formatExpiresTime($this->expire, $forceShowExpiry);

        $str = 'Set-Cookie: ' . $this->name . '=' . urlencode($this->value);

        if (!is_null($expireStr)) {
            $str .= '; expires=' . $expireStr;
        }

        if (!is_null($maxAgeStr)) {
            $str .= '; Max-Age=' . $maxAgeStr;
        }

        if (!empty($this->path) || $this->path === 0) {
            $str .= '; path=' . $this->path;
        }

        if (!empty($this->domain) || $this->domain === 0) {
            $str .= '; domain=' . $this->domain;
        }

        if ($this->secureOnly) {
            $str .= '; secure';
        }

        if ($this->httpOnly) {
            $str .= '; httponly';
        }

        if ($this->sameSite === self::SAMESITE_LAX) {
            $str .= '; SameSite=Lax';
        } elseif ($this->sameSite === self::SAMESITE_STRICT) {
            $str .= '; SameSite=Strict';
        }

        return $str;

    }

    /**
     * @param $domain
     * @param bool|false $keepWww
     * @return null|string
     */
    private function normalizeDomain($domain, $keepWww = false)
    {
        // make sure the domain is actually a string
        $domain = (string)$domain;

        // if the cookie should be valid for the current host only
        if ($domain === '') {
            // no need for further normalization
            return null;
        }

        // if the provided domain is actually an IP address
        if (filter_var($domain, FILTER_VALIDATE_IP) !== false) {
            // let the cookie be valid for the current host
            return null;
        }

        // for local hostnames (which either have no dot at all or a leading dot only)
        if (strpos($domain, '.') === false || strrpos($domain, '.') === 0) {
            // let the cookie be valid for the current host while ensuring maximum compatibility
            return null;
        }

        // unless the domain already starts with a dot
        if ($domain[0] !== '.') {
            // prepend a dot for maximum compatibility (e.g. with RFC 2109)
            $domain = '.' . $domain;
        }

        // if a leading `www` sub-domain may be dropped
        if (!$keepWww) {
            // if the domain name actually starts with a `www` sub-domain
            if (substr($domain, 0, 5) === '.www.') {
                // strip that sub-domain
                $domain = substr($domain, 4);
            }
        }

        // return the normalized domain
        return $domain;
    }

    /**
     * @param $expire
     * @param bool|false $forceShow
     * @return null|string
     */
    private function formatMaxAge($expire, $forceShow = false)
    {
        $expire = is_int($expire) ?: (int)$expire;
        if ($expire > 0 || $forceShow) {
            return (string)$this->calculateMaxAge($expire);
        }
    }

    /**
     * @param integer $expire
     * @return int
     */
    private function calculateMaxAge($expire)
    {
        $expire = is_int($expire) ?: (int)$expire;
        if ($expire === 0) {
            return 0;
        }

        return (int)$expire - time();
    }

    /**
     * @param integer $expire
     * @param bool|false $forceShow
     * @return null|string
     */
    private function formatExpiresTime($expire, $forceShow = false)
    {
        $expire = is_int($expire) ?: (int)$expire;
        if ($expire > 0 || $forceShow) {
            if ($forceShow) {
                $expire = 1;
            }

            return gmdate('D, d-M-Y H:i:s T', $expire);
        }
        return;
    }

}