<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class Request
{

    private static $instance;
    public $mime_types = array(
        'text/html' => 'html',
        'application/xhtml+xml' => 'html',
        'application/xml' => 'xml',
        'text/xml' => 'xml',
        'text/javascript' => 'js',
        'application/javascript' => 'js',
        'application/x-javascript' => 'js',
        'application/json' => 'json',
        'text/x-json' => 'json',
        'application/rss+xml' => 'rss',
        'application/atom+xml' => 'atom',
        '*/*' => 'html',
        'default' => 'html',
    );
    private $getVars = array();
    private $postVars = array();
    private $serverVars = array();
    private $ipAddress = false;
    private $userAgent = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->configs = Kazinduzi::getConfig()->toArray();
    }

    /**
     * Get Singleton instance
     * @return singleton Request Object
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            return self::$instance = new self();
        } else {
            return self::$instance;
        }
    }

    /**
     * gets the GET vars into an array
     * @return array
     */
    public function getParams()
    {
        return $this->getVars = &$_GET;
    }

    /**
     * get $_GET[$key]
     * @param string $key
     * @return mixed|null
     */
    public function getParam($key)
    {
        if (array_key_exists($key, $_GET)) {
            return $_GET[$key];
        }
        return null;
    }

    /**
     * gets the POST vars into an array
     * @return array
     */
    public function postParams()
    {
        return $this->postVars = &$_POST;
    }

    /**
     * get post value $_POST[$key]
     * @param string $key
     * @return mixed | null
     */
    public function postParam($key)
    {
        if (array_key_exists($key, $_POST)) {
            return $_POST[$key];
        }
        return null;
    }

    /**
     * Get the superglobal variable $_SERVER
     * @return mixed | null
     */
    public function serverParams()
    {
        return $this->serverVars = &$_SERVER;
    }

    /**
     * get server value $_SERVER[$key]
     * @param string $key
     * @return mixed | null
     */
    public function serverParam($key)
    {
        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }
        return null;
    }

    /**
     * Is this a GET request?  Equivalent to $Request->getMethod() == 'get'
     */
    public function isGet()
    {
        return $this->getMethod() == 'get';
    }

    /**
     * Returns the HTTP request method as a lowercase symbol ('get, for example)
     */
    public function getMethod()
    {
        return strtolower(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get');
    }

    /**
     * Is this a POST request?  Equivalent to $Request->getMethod() == 'post'
     */
    public function isPost()
    {
        return $this->getMethod() == 'post';
    }

    /**
     * Is this a PUT request?  Equivalent to $Request->getMethod() == 'put'
     */
    public function isPut()
    {
        return isset($_SERVER['REQUEST_METHOD']) ? $this->getMethod() == 'put' : false;
    }

    /**
     * Is this a DELETE request?  Equivalent to $Request->getMethod() == 'delete'
     */
    public function isDelete()
    {
        return $this->getMethod() == 'delete';
    }

    /**
     * Is this a HEAD request?  Equivalent to $Request->getMethod() == 'head'
     */
    public function isHead()
    {
        return $this->getMethod() == 'head';
    }

    /**
     * User Agent
     *
     * @return    string
     */
    public function user_agent()
    {
        if ($this->userAgent !== false) {
            return $this->userAgent;
        }
        return $this->userAgent = (!isset($_SERVER['HTTP_USER_AGENT'])) ? false : substr($_SERVER['HTTP_USER_AGENT'], 0, 128);
    }

    /**
     * Determine originating IP address.  REMOTE_ADDR is the standard
     * but will fail if ( the user is behind a proxy.  HTTP_CLIENT_IP and/or
     * HTTP_X_FORWARDED_FOR are set by proxies so check for these before
     * falling back to REMOTE_ADDR.  HTTP_X_FORWARDED_FOR may be a comma-
     * delimited list in the case of multiple chained proxies; the first is
     * the originating IP.
     */
    public function ip_address()
    {
        if ($this->ipAddress !== false) {
            return $this->ipAddress;
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $this->isAjax()) {
            $this->ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $this->ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ($_SERVER['REMOTE_ADDR']) {
            $this->ipAddress = $_SERVER['REMOTE_ADDR'];
        } elseif ($_SERVER['HTTP_CLIENT_IP']) {
            $this->ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ($_SERVER['HTTP_X_FORWARDED_FOR']) {
            $this->ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if ($this->ipAddress === false) {
            $this->ipAddress = '0.0.0.0';
            return $this->ipAddress;
        }
        if (strpos($this->ipAddress, ',') !== false) {
            $x = explode(',', $this->ipAddress);
            $this->ipAddress = trim(end($x));
        }
        if (!$this->valid_ip($this->ipAddress)) {
            $this->ipAddress = '0.0.0.0';
        }
        return $this->ipAddress;
    }

    /**
     *
     * @return type
     */
    public function isAjax()
    {
        return $this->isXmlHttpRequest();
    }

    /**
     * Returns true if the request's 'X-Requested-With' header contains
     * 'XMLHttpRequest'. (The Prototype Javascript library sends this header with
     * every Ajax request.)
     */
    public function isXmlHttpRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strstr(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']), 'xmlhttprequest');
    }

    /**
     * Validate IP Address
     *
     * Updated version suggested by Geert De Deckere
     *
     * @param    string
     * @return    string
     */
    public function valid_ip($ip)
    {
        $ip_segments = explode('.', $ip);
        // Always 4 segments needed
        if (count($ip_segments) != 4) {
            return false;
        }
        // IP can not start with 0
        if ($ip_segments[0][0] == '0') {
            return false;
        }
        // Check each segment
        foreach ($ip_segments as $segment) {
            // IP segments must be digits and can not be
            // longer than 3 digits or greater then 255
            if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3) {
                return false;
            }
        }
        return true;
    }

    /**
     * Determine originating IP address.  REMOTE_ADDR is the standard
     * but will fail if ( the user is behind a proxy.  HTTP_CLIENT_IP and/or
     * HTTP_X_FORWARDED_FOR are set by proxies so check for these before
     * falling back to REMOTE_ADDR.  HTTP_X_FORWARDED_FOR may be a comma-
     * delimited list in the case of multiple chained proxies; the first is
     * the originating IP.
     */
    public function getRemoteIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            foreach ((strstr($_SERVER['HTTP_X_FORWARDED_FOR'], ',') ? split(',', $_SERVER['HTTP_X_FORWARDED_FOR']) : array($_SERVER['HTTP_X_FORWARDED_FOR'])) as $remote_ip) {
                if ($remote_ip == 'unknown' ||
                        preg_match('/^((25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.) {3}(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])$/', $remote_ip) ||
                        preg_match('/^([0-9a-fA-F]{4}|0)(\:([0-9a-fA-F]{4}|0)) {7}$/', $remote_ip)
                ) {
                    return $remote_ip;
                }
            }
        }
        return empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Returns the request URI correctly
     */
    public function getRequestUri()
    {
        return $this->getProtocol() . $this->getHostWithPort();
    }

    /**
     * Return 'https://' if ( this is an SSL request and 'http://' otherwise.
     */
    public function getProtocol()
    {
        return $this->isSsl() ? 'https://' : 'http://';
    }

    /**
     * Is this an SSL request?
     */
    public function isSsl()
    {
        return isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === true || $_SERVER['HTTPS'] == 'on');
    }

    /**
     * Returns a host:port string for this request, such as example.com or
     * example.com:8080.
     */
    public function getHostWithPort()
    {
        return $this->getHost() . $this->getPortString();
    }

    /**
     *
     * @return type
     */
    public function getHost()
    {
        return $this->host ? $_SERVER['SERVER_NAME'] : 'localhost';
    }

    /**
     * Returns a port suffix like ':8080' if ( the port number of this request
     * is not the default HTTP port 80 or HTTPS port 443.
     */
    public function getPortString()
    {
        $port = $this->getPort();
        return $port == $this->getStandardPort() ? '' : ($port ? ':' . $this->getPort() : '');
    }

    /**
     * Returns the standard port number for this request's protocol
     */
    public function getStandardPort()
    {
        return $this->isSsl() ? 443 : 80;
    }

    /**
     * Returns the interpreted path to requested resource
     */
    public function getPath()
    {
        return strstr($_SERVER['REQUEST_URI'], '?') ? substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')) : $_SERVER['REQUEST_URI'];
    }

    /**
     *
     * @return type
     */
    public function &getSession()
    {
        return (array) $_SESSION;
    }

    /**
     *
     * @return boolean
     */
    public function resetSession()
    {
        $_SESSION = array();
        return true;
    }

    /**
     *
     * @return type
     */
    public function &getCookies()
    {
        return (array) $_COOKIE;
    }

    /**
     *
     * @return type
     */
    public function &getEnv()
    {
        return (array) $_SERVER;
    }

    /**
     *
     * @return boolean
     */
    public function getServerSoftware()
    {
        if (!empty($_SERVER['SERVER_SOFTWARE'])) {
            if (preg_match('/^([a-zA-Z]+)/', $_SERVER['SERVER_SOFTWARE'], $match)) {
                return strtolower($match[0]);
            }
        }
        return false;
    }

    /**
     *
     * @return type
     */
    public function xhr()
    {
        return $this->isXmlHttpRequest();
    }

    /**
     * Receive the raw post data.
     * This is useful for services such as REST, XMLRPC and SOAP
     * which communicate over HTTP POST but don't use the traditional parameter format.
     */
    public function getRawPost()
    {
        return empty($_ENV['RAW_POST_DATA']) ? '' : $_ENV['RAW_POST_DATA'];
    }

}
