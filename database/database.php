<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class Database extends DbActiveRecord
{

    /**
     * @var type
     */
    public static $db = false;

    /**
     * The DB singleton object to be held statically
     *
     * @var object instance for database class
     */
    protected static $instance, $instances = array();

    /**
     * Inserted record
     *
     * @var integer
     */
    public $inserted = 0;

    /**
     * The DB driver name
     *
     * @var string
     */
    public $name = '';

    /**
     *
     * @var string
     */
    public $sql;

    /**
     * The database link identifier
     *
     * @var mixed
     */
    protected $conn = false;

    /**
     * The null date string
     * @var string
     */
    protected $nullDate = '';

    /**
     * The debug level (0 = off, 1 = on)
     */
    protected $debug = 0;

    /**
     * The number of queries performed by the object instance
     *
     * @var int
     */
    protected $ticker = 0;

    /**
     * A log of queries
     *
     * @var array
     */
    protected $log = array();

    /**
     * The database error number
     *
     * @var int
     */
    protected $errorNum = 0;

    /**
     * The database error message
     *
     * @var string
     */
    protected $errorMsg = '';

    /**
     * The limit for the query
     *
     * @var int
     */
    protected $limit = 0;

    /**
     * The for offset for the limit
     *
     * @var int
     */
    protected $offset = 0;

    /**
     * All queries will be kept here @var type array() of queries as array
     */
    protected $queries = array();

    /**
     * Fields to be quoted @var array Array of fields that are going to be quoted
     */
    protected $quoted = false;

    /**
     * Legacy compatibility
     * @var bool
     */
    protected $hasQuoted = false;

    /**
     * UTF-8 support
     *
     * @var boolean
     */
    protected $utf = false;

    /**
     *  Cfg data to be used for the DB object @var array
     */
    protected $config = array();

    /**
     * The last query cursor
     *
     * @var resource
     */
    protected $cursor = false;

    /**
     * The query result from quering db
     *
     * @var resource
     */
    protected $result = false;

    /**
     * parameter to hold the DB driver object @var Object for the specific driver class
     */
    protected $driver = false;

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->config = Kazinduzi::getConfig('database')->toArray();
        $dbDriverClassName = isset($this->config['driver']) ? 'database_driver_' . $this->config['driver'] : 'database_driver_mysqli';
        $db_driver_path = str_replace('_', '/', $dbDriverClassName);
        require_once $db_driver_path . EXT;
        $dbDriverClassName = ucfirst(substr($dbDriverClassName, 9));
        $this->driver = new $dbDriverClassName($this->config);
        $this->utf = $this->driver->hasUTF();
        if ($this->utf) {
            $this->driver->setUTF();
        }
    }

    /**
     * @param type $table
     * @param type $extra
     * @return \class
     */
    public static function findAll($table, $extra = null)
    {
        $models = array();
        $qry = 'SELECT * FROM ' . $table;
        if (is_numeric($extra)) {
            $extra = array_slice(func_get_args(), 1);
        }
        if (is_array($extra)) {
            $qry .= ' WHERE `id` IN (' . implode(', ', $extra) . ')';
        } elseif (is_string($extra)) {
            $qry .= ' ' . $extra;
        }

        $results = Database::getInstance()->fetchAssoc($qry);
        $class = ucfirst(plural($table));
        foreach ($results as $result) {
            $models[] = new $class($result);
        }
        return $models;
    }

    /**
     * Method to get the instance of the database
     * Create a singleton object of the DB
     *
     * @return Object instance of the DB object
     */
    final public static function getInstance()
    {
        if (!self::$instance instanceof Database) {
            self::$instance = new Database();
            return self::$instance->driver;
        } else {
            return self::$instance->driver;
        }
    }

    /**
     * Sets the debug level on or off
     * @param int 0 = off, 1 = on
     */
    public function debug($level)
    {
        $this->debug = (int) $level;
    }

    /**
     * Method to be overrided from class extending __CLASS__
     * Determines if the connection to the server is active
     *
     * @return    boolean true if active, otherwise false
     */
    public function connected()
    {
        
    }

    /**
     * Adds a field or array of field names to the list that are to be quoted
     * @param    mixed    Field name or array of names
     */
    public function quoted($fields)
    {
        if (is_string($fields)) {
            $this->quoted[] = $fields;
        } else {
            $this->quoted = array_merge($this->quoted, (array) $fields);
        }
        $this->hasQuoted = true;
    }

    /**
     * Checks if field name needs to be quoted
     * @param    string    The field name
     * @return    bool
     */
    public function is_quoted($fieldName)
    {
        if ($this->hasQuoted) {
            return in_array($fieldName, $this->quoted);
        } else {
            return true;
        }
    }

    /**
     * Get the database UTF-8 support
     * @return    boolean
     */
    public function isUTF()
    {
        return $this->utf;
    }

    /**
     * Close the connection when serializing.
     */
    public function __sleep()
    {
        self::__destruct();
        return array_keys(get_object_vars($this));
    }

    /**
     * Destructor method for the current database driver
     */
    public function __destruct()
    {
        $this->conn = false;
        if ($this->driver) {
            unset($this->driver);
        }
    }

    /**
     * @return type
     */
    public function __wakeup()
    {
        $this->connect();
    }

    /**
     * @return type
     */
    public function execute()
    {
        
    }

    /**
     * Get the connection
     * Provides access to the underlying database connection.
     * Useful for when calling a proprietary method such as postgre's lo_* methods
     * @return resource connection
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Get the database null date
     * @return    string    Quoted null/zero date string
     */
    public function getNullDate()
    {
        return $this->nullDate;
    }

    /**
     *
     */
    public function buildQuery()
    {
        $this->setQuery((string) $this);
        return $this;
    }

    /*
     *
      public function query() {}
      public function db_query() {}
      public function execute() {}
      public function fields() {}
     *
     */

    /**
     * @return    object    This object to support chaining.
     */
    public function setQuery($query, $offset = 0, $limit = 0)
    {
        $this->sql = (string) $query;
        $this->limit = (int) $limit;
        $this->offset = (int) $offset;
        return $this;
    }

    /**
     * Get the current query, or new ActiveRecord query object.
     * @param    boolean    False to return the last query set by setQuery, True to return a new JDatabaseQuery object.
     * @return    string    The current value of the internal SQL variable
     */
    public function getQueryAR($new = false)
    {
        //require_once('dbActiveRecord.php');
        //return DbActiveRecord::getSingleton();
        return $this;
    }

    /**
     * @return type
     */
    public function getQueryString()
    {
        if (empty($this->sql)) {
            $this->setQuery((string) $this);
        }
        return $this->sql;
    }

    /**
     * Get a quoted database
     * @param    string    A string
     * @param    boolean    Default true to escape string, false to leave the string unchanged
     * @return    string
     */
    public function quote($text, $escaped = true)
    {
        return $escaped ? $this->escape($text) : $text;
    }

    /**
     * Splits a string of queries into an array of individual queries
     * @param    string    The queries to split
     * @return    array    queries
     */
    public function splitSql($queries)
    {
        $start = 0;
        $open = false;
        $open_char = '';
        $query_split = array();
        $end = strlen($queries);
        for ($i = 0; $i < $end; $i++) {
            $current = substr($queries, $i, 1);
            if ($current == '"' || $current == '\'') {
                $n = 2;
                while (substr($queries, $i - $n + 1, 1) == '\\' && $n < $i) {
                    $n++;
                }
                if ($n % 2 == 0) {
                    if ($open) {
                        if ($current == $open_char) {
                            $open = false;
                            $open_char = '';
                        }
                    } else {
                        $open = true;
                        $open_char = $current;
                    }
                }
            }
            if (($current == ';' && !$open) || $i == $end - 1) {
                $query_split[] = substr($queries, $start, ($i - $start + 1));
                $start = $i + 1;
            }
        }
        return $query_split;
    }

    /**
     * @return type
     */
    public function test()
    {
        if (empty($this->test)) {
            $this->test = __METHOD__;
        }
        return $this->test;
    }

    /**
     * @param type $table
     * @param type $columns
     * @return type
     */
    public function insert($table, $columns)
    {
        $params = array();
        $names = array();
        foreach ($columns as $name => $value) {
            $names[] = $this->quoteColumn($name);
            $params[] = $value;
        }
        $params = array_map(array($this->getDbo(), 'quote'), $params);
        $sql = 'INSERT INTO ' . $this->quoteTable($table) . '(' . implode(', ', $names) . ') VALUES (' . implode(', ', array_values($params)) . ');';
        $this->autocommit(false);
        try {
            $this->setQuery($sql)->execute();
            $this->inserted = $this->insert_id();
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            print_r($e);
        }
        return $this->inserted;
    }

    /**
     * @param type $col
     * @return type
     */
    public function quoteColumn($col)
    {
        if (is_string($col)) {
            $col = trim($col);
            return $col === '*' ? $col : '`' . $col . '`';
        }
        if (is_array($col)) {
            foreach ($col as $c) {
                $quoted[] = quoteColumn($c);
            }
        }
        return implode(',', $quoted);
    }

    /**
     * @param type $col
     * @return type
     */
    public function quoteTable($col)
    {
        if (is_string($col)) {
            return '`' . $col . '`';
        }
        if (is_array($col)) {
            foreach ($col as $c) {
                $quoted[] = quoteColumn($c);
            }
        }
        return implode(',', $quoted);
    }

    /**
     *
     * @param type $table
     * @param type $columns
     * @param type $conditions
     * @param type $params
     * @return type
     */
    public function update($table, $columns, $conditions = '', $params = array())
    {
        static $affected_rows;
        $lines = array();
        foreach ($columns as $name => $value) {
            $lines[] = $this->getDbo()->quoteColumn($name) . '=' . $this->getDbo()->quote($value);
        }
        $sql = 'UPDATE ' . $this->getDbo()->quoteTable($table) . ' SET ' . implode(', ', $lines);
        if (($where = $this->proceedConditions($conditions)) != '') {
            $sql .= ' WHERE ' . $where;
        }
        $this->autocommit(false);
        try {
            $this->setQuery($sql)->execute();
            $affected_rows = $this->affected_rows();
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            print_r($e);
        }
        return $affected_rows;
    }

    /**
     * Retrieve the configuration data for the databse
     * @return array configs
     */
    protected function getCfg($item = null)
    {
        if (empty($this->config)) {
            $this->config = Kazinduzi::getConfig('database')->toArray();
        }
        return isset($item) ? $this->config[$item] : $this->config;
    }

    /**
     * Method to be overrided from class extending __CLASS__     *
     * Is (mysqli|mysql|postgre|...) connector is available
     *
     * @return boolean  True on success, false otherwise.
     */
    protected function enabled()
    {
        
    }

    /**
     * Method to be overrided from class extending __CLASS__
     * Try to reconnect to DB server
     *
     * @return connection resource
     */
    protected function reconnect()
    {
        
    }

    /**
     * Disable Cloning this object class.
     */
    private final function __clone()
    {
        
    }

}
