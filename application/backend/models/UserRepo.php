<?php

namespace models;

use User;

class UserRepo extends User
{

    /**
     * @var
     */
    protected $users;

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->findAll();
    }

    /**
     * @param User $user
     */
    public function addUser(User $user)
    {
        print_r($params);
    }

    /**
     * @param User $user
     * @param array $data
     */
    public function updateUser(User $user, array $data)
    {
        print_r($params);
    }

    /**
     * @param User $user
     */
    protected function deleteUser(User $user)
    {
        print_r($params);
    }

}
