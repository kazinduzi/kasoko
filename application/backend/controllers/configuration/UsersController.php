<?php

use models\Users;

/**
 * Description of UsersController
 *
 * @author Emmanuel Ndayiragije <endayiragije@gmail.com>
 */
class ConfigurationUsersController extends Admin_controller
{

    /**
     * 
     * @param \Request $req
     * @param \Response $res
     */
    public function __construct(\Request $req = null, \Response $res = null)
    {
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('phtml');
        $this->token = Security::token();
    }

    /**
     * Index action
     */
    public function index()
    {
        $usersModel = new Users;
        $this->Template->setFilename('configuration/users');
        $this->title = __('Backoffice users');
        $this->users = $usersModel->findAll();
    }

    /**
     * Create new user action
     */
    public function create()
    {
        $this->title = __('Create new user');
        if ($this->Request->isPost()) {
            $data = $this->Request->postParam('user');
            $user = new Users();
            if (Users::existsUsername($data['username'])) {
                throw new Exception(sprintf('Username [%s] is already in use. Try another one.', $data['username']));
            }
            $user->username = $data['username'];
            $user->firstname = $data['firstname'];
            $user->lastname = $data['lastname'];
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->status = 0;
            $user->level = 1;
            if (!empty($data['password']) && Security::compareStrings($data['password'], $data['confirm_password'])) {
                $user->password = password_hash($data['password'], PASSWORD_BCRYPT, array('cost' => 10));
            }
            $user->created = time();
            $user->createdby = $this->getUser()->getId();
            $user->save();
            $this->redirect('/admin/configuration/users');
        }
    }

    /**
     * Update user
     */
    public function update()
    {
        $this->title = __('User update');
        if ($this->Request->isPost()) {
            $data = $this->Request->postParam('user');            
            $user = new models\Users($data['id']);            
            $user->firstname = $data['firstname'];
            $user->lastname = $data['lastname'];
            $user->name = $data['name'];
            $user->email = $data['email'];
            if (!empty($data['password']) && Security::compareStrings($data['password'], $data['confirm_password'])) {
                $user->password = password_hash($data['password'], PASSWORD_BCRYPT, array('cost' => 10));
            }
            $user->updated = time();
            $user->updatedby = $this->getUser()->getId();
            $user->save();
            $this->redirect('/admin/configuration/users');
        }
    }
    
    public function delete()
    {
        $this->title = __('Delete user');
        if ($this->Request->isPost()) {
            $userId = $this->Request->postParam('user_id');
            $token = $this->Request->getParam('_token');
            $user = new Users($userId);        
            if (! $user->isSuperUser() && Security::check($token)) {
                $user->delete();                
            }
            $this->redirect('/admin/configuration/users');
        }
    }

}
