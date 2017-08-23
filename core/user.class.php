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
use framework\library\Acls\Role as UserRole;
use framework\library\Acls\Permission;

class User extends \Model
{

    const USER_TABLE_NAME = 'users';

    /**
     * @var array
     */
    protected $roles = array();

    /**
     * @var string
     */
    protected $table = self::USER_TABLE_NAME;

    /**
     * @var
     */
    protected $dbo;

    /**
     * Check if the username is availabe or not
     * @param string $username
     * @return bool
     */
    public static function usernameAvailable($username)
    {
        $db = Kazinduzi::db()->clear();
        $escaped = $db->real_escape_string((string) $username);
        $db->select('`id`')
                ->from('`users`')
                ->where('`username` = \'' . $escaped . '\'')
                ->buildQuery()
                ->execute();
        return $db->fetchAssoc() ? false : true;
    }

    /**
     * Get all user
     * @return array
     */
    public static function getAll()
    {
        return static::getInstance()->findAll();
    }

    /**
     * Set the database object
     *
     * @param \Database|Database $dbo
     * @return User
     */
    public function setDbo(\Database $dbo)
    {
        if (!$dbo instanceof Database) {
            $this->dbo = Kazinduzi::db()->clear();
        } else {
            $this->dbo = $dbo;
        }
        return $this;
    }

    /**
     *
     * @return type
     * @throws Exception
     */
    public function getDbo()
    {
        if (!$this->dbo instanceof Database) {
            return $this->dbo = Kazinduzi::db()->clear();
        }
        return $this->dbo;
    }

    /**
     * Get user'roles
     * 
     * @return array
     */
    public function getRoles()
    {
        if (empty($this->roles)) {
            $this->loadUserRoles();
        }
        return $this->roles;
    }

    /**
     *
     * @param UserRole $role
     * @return boolean
     */
    public function hasRole(UserRole $role)
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * Add the user_role
     * 
     * @param UserRole $role
     * @return \User
     */
    public function addRole(UserRole $role)
    {
        if (!$this->hasRole($role)) {
            $data = array(
                'user_id' => $this->getId(),
                'role_id' => $role->getId()
            );
            $this->getDbo()->insert('users_roles', $data);
        }
        return $this;
    }

    /**
     * Remove user's role
     * @param UserRole $role
     * @return \User
     */
    public function removeRole(UserRole $role)
    {
        $this->getDbo()->delete('users_roles', 'role_id=' . $role->getId() . ' AND user_id=' . $this->getId());
        return $this;
    }

    /**
     * Remove all user's roles
     * @return \User
     */
    public function removeUserRoles()
    {
        $this->getDbo()->delete('users_roles', 'user_id=' . $this->getId());
        return $this;
    }

    /**
     * check if user has a specific privilege
     * 
     * @param string|Permission $permission
     * @return boolean
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::getByName($permission);
        } elseif (is_int($permission)) {
            $permission = new Permission($permission);
        }
        if ($permission instanceof Permission) {
            foreach ($this->getRoles() as $role) {
                if ($role->hasPermission($permission)) {
                    return true;
                }
            }
        }
        return $this->isAdmin();
    }

    /**
     *
     * @param integer $id
     * @return static
     * @throws \Exception
     */
    public function getUserByUserid($id)
    {
        if (!isset($id) || !is_numeric($id)) {
            throw new \InvalidArgumentException('Invalid userid provided');
        }
        $this->getDbo()->select('*')
                ->from('`users`')
                ->where('`id` = ' . (int) $id . '')
                ->buildQuery()
                ->execute();
        if (null !== $row = $this->getDbo()->fetchAssocRow()) {
            return new static($row);
        }
        throw new \RuntimeException("Unable to find user with username #{$id}");
    }

    /**
     *
     * @param $username
     * @return void|static
     * @internal param type $id
     */
    public function getUserByUsername($username)
    {
        if (empty($username) || !is_string($username)) {
            throw new \InvalidArgumentException('Invalid username provided');
        }
        $this->getDbo()->select('*')
                ->from('`users`')
                ->where("`username` = '" . $this->getDbo()->real_escape_string($username) . "'")
                ->buildQuery()
                ->execute();
        if (null !== $row = $this->getDbo()->fetchAssocRow()) {
            return new static($row);
        }
        return;
    }

    /**
     * Load the user_roles
     */
    protected function loadUserRoles()
    {
        if (!$this->getId()) {
            return;
        }
        $this->getDbo()->clear()->select('r.*')
                ->from('`roles` as r')
                ->join('`users_roles` as ur', 'ur.role_id = r.role_id')
                ->where('ur.user_id=' . $this->getId())
                ->buildQuery()
                ->execute();
        $rows = $this->getDbo()->fetchAssocList();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $this->roles[] = new UserRole($row);
            }
        }
    }

    /**
     * Set adminstrator
     * 
     * @param boolean $admin
     * @return $this
     */
    public function setAdmin($admin = false)
    {
        $this->level = ($admin == true) ? 9 : 1;
        return $this;
    }

    /**
     * Check if the user is administrator
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return ($this->level == 9) ? true : false;
    }

    /**
     * 
     * @return type
     */
    public function isSuperAdmin()
    {
        return $this->getId() == 1;
    }

    /**
     *
     * @param bool|boolen $active
     * @return User
     */
    public function setActive($active = false)
    {
        $this->status = (boolean) $active;
        return $this;
    }

    /**
     * Is the user active
     * 
     * @return boolean
     */
    public function isActive()
    {
        return $this->status == true;
    }

    /**
     * Check if the user is internal to the system;
     * Thus it cannot be deleted
     * 
     * @return boolean
     */
    public function isInternal()
    {
        return ($this->internal == true);
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getFullname();
    }

    /**
     * Get the fullname
     * 
     * @return string
     */
    public function getFullname()
    {
        $fullName = '';
        if (isset($this->lastname)) {
            $fullName .= $this->lastname;
        }
        if (isset($this->middlename)) {
            $fullName .= $this->getmiddlename;
        }
        $fullName .= ($fullName) ? ', ' : NULL;
        if (isset($this->firstname)) {
            $fullName .= $this->firstname;
        }
        return $fullName;
    }

    /**
     * Set the username
     * @param string $username
     * @return \User
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function setUsername($username)
    {
        if (is_null($username) || !is_string($username)) {
            throw new \InvalidArgumentException("Username must be a string");
        }
        if (!self::usernameAvailable($username)) {
            throw new \Exception("Actually usernames are unique, this username #{$username} is not available.");
        }
        $this->username = $username;
        return $this;
    }

    /**
     * Get username
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     *
     * @param type $password
     * @param type $confirm_password
     * @return \User
     * @throws Exception
     */
    public function setPassword($password, $confirm_password = null)
    {
        if (empty($password) || $password != $confirm_password) {
            throw new \Exception('Invalid password and/or password verification fails.');
        }
        $this->password = password_hash($password, PASSWORD_BCRYPT, array('cost' => PASSWORD_BCRYPT_DEFAULT_COST));
        return $this;
    }

    /**
     *
     * @return type
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Save the user
     * @return User
     * @throws Exception
     * @internal param bool $reload
     */
    public function save()
    {
        try {
            return parent::save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete a user
     * 
     * @return boolean
     * @throws Exception
     */
    public function delete()
    {
        if ($this->internal) {
            throw new \Exception('An internal user may not be deleted.');
        }
        return parent::delete();
    }

}
