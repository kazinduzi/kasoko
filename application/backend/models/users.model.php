<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class Users extends Model
{

    public $table = 'users';
    protected $id;

    public static function getInstance($className = NULL, array $options = array())
    {
        static $Instance;
        if ($Instance === null) {
            $Instance = new self;
        }
        return $Instance;
    }

    public function __construct($value = null)
    {

        if (empty($value)) {
            //stop from creating models with id of 0
        } elseif (is_numeric($value)) {
            $this->id = $value;
            $this->values = arrayFirst(Kazinduzi::db()->fetchAssoc("SELECT * FROM `" . $this->table . "` WHERE id = " . $value));

            if (empty($this->values)) {
                trigger_error(get_class($this) . ' with ' . $this->primaryKeyField() . ' ' . $value . ' does not exist', E_USER_NOTICE);
            }
        } elseif (is_array($value)) {
            parent::__construct($value);
        }
    }

    public static function getUserByUsername()
    {
        $params = @func_get_args();
        $where = "`username` = " . static::getInstance()->getDbo()->escape($params[0]);
        $user = self::find('users', array('WHERE' => $where, 'LIMIT' => 1));
        if (!empty($user)) {
            return $user;
        }
        return false;
    }

    //
    //
    public static function getAllUsers()
    {
        $args = func_get_args();
        $args = array_merge(array('users'), array_slice($args, 1));
        return call_user_func_array(array('Database', 'findAll'), $args);
    }

    public function addNew($params)
    {
        print_r($params);
    }

    //
    public function updateUser($params)
    {
        print_r($params);
    }

    //
    protected function deleteUser($params)
    {
        print_r($params);
    }

}
