<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

use \Customer,
    \Request,
    \Response,
    \Session,
    \Cart;

class CheckoutCheckoutController extends BaseController
{

    const STEP_LOGIN = 1;
    const STEP_BILLING = 2;
    const STEP_SHIPPING = 3;
    const STEP_SHIPPING_METHOD = 4;
    const STEP_PAYMENT = 5;
    const STEP_CONFIRM = 6;

    protected $session;
    protected $progressTemplate;
    protected $customerInstance;

    /**
     * Contructor for the CheckoutController
     *
     * @param Request $req
     * @param Response $res
     */
    public function __construct(Request $req = null, Response $res = null)
    {
        if (!Cart::getSingleton()->hasProducts()) {
            redirect('/cart');
        }
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('phtml');
        $this->progressTemplate = new Template('checkout/misc/checkout_progress', 'phtml');
    }

    /**
     * Index method overloaded from the abstract class Controller for checkout index action
     */
    public function index()
    {
        if ($this->getCustomer()->isLogged()) {
            $this->redirect('/checkout/checkout/shipping_information');
        }
        if (!Cart::getSingleton()->hasProducts()) {
            $this->redirect('/cart');
        }
        $this->progressTemplate->set('step', 'checkout');
        $this->progressHtml = $this->progressTemplate->render();
        $this->title = 'Checkout Options';
    }

    /**
     * Get customer
     *
     * @return Customer
     */
    protected function getCustomer()
    {
        if (!$this->customerInstance instanceof Customer) {
            $this->customerInstance = Customer::getSingleton();
        }
        return $this->customerInstance;
    }

    /**
     *
     */
    public function login()
    {
        if ($this->getCustomer()->isLogged()) {
            $this->redirect('/checkout/checkout/shipping_information');
        }
        $this->customer = $this->getCustomer();
        if ($this->Request->isPost()) {
            try {
                if (false !== $logged = $this->getCustomer()->login($this->Request->postParam('email'), $this->Request->postParam('password'))) {
                    $json['customer_id'] = $this->getCustomer()->getId();
                    $json['redirect'] = '/checkout/checkout/shipping_information';
                } else {
                    $json['error'] = 'Failed to login Customer';
                }
                $this->getSession()->add('current_step', self::STEP_BILLING);
                $json['output'] = $this->Template->render();
                header('Content-type: application/json');
                echo json_encode($json);
                die;
            } catch (Exception $e) {
                print_r($e);
            }
        }
    }

    /**
     *
     */
    public function register()
    {
        $json = array();
        if (($customerId = $this->getSession()->get('customer_id'))) {
            $customer = new Customer($customerId);
        } else {
            $customer = new Customer();
        }
        $this->Cart = new Cart;
        $this->customer = $customer;
        $this->agree = true;
        $this->shipping_required = Cart::getSingleton()->hasShipping();
        $this->countries = Country::getAll();
        if ($customer->getCountryId()) {
            $this->zones = Country::getZonesByCountryId($customer->getCountryId());
            $this->zone_id = $customer->getZoneId();
        }
        if (!Cart::getSingleton()->hasProducts()) {
            $this->redirect('/cart');
        }

        if ($this->Request->isPost()) {
            $billingData = (array)$this->Request->postParam('billing');
            if (empty($billingData['firstname'])) {
                $json['error']['firstname'] = 'Invalid firstname';
            }
            if (empty($billingData['lastname'])) {
                $json['error']['lastname'] = 'Invalid lastname';
            }
            if (empty($billingData['email'])) {
                $json['error']['email'] = 'Invalid email';
            }
            if (empty($billingData['address_1'])) {
                $json['error']['address_1'] = 'Invalid address';
            }
            if (empty($billingData['zipcode'])) {
                $json['error']['zipcode'] = 'Invalid zipcode';
            }
            if (empty($billingData['city'])) {
                $json['error']['city'] = 'Invalid city';
            }
            if (empty($billingData['country_id'])) {
                $json['error']['country_id'] = 'Invalid country';
            }
            if (empty($billingData['telephone'])) {
                $json['error']['telephone'] = 'Invalid telephone';
            }
            if (empty($billingData['password'])) {
                $json['error']['password'] = 'Invalid password';
            }
            if (!empty($billingData['password']) && $billingData['password'] !== $billingData['confirm_password']) {
                $json['error']['confirm_password'] = 'Invalid confirm password';
            }

            if (empty($json)) {
                try {
                    if ($customer->getId()) {
                        $accountModel = new AccountCustomer($customer->getId());
                        $accountModel->editCustomer($billingData);
                    } else {
                        $accountModel = new AccountCustomer();
                        $accountModel->addCustomer($billingData);
                        $customer->login($billingData['email'], $billingData['password']);
                    }
                    if ($customer->getId()) {
                        $json['redirect'] = '/checkout/checkout/shipping_information';
                    }
                    $this->getSession()->add('billing_address_id', $customer->getAddressId());
                    if ($billingData['use_for_shipping']) {
                        $this->getSession()->add('shipping_address_id', $customer->getAddressId());
                    } else {
                        $this->getSession()->remove('shipping_address_id');
                    }
                } catch (\Exception $e) {
                    print_r($e);
                }
            }
        }
        $this->getSession()->add('current_step', self::STEP_BILLING);
        header('Content-type: application/json');
        $json['output'] = $this->Template->render();
        echo json_encode($json);
        die;
    }

    /**
     *
     */
    public function shippingInformation()
    {
        $this->checkAuthentication();
        $json = array();
        $this->progressTemplate->set('step', 'shipping');
        $this->progressHtml = $this->progressTemplate->render();

        $this->Template->setFilename('checkout/checkout/shipping_information');
        if (!Cart::getSingleton()->hasProducts()) {
            $this->redirect('/cart');
        }
        $this->title = 'Shipping information';
        $this->Cart = new Cart();
        $this->agree = true;
        $this->shipping_required = Cart::getSingleton()->hasShipping();
        $this->customer = $customer = new Customer((int)$this->getSession()->get('customer_id'));
        $this->countries = Country::getAll();
        if ($customer->getCountryId()) {
            $this->zones = Country::getZonesByCountryId($customer->getCountryId());
            $this->zone_id = $customer->getZoneId();
        }

        if (isset($this->getSession()->shipping_address_id)) {
            //@TODO: implement shipping address
        }

        if ($this->Request->isPost()) {
            $account = new AccountCustomer($this->getSession()->get('customer_id'));
            if (!($this->Request->postParam('use_for_shipping'))) {
                $account->addShippingAddress((array)$this->Request->postParam('shipping'));
            } else {
                $shippingData = array();
                $shippingData['customer_id'] = $customer->getId();
                $shippingData['firstname'] = $customer->getFirstName();
                $shippingData['lastname'] = $customer->getLastName();
                $shippingData['address_1'] = $customer->getAddress();
                $shippingData['address_2'] = $customer->getAddress_2();
                $shippingData['phone'] = $customer->getTelephone();
                $shippingData['mobile'] = $customer->getMobile();
                $shippingData['fax'] = $customer->getFax();
                $shippingData['zipcode'] = $customer->getZipcode();
                $shippingData['city'] = $customer->getCity();
                $shippingData['country_id'] = $customer->getCountryId();
                $shippingData['zone_id'] = $customer->getZoneId();
                $account->addShippingAddress($shippingData);
            }
            $json['redirect'] = '/checkout/checkout/shipping_method';
            header('Content-type: application/json');
            echo json_encode($json);
            die;
        }
    }

    /**
     *
     */
    protected function checkAuthentication()
    {
        if (!$this->getCustomer()->isLogged()) {
            $this->redirect('/checkout/checkout/login');
        }
    }

    /**
     *
     */
    public function shippingMethod()
    {
        $this->checkAuthentication();
        $this->progressTemplate->set('step', 'shipping_method');
        $this->progressHtml = $this->progressTemplate->render();
        $this->Template->setFilename('checkout/checkout/shipping_method');
        if (!Cart::getSingleton()->hasProducts()) {
            $this->redirect('/cart');
        }
        if (!Cart::getSingleton()->hasShipping()) {
            $this->redirect('/checkout/checkout/payment');
        }
        $json = array();
        $this->getSession()->add('current_step', self::STEP_SHIPPING);
        $this->title = 'Shipping method';
        $this->shipping_required = Cart::getSingleton()->hasShipping();

        if ($this->Request->isPost() && $this->Request->postParam('shipping_method')) {
            $this->getSession()->add('shipping_method', $this->Request->postParam('shipping_method'));
            $json['redirect'] = '/checkout/checkout/payment_method';
            $json['output'] = $this->Template->render();
            header('Content-type: application/json');
            echo json_encode($json);
            die;
        }
    }

    /**
     *
     */
    public function paymentMethod()
    {
        $this->checkAuthentication();
        $this->progressTemplate->set('step', 'payment');
        $this->progressHtml = $this->progressTemplate->render();
        $this->Template->setFilename('checkout/checkout/payment_method');
        if (!Cart::getSingleton()->hasProducts()) {
            $this->redirect('/cart');
        }
        $this->getSession()->add('current_step', self::STEP_PAYMENT);
        $json = array();
        $this->title = 'Payment method';
        if ($this->Request->isPost() && $this->Request->postParam('payment_method')) {
            $this->getSession()->add('payment_method', $this->Request->postParam('payment_method'));
            $json['output'] = $this->Template->render();
            $json['redirect'] = '/checkout/checkout/review';
            header('Content-type: application/json');
            echo json_encode($json);
            die;
        }
    }

    public function review()
    {
        $this->checkAuthentication();
        $data = array();
        if (!Cart::getSingleton()->hasProducts()) {
            $this->redirect('/cart');
        }
        foreach (Cart::getSingleton()->getContent() as $key => $qty) {
            $keyOpts = explode(':', $key);
            try {
                $product = new Product($keyOpts[0]);
                $data[] = array('product' => $product, 'qty' => $qty);
            } catch (Exception $e) {
                print_r($e);
            }
        }
        $this->getSession()->add('current_step', self::STEP_CONFIRM);
        $this->title = 'Review';
        $this->Products = $data;
        $this->sub_total = Cart::getSingleton()->getSubtotal();
        $this->grand_total = Cart::getSingleton()->getTotal();
        $this->progressTemplate->set('step', 'review');
        $this->progressHtml = $this->progressTemplate->render();

    }

    public function confirm()
    {
        $this->checkAuthentication();
        if (!Cart::getSingleton()->hasProducts()) {
            $this->redirect('/cart');
        }
        if ($this->Request->isXmlHttpRequest() && $this->Request->isPost()) {
            $orderData = array();
            $cart = Cart::getSingleton();
            $customer = new Customer((int)$this->getSession()->get('customer_id'));
            //
            $orderData['customer_id'] = $this->getSession()->get('customer_id');
            $orderData['grand_total'] = $cart->getTotal();
            $orderData['shipping_method'] = $this->getSession()->get('shipping_method');
            $orderData['payment_method'] = $this->getSession()->get('payment_method');
            //
            $shippingAddress = $customer->getShippingAddress();
            $orderData['shipping_name'] = $shippingAddress->firstname . ' ' . $shippingAddress->lastname;
            $orderData['shipping_address'] = $shippingAddress->address_1;
            $orderData['shipping_address_2'] = $shippingAddress->address_2;
            $orderData['shipping_zipcode'] = $shippingAddress->zipcode;
            $orderData['shipping_city'] = $shippingAddress->city;
            $orderData['shipping_country'] = $shippingAddress->country_name;
            $orderData['shipping_zone'] = $shippingAddress->zone_name;
            foreach ($cart->getContent() as $key => $qty) {
                $id = explode(':', $key);
                $product = new Product((int)$id[0]);
                $orderData['products'][] = array(
                    'product_id' => $product->getId(),
                    'name' => $product->model,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'quantity' => $qty,
                    'total' => number_format($product->price * floatval($qty), 2),
                    'tax' => number_format(floatval($qty) * $product->tax, 2)
                );
            }

            // Create order
            if ($orderId = Order::create($orderData)) {
                $this->getSession()->add('order_id', $orderId);
                $this->sendInvoiceMail($customer, $orderId, $orderData);
            }

            Cart::getSingleton()->clear();
            Session::instance()->remove('current_step');
            Session::instance()->remove('billing_address_id');
            Session::instance()->remove('shipping_method');
            Session::instance()->remove('shipping_method');
            Session::instance()->remove('payment_method');
            Session::instance()->remove('order_id');

            $json['output'] = $this->Template->render();
            $json['redirect'] = '/';
            header('Content-type: application/json');
            echo json_encode($json);
            die;
        }
    }

    /**
     *
     * @param Customer $customer
     * @param integer $orderId
     * @param array $orderData
     * @return type
     */
    protected function sendInvoiceMail(Customer $customer, $orderId, array $orderData)
    {
        $productsHtml = '';
        $total_tax = 0;
        foreach ($orderData['products'] as $product) {
            $total_tax += $product['tax'];
            $productsHtml .= '<tr>';
            $productsHtml .= '<td style="padding:10px; border:1px solid #d6d4d4">' . $product['product_id'] . '</td>';
            $productsHtml .= '<td style="padding:10px; border:1px solid #d6d4d4">' . $product['name'] . '</td>';
            $productsHtml .= '<td style="padding:10px; border:1px solid #d6d4d4">' . Stringify::currency_format($product['price']) . '</td>';
            $productsHtml .= '<td style="padding:10px; border:1px solid #d6d4d4">' . $product['quantity'] . '</td>';
            $productsHtml .= '<td style="padding:10px; border:1px solid #d6d4d4">' . Stringify::currency_format($product['total']) . '</td>';
            $productsHtml .= '<td style="padding:10px; border:1px solid #d6d4d4">' . Stringify::currency_format($product['tax']) . '</td>';
            $productsHtml .= '</tr>';
        }

        $i18n = new I18n();
        $content = file_get_contents(APP_PATH . '/mails/' . $i18n->getLanguage() . '/order_conf.html');
        $search_replace = array(
            '{firstname}' => $customer->getFirstName(),
            '{lastname}' => $customer->getLastName(),
            '{email}' => $customer->getEmail(),
            '{shop_logo}' => Configuration::get('shop_logo'),
            '{shop_name}' => Configuration::get('shop_name'),
            '{shop_url}' => Configuration::get('frontend_baseUrl'),
            '{order_name}' => $orderId,
            '{products}' => $productsHtml,
            '{total_paid}' => Stringify::currency_format($orderData['grand_total']),
            '{total_tax_paid}' => Stringify::currency_format($total_tax),
            '{total_shipping}' => Stringify::currency_format(0),
            '{payment}' => $orderData['payment_method'],
            '{delivery_block_html}' => $this->fetchShippingAddress($customer),
            '{invoice_block_html}' => $this->fetchBillingAddress($customer),
            '{date}' => date('d-m-Y H:i'),
            '{my_account_url}' => Configuration::get('frontend_baseUrl') . '/customer/account',
            '{history_url}' => Configuration::get('frontend_baseUrl') . '/customer/account/order'
        );
        $content = str_replace(array_keys($search_replace), array_values($search_replace), $content);

        $mail = new Mailer(true);
        try {
            $mail->AddAddress($customer->getEmail(), $customer->__toString());
            $mail->addCC('endayiragije@gmail.com', 'Ownershop');
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
     * Fetch the customer's billingaddress
     * @param Customer $customer
     * @return string
     */
    protected function fetchShippingAddress(Customer $customer)
    {
        $addressTemplate = new Template('checkout/misc/snippet_shipping_address', 'phtml');
        $addressTemplate->setViewSuffix('phtml');
        $addressTemplate->customer = $customer;
        return $addressTemplate->render();
    }

    /**
     * Fetch the customer's billingaddress
     * @param Customer $customer
     * @return string
     */
    protected function fetchBillingAddress(Customer $customer)
    {
        $addressTemplate = new Template('checkout/misc/snippet_billing_address', 'phtml');
        $addressTemplate->setViewSuffix('phtml');
        $addressTemplate->customer = $customer;
        return $addressTemplate->render();
    }

}
