<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class CustomerAccountController extends BaseController
{

    protected $customer;

    /**
     *
     * @param \Request $req
     * @param \Response $res
     */
    public function __construct(\Request $req = null, \Response $res = null)
    {
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('phtml');
    }

    public function before()
    {
        if (!$this->getCustomer()->isLogged() && 'create' != $this->getAction()) {
            $this->redirect('/customer/login');
        }
    }

    /**
     * Get customer singleton object
     * @return Customer object
     */
    protected function getCustomer()
    {
        if (!$this->customer instanceof \Customer) {
            $this->customer = \Customer::getSingleton();
        }
        return $this->customer;
    }

    /**
     * Index action
     */
    public function index()
    {
        $this->Template->setFilename('account/dashboard');
        $this->title = 'Account';
        $this->Customer = $this->getCustomer();
    }

    /**
     * Edit action
     */
    public function edit()
    {
        $this->Template->setFilename('account/edit');
        $this->title = 'Account Information';
        $this->Customer = $this->getCustomer();
        if (true == $this->Request->getParam('changepass')) {
            $this->changepassword = $this->Request->getParam('changepass');
        }
    }

    /**
     * Create action
     */
    public function create()
    {
        $this->title = 'Create New Customer Account';
        $this->Template->setViewSuffix('phtml');
        $this->Template->setFilename('account/create');
        $this->countries = Country::getAll();
        if ($this->Request->isPost() && ($data = $this->Request->postParams())) {
            $accountModel = new AccountCustomer();
            if ($accountModel->existsEmail($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->redirect('/customer/account/create');
            } else {
                $customer = $accountModel->addCustomer($data);
                $this->sendConfirmationMail($customer);
                $this->getCustomer()->login($data['email'], $data['password']);
                $this->redirect('/customer/account');
            }
        }
    }

    /**
     * Send confirmation email
     *
     * @param AccountCustomer $customer
     * @return boolean
     */
    protected function sendConfirmationMail(AccountCustomer $customer)
    {
        $i18n = new I18n();
        $content = file_get_contents(APP_PATH . '/mails/' . $i18n->getLanguage() . '/account.html');
        $search = array('{firstname}', '{lastname}', '{email}', '{shop_logo}', '{shop_name}', '{shop_url}');
        $replace = array($customer->firstname, $customer->lastname, $customer->email, Configuration::get('shop_logo'), \Configuration::get('shop_name'), \Configuration::get('frontend_baseUrl'));
        $content = \str_replace($search, $replace, $content);

        $mail = new Mailer(true);
        try {
            $mail->AddAddress($customer->email, $customer->lastname);
            $mail->SetFrom('noreply@kazinduzidev.com', 'Kasoko Dev');
            $mail->AddReplyTo('endayiragije@yahoo.fr', 'Emmanuel Ndayiragije');
            $mail->Subject = __('account.welcome');
            $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
            $mail->MsgHTML($content);
            return $mail->Send();
        } catch (phpmailerException $e) {
            echo $e->errorMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Order action
     */
    public function order()
    {
        $this->title = 'Order history';
        $this->Template->setFilename('account/order_history');
        $orderModel = new AccountOrder();
        $this->orders = $orderModel->getByCustomer($this->getCustomer()->getId());
    }

}
