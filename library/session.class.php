<?php

defined('KAZINDUZI_PATH') or die('No direct access script allowed');

/**
 * Session provides session-level data management and the related configurations.
 *
 * To start the session, call {@link open()}; To complete and send out session data, call {@link close()};
 * To destroy the session, call {@link destroy()}.
 *
 *
 * Session can be used like an array to set and get session data. For example,
 * <pre>
 *   $session=Session::Instance();
 *   $session->open();
 *   $value1=$session['name1'];  // get session variable 'name1'
 *   $value2=$session['name2'];  // get session variable 'name2'
 *   foreach($session as $name=>$value) // traverse all session variables
 *   $session['name3']=$value3;  // set session variable 'name3'
 * </pre>
 *
 * The following configurations are available for session:
 * <ul>
 * <li>{@link setId sessionID};</li>
 * <li>{@link setSessionName sessionName};</li>
 * <li>{@link setSavePath savePath};</li>
 * <li>{@link setCookieParams cookieParams};</li>
 * <li>{@link setGCProbability gcProbability};</li>
 * <li>{@link setCookieMode cookieMode};</li>
 * <li>{@link setUseTransparentSessionID useTransparentSessionID};</li>
 * <li>{@link setTimeout timeout}.</li>
 * </ul>
 * See the corresponding setter and getter documentation for more information.
 * Note, these properties must be set before the session is started.
 *
 * Session can be extended to support customized session storage.
 * Override {@link openSession}, {@link closeSession}, {@link readSession},
 * {@link writeSession}, {@link destroySession} and {@link gcSession}
 * and set {@link useCustomStorage} to true.
 * Then, the session data will be stored and retrieved using the above methods.
 *
 * Session is a Web application component that can be accessed via
 * {@link Kazinduzi::getSession()}.
 *
 * Description of session
 *
 * @author Emmanuel_Leonie
 */
abstract class Session implements ArrayAccess, IteratorAggregate, Countable
{

    /**
     * @var  array  session instances
     */
    public static $instances = array();

    /**
     * @var type of session to be used (default|database)
     */
    public static $default = 'default';

    /**
     * @var type
     */
    protected static $configs = array();

    /**
     * @var type
     */
    public $ip = false;

    /**
     * @var type
     */
    public $ua = false;

    /**
     *
     * @var type
     */
    private $sessionData = array();

    /**
     *
     * @var type
     */
    private $_encrypted = false;

    /**
     * This abstract may not have been instantiated
     * @throws Exception
     */
    private function __construct()
    {
        
    }

    /**
     * Get the singleton Object for the session
     * @param type $type
     * @return session object
     */
    public static function instance($type = null)
    {
        if (null === $type) {
            $type = Kazinduzi::getConfig('session')->get('type');
        }

        // Get the session type
        if (isset(self::$instances[$type])) {
            return self::$instances[$type];
        } else {
            // Load the configuration for the session
            self::$configs = Kazinduzi::getConfig('session')->as_array();

            // Set the session class name
            $class = 'Session' . ucfirst($type);

            // Create a new session instance
            self::$instances[$type] = new $class(self::$configs);

            if (isset(self::$configs['session_autostart']) && self::$configs['session_autostart']) {
                self::$instances[$type]->start();
            }
            register_shutdown_function(array(self::$instances[$type], 'close'));
        }
        // Return the instance object for the session
        return self::$instances[$type];
    }

    /**
     * Session object is rendered to a serialized string. If encryption is
     * enabled, the session will be encrypted. If not, the output string will
     * be encoded using [base64_encode].
     *
     * echo $session;
     *
     * @return  string
     * @uses    Encrypt::encode
     */
    public function __toString()
    {
        $data = serialize($this->sessionData);
        if ($this->_encrypted) {
            // Encrypt the data using the default key
            $data = Encrypt::instance($this->_encrypted)->encode($data);
        } else {
            // Obfuscate the data with base64 encoding
            $data = base64_encode($data);
        }
        return $data;
    }

    /**
     * This method initiates the application session component.
     * If the the session already started, return true.
     * In any other case, initialize and start the session, if security are tuned, then check it.
     *
     * @return bool
     */
    public function start()
    {
        if ($this->isStarted()) {
            return true;
        }
        $this->init();
        $this->open();

        # check security
        $this->security_check();
        register_shutdown_function(array($this, 'close'));
        $this->sessionData = &$_SESSION;
        return true;
    }

    /**
     * @return boolean whether the session has started
     */
    public function isStarted()
    {
        return '' !== session_id();
    }

    /**
     * initializing the session
     */
    private function init()
    {
        // Need to destroy any existing sessions started with session.auto_start
        if (session_id()) {
            session_unset();
            session_destroy();
        }
        // Set the name of the session which is used as cookie name.
        ini_set('session.name', (string) self::$configs['session_name']);

        // Set session lifetime of the cookie in seconds
        ini_set('session.cookie_lifetime', (int) self::$configs['session_lifetime']);

        // Set Session property to use cookies ONLY
        if (!ini_get('session.use_only_cookies')) {
            ini_set('session.use_only_cookies', '1');
        }
        // Use session cookies, not transparent sessions that puts the session id in the query string.
        ini_set('session.use_trans_sid', '0');

        // Don't send HTTP headers using PHP's session handler.
        ini_set('session.cache_limiter', 'none');

        // Use httponly session cookies.
        ini_set('session.cookie_httponly', '1');

        // Use a strong session hash identifier
        (version_compare(PHP_VERSION, '5.3.0') >= 0) ? ini_set('session.hash_function', self::$configs['hash_function']) : ini_set('session.hash_function', '1');
    }

    /**
     * Starts the session if it has not started yet
     * 
     * @return boolean
     */
    private function open()
    {
        if ($this->isStarted()) {
            return true;
        }

        // Sync up the session cookie with Cookie parameters
        $this->getCookieParams();

        if ($this->getUseCustomStorage()) {
            // use this object as the session handler
            session_set_save_handler(
                    array(&$this, 'openSession'), array(&$this, 'closeSession'), array(&$this, 'readSession'), array(&$this, 'writeSession'), array(&$this, 'destroySession'), array(&$this, 'gcSession')
            );
        }

        /**
         *  Start normally the session
         */
        if (\PHP_SESSION_ACTIVE === session_status()) {
            throw new \RuntimeException('Failed to start the session: already started by PHP.');
        }
        if (ini_get('session.use_cookies') && headers_sent($file, $line)) {
            throw new \RuntimeException(sprintf('Failed to start the session because headers have already been sent by "%s" at line %d.', $file, $line));
        }
        // ok to try and start the session
        if (!session_start()) {
            throw new \RuntimeException('Failed to start the session');
        }

        /**
         * If {session_match_ip} is set in the session's configuration file, and
         * it is not yet set in $_SESSION global, set it.
         */
        if (self::$configs['session_match_ip'] && !isset($_SESSION['ip'])) {
            $_SESSION['ip'] = Request::getInstance()->ip_address();
        }
        /**
         * If {session_match_useragent} is set in the session's configuration file, and
         * it is not yet set in $_SESSION global, set it.
         */
        if (self::$configs['session_match_useragent'] && !isset($_SESSION['ua'])) {
            $_SESSION['ua'] = Request::getInstance()->user_agent();
        }

        return true;
    }

    /**
     * @return array the session cookie parameters.
     * @see http://us2.php.net/manual/en/function.session-get-cookie-params.php
     */
    public function getCookieParams()
    {
        return session_get_cookie_params();
    }

    /**
     * Returns a value indicating whether to use custom session storage.
     * This method should be overriden to return true if custom session storage handler should be used.
     * If returning true, make sure the methods {@link openSession}, {@link closeSession}, {@link readSession},
     * {@link writeSession}, {@link destroySession}, and {@link gcSession} are overridden in child
     * class, because they will be used as the callback handlers.
     * The default implementation always return false.
     * @return boolean whether to use custom storage.
     */
    public function getUseCustomStorage()
    {
        return false;
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function security_check()
    {
        if (self::$configs['session_match_useragent'] && isset($_SESSION['ua']) && ($this->ua != $_SESSION['ua'])) {
            $this->destroy();
            throw new Exception('The UserAgent doesn\'t match.');
        }
        if (self::$configs['session_match_ip'] && isset($_SESSION['ip']) && ($this->ip != $_SESSION['ip'])) {
            $this->destroy();
            throw new Exception('The IP address doesn\'t match.');
        }
    }

    /**
     * Frees all session variables and destroys all data registered to a session.
     * Completely destroy the current session.
     *
     *     $success = $session->destroy();
     *
     * @return  boolean
     */
    public function destroy()
    {
        if ('' !== session_id()) {
            $this->sessionData = array();
            @session_unset();
            @session_destroy();
            if (ini_get('session.use_cookies')) {
                $params = $this->getCookieParams();
                extract($params);
                if ($httponly) {
                    \library\Cookie\Cookie::setcookie($this->name(), '', time() - 42000, $path, $domain, $secure, $httponly, 'Lax');
                } else {
                    \library\Cookie\Cookie::setcookie($this->name(), '', time() - 42000, $path, $domain, $secure, false, 'Lax');
                }
            }
        }
        return true;
    }

    /**
     * Method to set or get the session_name
     * @param (stirng|null) $name
     * @return string
     */
    public function name($name = null)
    {
        return session_name($name);
    }

    /**
     * Restarts the current session.
     * 
     * @return  boolean
     */
    public function restart()
    {
        session_start();
        $this->sessionData = &$_SESSION;
        return true;
    }

    /**
     * Generate a new session id and return it.
     * 
     * @return  string
     */
    public function regenerate($delete_old_session = true)
    {
        session_regenerate_id($delete_old_session);
        return session_id();
    }

    /**
     * Ends the current session and store session data.
     */
    public function close()
    {
        if ('' !== session_id()) {
            session_write_close();
        }
        return true;
    }

    /**
     * retrive the session id
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * @param string $value the session ID for the current session
     */
    public function setId($value)
    {
        session_id($value);
        return $this;
        ;
    }

    /**
     * @return string the current session name
     */
    public function getSessionName()
    {
        return session_name();
    }

    /**
     * @param string $value the session name for the current session, must be an alphanumeric string, defaults to PHPSESSID
     */
    public function setSessionName($value)
    {
        session_name($value);
    }

    /**
     * @return string the current session save path, defaults to '/tmp'.
     */
    public function getSavePath()
    {
        return session_save_path();
    }

    /**
     * @param string $value the current session save path
     * @throws CException if the path is not a valid directory
     */
    public function setSavePath($value)
    {
        if (is_dir($value)) {
            session_save_path($value);
        } else {
            throw new Exception('session.savePath [<strong>' . $value . '</strong>] is not a valid directory.');
        }
    }

    /**
     * Sets the session cookie parameters.
     * The effect of this method only lasts for the duration of the script.
     * Call this method before the session starts.
     * @param array $value cookie parameters, valid keys include: lifetime, path, domain, secure.
     * @see http://us2.php.net/manual/en/function.session-set-cookie-params.php
     */
    public function setCookieParams(array $value)
    {
        $params = session_get_cookie_params();
        extract($params);
        extract($value);
        if (isset($httponly)) {
            session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
        } else {
            session_set_cookie_params($lifetime, $path, $domain, $secure);
        }
    }

    /**
     * @return string how to use cookie to store session ID. Defaults to 'Allow'.
     */
    public function getCookieMode()
    {
        if (ini_get('session.use_cookies') === '0')
            return 'none';
        else if (ini_get('session.use_only_cookies') === '0')
            return 'allow';
        else
            return 'only';
    }

    /**
     * @param string $value how to use cookie to store session ID. Valid values include 'none', 'allow' and 'only'.
     * @throws Exception
     */
    public function setCookieMode($value)
    {
        if ($value === 'none') {
            ini_set('session.use_cookies', '0');
        } else if ($value === 'allow') {
            ini_set('session.use_cookies', '1');
            ini_set('session.use_only_cookies', '0');
        } else if ($value === 'only') {
            ini_set('session.use_cookies', '1');
            ini_set('session.use_only_cookies', '1');
        } else {
            throw new Exception('Session.cookie mode can only be "none", "allow" or "only".');
        }
    }

    /**
     * @return integer the probability (percentage) that the gc (garbage collection) process is started on every session initialization,
     * defaults to 1 meaning 1% chance.
     */
    public function getGCProbability()
    {
        return (int) ini_get('session.gc_probability');
    }

    /**
     * @param integer $value the probability (percentage) that the gc (garbage collection) process is started on every session initialization.
     * @throws Exception
     */
    public function setGCProbability($value)
    {
        $value = (int) $value;
        if ($value >= 0 && $value <= 100) {
            ini_set('session.gc_probability', $value);
            ini_set('session.gc_divisor', '100');
        } else {
            throw new Exception('Session.gcProbability "{value}" is invalid. It must be an integer between 0 and 100.');
        }
    }

    /**
     * @return boolean whether transparent sid support is enabled or not, defaults to false.
     */
    public function getUseTransparentSession()
    {
        return ini_get('session.use_trans_sid') == 1;
    }

    /**
     * @param boolean $value whether transparent sid support is enabled or not.
     */
    public function setUseTransparentSession($value)
    {
        ini_set('session.use_trans_sid', $value ? '1' : '0');
    }

    /**
     * @return integer the number of seconds after which data will be seen as 'garbage' and cleaned up, defaults to 1440 seconds.
     */
    public function getTimeout()
    {
        return (int) ini_get('session.gc_maxlifetime');
    }

    /**
     * @param integer $value the number of seconds after which data will be seen as 'garbage' and cleaned up
     */
    public function setTimeout($value)
    {
        ini_set('session.gc_maxlifetime', $value);
    }

    /**
     * Session open handler.
     * This method should be overridden if {@link useCustomStorage} is set true.
     * Do not call this method directly.
     * @param string $savePath session save path
     * @param string $sessionName session name
     * @return boolean whether session is opened successfully
     */
    public function openSession($savePath, $sessionName)
    {
        return true;
    }

    /**
     * Session close handler.
     * This method should be overridden if {@link useCustomStorage} is set true.
     * Do not call this method directly.
     * @return boolean whether session is closed successfully
     */
    public function closeSession()
    {
        return true;
    }

    /**
     * Session read handler.
     * This method should be overridden if {@link useCustomStorage} is set true.
     * Do not call this method directly.
     * @param string $id session ID
     * @return string the session data
     */
    public function readSession($id)
    {
        return '';
    }

    /**
     * Session write handler.
     * This method should be overridden if {@link useCustomStorage} is set true.
     * Do not call this method directly.
     * @param string $id session ID
     * @param string $data session data
     * @return boolean whether session write is successful
     */
    public function writeSession($id, $data)
    {
        return true;
    }

    /**
     * Session destroy handler.
     * This method should be overridden if {@link useCustomStorage} is set true.
     * Do not call this method directly.
     * @param string $id session ID
     * @return boolean whether session is destroyed successfully
     */
    public function destroySession($id)
    {
        return true;
    }

    /**
     * Session GC (garbage collection) handler.
     * This method should be overridden if {@link useCustomStorage} is set true.
     * Do not call this method directly.
     * @param integer $maxLifetime the number of seconds after which data will be seen as 'garbage' and cleaned up.
     * @return boolean whether session is GCed successfully
     */
    public function gcSession($maxLifetime)
    {
        return true;
    }

    /**
     * Returns an iterator for traversing the session variables.
     * This method is required by the interface IteratorAggregate.
     * @return SessionIterator an iterator for traversing the session variables.
     */
    public function getIterator()
    {
        return new SessionIterator($this->sessionData);
    }

    /**
     * Returns the number of items in the session.
     * This method is required by Countable interface.
     * @return integer number of items in the session.
     */
    public function count()
    {
        return $this->getCount();
    }

    /**
     * Returns the number of items in the session.
     * @return integer the number of session variables
     */
    public function getCount()
    {
        return count($this->sessionData);
    }

    /**
     *
     * @param type $offset
     * @return type
     */
    public function offsetExists($offset)
    {
        return isset($this->sessionData[$offset]);
    }

    /**
     *
     * @param type $offset
     * @param self $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_array($value)) {
            $value = new static($value);
        }
        if ($offset === null) {
            $this->sessionData[] = $value;
        } else {
            $this->sessionData[$offset] = $value;
        }
    }

    /**
     *
     * @param type $offset
     * @return type
     */
    public function offsetGet($offset)
    {
        return $this->sessionData[$offset];
    }

    /**
     *
     * @param type $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->sessionData[$offset]);
    }

    /**
     * @return array the list of session variable names
     */
    public function getKeys()
    {
        return array_keys($this->sessionData);
    }

    /**
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * Alias of add method
     * @param string $key
     * @param mixed $value
     * @return Session
     * @throws Exception
     */
    public function set($key, $value)
    {
        if ($this->id() === '') {
            throw new Exception("The session is not started yet");
        }
        if ($key === '') {
            throw new Exception("The '$key' key must be a non-empty string");
        }
        $this->sessionData[$key] = $value;
        return $this;
    }

    /**
     * @return string the current session ID
     */
    public function id()
    {
        return session_id();
    }

    /**
     * Returns the session variable value with the session variable name.
     * This method is very similar to {@link itemAt} and {@link offsetGet},
     * except that it will return $defaultValue if the session variable does not exist.
     * @param mixed $key the session variable name
     * @param mixed $defaultValue the default value to be returned when the session variable does not exist.
     * @return mixed the session variable value, or $defaultValue if the session variable does not exist.
     * @throws Exception
     * @since 1.1.2
     */
    public function &get($key, $defaultValue = null)
    {
        if ($this->id() === '') {
            throw new Exception("The session is not started yet");
        }
        if ($key === '') {
            throw new Exception("The '$key' key must be a non-empty string");
        }
        if (isset($this->sessionData[$key])) {
            return $this->sessionData[$key];
        }
        return $defaultValue;
    }

    /**
     *
     * @param string $key
     * @return mixed
     */
    public function & __get($key)
    {
        return $this->get($key, null);
    }

    /**
     *
     * @param string $key
     * @return bool
     * @throws Exception
     */
    public function __isset($key)
    {
        if ($this->id() === '') {
            throw new Exception("The session is not started yet");
        }
        if ($key === '') {
            throw new Exception("The '$key' key must be a non-empty string");
        }
        return isset($this->sessionData[$key]) && ('' !== session_id());
    }

    /**
     * __unset() - unset a variable in this object's namespace.
     *
     * @param $key
     * @return true
     * @throws Exception
     * @internal param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     */
    public function __unset($key)
    {
        if ($this->id() === '') {
            throw new Exception("The session is not started yet");
        }
        if ($key === '') {
            throw new Exception("The '$key' key must be a non-empty string");
        }
        unset($this->sessionData[(string) $key]);
    }

    /**
     * Returns the session variable value with the session variable name.
     * This method is exactly the same as {@link offsetGet}.
     * @param mixed $key the session variable name
     * @return mixed the session variable value, null if no such variable exists
     * @throws Exception
     */
    public function itemAt($key)
    {
        if ($this->id() === '') {
            throw new Exception("The session is not started yet");
        }
        if ($key === '') {
            throw new Exception("The '$key' key must be a non-empty string");
        }
        return isset($this->sessionData[$key]) ? $this->sessionData[$key] : null;
    }

    /**
     * Adds a session variable.
     * Note, if the specified name already exists, the old value will be removed first.
     * @param mixed $key session variable name
     * @param mixed $value session variable value
     * @return $this
     * @return $this
     * @throws Exception
     */
    public function add($key, $value)
    {
        if ($this->id() === '') {
            throw new Exception("The session is not started yet");
        }
        if ($key === '') {
            throw new Exception("The '$key' key must be a non-empty string");
        }
        $this->sessionData[$key] = $value;
        return $this;
    }

    /**
     * Removes a session variable.
     * @param mixed $key the name of the session variable to be removed
     * @return mixed the removed value, null if no such session variable.
     * @throws Exception
     */
    public function remove($key)
    {
        if ($this->id() === '') {
            throw new Exception("The session is not started yet");
        }
        if ($key === '') {
            throw new Exception("The '$key' key must be a non-empty string");
        }
        if (isset($this->sessionData[$key])) {
            $value = $this->sessionData[$key];
            unset($this->sessionData[$key]);
            return $value;
        }
        return null;
    }

    /**
     * Removes all session variables
     */
    public function clear()
    {
        if ($this->id() === '') {
            throw new Exception("The session is not started yet");
        }
        foreach (array_keys($this->sessionData) as $key) {
            unset($this->sessionData[$key]);
        }
    }

    /**
     * @param mixed $key session variable name
     * @return bool whether there is the named session variable
     * @throws Exception
     */
    public function contains($key)
    {
        if ($this->id() === '') {
            throw new Exception("The session is not started yet");
        }
        if ($key === '') {
            throw new Exception("The '$key' key must be a non-empty string");
        }
        return isset($this->sessionData[$key]);
    }

    /**
     * Returns the current session array. The returned array can also be
     * assigned by reference.
     * // Get a copy of the current session data
     * $data = $session->toArray();
     * // Assign by reference for modification
     * $data = $session->toArray();
     * @return array the list of all session variables in array
     * @throws Exception
     */
    public function toArray()
    {
        if ($this->id() === '') {
            throw new Exception("The session is not started yet");
        }
        return $this->sessionData;
    }

}
