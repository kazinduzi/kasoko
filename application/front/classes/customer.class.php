<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Customer
{

    const CUSTOMER_TABLE = 'customer';
    const CUSTOMER_ADDRESS_TABLE = 'address';

    protected $session;
    protected $db;
    protected $customer_id;
    protected $firstname;
    protected $lastname;
    protected $email;
    protected $telephone, $mobile;
    protected $fax;
    protected $address_id;
    protected $ip;
    protected $newsletter;
    protected $company, $address_1, $address_2, $city, $zipcode, $country_id, $zone_id;
    private static $instance;

    /**
     * Create a singleton for the Customer
     * @return mixed
     */
    public static function getSingleton()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function __construct()
    {
        static $customerData = NULL;
        if ($this->getSession()->contains('customer_id')) {
            $query = "SELECT * FROM `" . self::CUSTOMER_TABLE . "` as `c` INNER JOIN `" . self::CUSTOMER_ADDRESS_TABLE . "` as `a` USING (`customer_id`) WHERE `customer_id` = '" . (int) $this->session->get('customer_id') . "' AND status = '1'";
            $this->getDb()->setQuery($query);
            $customerData = $this->getDb()->fetchObjectRow();
            if ($customerData) {
                $this->customer_id = $customerData->customer_id;
                $this->firstname = $customerData->firstname;
                $this->lastname = $customerData->lastname;
                $this->email = $customerData->email;
                $this->telephone = $customerData->telephone;
                $this->mobile = $customerData->mobile;
                $this->fax = $customerData->fax;
                $this->address_id = $customerData->address_id;
                $this->newsletter = $customerData->newsletter;
                $this->ip = $customerData->ip;

                // Address related data
                $this->company = $customerData->company;
                $this->address_1 = $customerData->address_1;
                $this->address_2 = $customerData->address_2;
                $this->city = $customerData->city;
                $this->zipcode = $customerData->zipcode;
                $this->country_id = $customerData->country_id;
                $this->zone_id = $customerData->zone_id;
            } else {
                $this->logout();
            }
        }
    }

    /**
     * 
     * @return \Session
     */
    public function getSession()
    {
        if (!$this->session instanceof \Session) {
            $this->session = \Kazinduzi::session();
        }
        return $this->session;
    }

    /**
     * 
     * @return \Database
     */
    public function getDb()
    {
        if (!$this->db instanceof \Database) {
            $this->db = \Kazinduzi::db()->clear();
        }
        return $this->db;
    }

    /**
     *
     * @param type $email
     * @param type $password
     * @return boolean
     */
    public function login($email, $password)
    {
        static $customerData;
        $query = "SELECT * FROM `customer` INNER JOIN `address` USING (`customer_id`) WHERE LOWER(`email`) = " . $this->getDb()->escape(strtolower($email)) . " AND `status` = '1' LIMIT 1;";
        $this->getDb()->setQuery($query);
        $customerData = $this->getDb()->fetchObjectRow();
        if (!password_verify($password, $customerData->password)) {
            return false;
        }
        if ($customerData) {
            $this->session->add('customer_id', $customerData->customer_id);
            $this->customer_id = $customerData->customer_id;
            $this->firstname = $customerData->firstname;
            $this->lastname = $customerData->lastname;
            $this->email = $customerData->email;
            $this->telephone = $customerData->telephone;
            $this->mobile = $customerData->mobile;
            $this->fax = $customerData->fax;
            $this->address_id = $customerData->address_id;
            $this->newsletter = $customerData->newsletter;
            $this->ip = $customerData->ip;
            // Address related data
            $this->company = $customerData->company;
            $this->address_1 = $customerData->address_1;
            $this->address_2 = $customerData->address_2;
            $this->city = $customerData->city;
            $this->zipcode = $customerData->zipcode;
            $this->country_id = $customerData->country_id;
            $this->zone_id = $customerData->zone_id;
            $this->getDb()->execute("UPDATE `customer` SET `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' WHERE `customer_id` = '" . (int) $customerData->customer_id . "';");
            return $this;
        }
        return false;
    }

    /**
     *
     */
    public function logout()
    {
        $this->getSession()->remove('customer_id');
        $this->getSession()->remove('cart');
        $this->getSession()->remove('current_step');
        $this->getSession()->remove('shipping_method');
        $this->getSession()->remove('payment_method');
        // $this->getSession()->destroy();
        $this->customer_id = false;
        $this->firstname = false;
        $this->lastname = false;
        $this->email = false;
        $this->telephone = false;
        $this->mobile = false;
        $this->fax = false;
        $this->address_id = false;
        $this->newsletter = false;
        $this->ip = false;
        $this->country_id = false;
        $this->zone_id = false;
        $this->city = false;
    }

    public function isLogged()
    {
        return $this->customer_id;
    }

    public function getId()
    {
        return $this->customer_id;
    }

    public function getFirstName()
    {
        return $this->firstname;
    }

    public function getLastName()
    {
        return $this->lastname;
    }

    public function getFullname()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function getMobile()
    {
        return $this->mobile;
    }

    public function getFax()
    {
        return $this->fax;
    }

    public function getAddressId()
    {
        return $this->address_id;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function getNewsletter()
    {
        return $this->newsletter;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getAddress()
    {
        return $this->address_1;
    }

    public function getAddress_2()
    {
        return $this->address_2;
    }

    public function getZipcode()
    {
        return $this->zipcode;
    }

    public function getCountryId()
    {
        return $this->country_id;
    }

    public function getZoneId()
    {
        return $this->zone_id;
    }

    public function getCountry()
    {
        try {
            return Country::getCountry($this->country_id);
        } catch (\Exception $e) {
            print_r($e);
        }
    }

    public function getZone()
    {
        try {
            return Country::getZone($this->country_id, $this->zone_id);
        } catch (\Exception $e) {
            print_r($e);
        }
    }

    public function getShippingAddress()
    {
        $this->getDb()->execute("SELECT `sa`.*, `c`.`name` AS `country_name`, `cz`.`name` AS `zone_name` FROM `shipping_address` AS `sa`
            LEFT JOIN `country` AS `c` ON `sa`.`country_id` = `c`.`id`
            LEFT JOIN `country_zone` AS `cz` ON `sa`.`zone_id` = `cz`.`id`
            WHERE `sa`.`customer_id` = " . $this->customer_id . ";");
        return $this->getDb()->fetchObjectRow();
    }

    /**
     *
     * @return type
     */
    public function __toString()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

}
