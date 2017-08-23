<?php

namespace models;

use Kazinduzi;

/**
 * Description of Users
 *
 * @author Emmanuel Ndayiragije <endayiragije@gmail.com>
 */
class Users extends \Model
{
    public $table = 'users';
    
    /**
     * Check user is superUser, ie. user has level greater, or equal to 9
     * 
     * @return bool
     */
    public function isSuperUser()
    {
        return $this->level >= 9;
    }

    /**
     * Check is username is available
     * 
     * @param string $username
     * @return bool
     */
    public static function existsUsername($username)
    {
        Kazinduzi::db()->select('id')
            ->from('users')
            ->where("username = '{$username}'")
            ->buildQuery()
            ->execute();
    
        return Kazinduzi::db()->num_rows > 0;
    }
    
}
