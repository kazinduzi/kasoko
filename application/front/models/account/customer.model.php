<?php

class AccountCustomer extends Model
{

    const CUSTOMER_TABLE = 'customer';
    const ADDRESS_TABLE = 'address';
    const SHIPPING_ADDRESS_TABLE = 'shipping_address';

    public $table = self::CUSTOMER_TABLE;
    public $pk = 'customer_id';

    /**
     * Get customer by email
     *
     * @param string $email
     * @return \self|null
     * @throws Exception
     */
    public static function getByEmail($email)
    {
        $dbo = self::getInstance()->getDbo();
        $email = $dbo->real_escape_string($email);
        $dbo->select('*')->from(self::CUSTOMER_TABLE)
            ->where(sprintf('`email`=\'%s\' AND `status` = 1', $email))
            ->limit(1)
            ->buildQuery();
        if (null !== $row = $dbo->fetchAssocRow()) {
            return new self($row);
        }
        return null;
    }

    /**
     * Check if the customer with the email does already exists
     *
     * @param string $email
     * @return boolean
     */
    public function existsEmail($email)
    {
        $this->getDbo()->execute("SELECT `customer_id` FROM `" . self::CUSTOMER_TABLE . "` WHERE `email` = '" . $this->getDbo()->real_escape_string($email) . "'");
        return $this->getDbo()->num_rows != false;
    }

    /**
     *
     * @param type $data
     */
    public function addCustomer(array $data)
    {
        $this->getDbo()->autocommit(false);
        try {
            $hashPassword = password_hash($data['password'], PASSWORD_BCRYPT, array("cost" => 10));
            $this->getDbo()->execute("INSERT INTO `" . self::CUSTOMER_TABLE . "` SET `firstname` = '" . $this->getDbo()->real_escape_string($data['firstname']) . "', `lastname` = '" . $this->getDbo()->real_escape_string($data['lastname']) . "', `email` = '" . $this->getDbo()->real_escape_string($data['email']) . "', `telephone` = '" . $this->getDbo()->real_escape_string($data['telephone']) . "', `mobile` = '" . $this->getDbo()->real_escape_string($data['mobile']) . "', `fax` = '" . $this->getDbo()->real_escape_string($data['fax']) . "', `password` = '" . $this->getDbo()->real_escape_string($hashPassword) . "', `newsletter` = '" . (isset($data['newsletter']) ? (int)$data['newsletter'] : 0) . "', `approved` = '" . (isset($data['approved']) ? (int)$data['approved'] : 0) . "', `ip` = '" . $this->getDbo()->real_escape_string(Request::getInstance()->ip_address()) . "', `status` = '1', `date_added` = now();");
            $customer_id = $this->getDbo()->insert_id();
            $this->getDbo()->execute("INSERT INTO `" . self::ADDRESS_TABLE . "` SET `customer_id` = " . $customer_id . ", `firstname` = '" . $this->getDbo()->real_escape_string($data['firstname']) . "', `lastname` = '" . $this->getDbo()->real_escape_string($data['lastname']) . "', `company` = '" . $this->getDbo()->real_escape_string($data['company']) . "', `address_1` = '" . $this->getDbo()->real_escape_string($data['address_1']) . "', `address_2` = '" . $this->getDbo()->real_escape_string($data['address_2']) . "', `city` = '" . $this->getDbo()->real_escape_string($data['city']) . "', `zipcode` = '" . $this->getDbo()->real_escape_string($data['zipcode']) . "', `country_id` = '" . (isset($data['country_id']) ? (int)$data['country_id'] : 0) . "', `zone_id` = '" . (isset($data['zone_id']) ? (int)$data['zone_id'] : 0) . "', `date_added` = now();");
            if (!empty($data['use_for_shipping'])) {
                $this->getDbo()->execute("INSERT INTO `" . self::SHIPPING_ADDRESS_TABLE . "` SET `customer_id` = " . $customer_id . ", `firstname` = '" . $this->getDbo()->real_escape_string($data['firstname']) . "', `lastname` = '" . $this->getDbo()->real_escape_string($data['lastname']) . "', `phone` = '" . $this->getDbo()->real_escape_string($data['telephone']) . "', `mobile` = '" . $this->getDbo()->real_escape_string($data['mobile']) . "', `address_1` = '" . $this->getDbo()->real_escape_string($data['address_1']) . "', `address_2` = '" . $this->getDbo()->real_escape_string($data['address_2']) . "', `city` = '" . $this->getDbo()->real_escape_string($data['city']) . "', `zipcode` = '" . $this->getDbo()->real_escape_string($data['postcode']) . "', `country_id` = '" . (isset($data['country_id']) ? (int)$data['country_id'] : 0) . "', `zone_id` = '" . (isset($data['zone_id']) ? (int)$data['zone_id'] : 0) . "', `date_added` = now();");
            }
            $this->getDbo()->commit();
            return new static($customer_id);
        } catch (\Exception $e) {
            $this->getDbo()->rollback();
            print_r($e);
        }
    }

    /**
     *
     * @param array $data
     * @return \static
     */
    public function editCustomerAddress(array $data)
    {
        $this->getDbo()->autocommit(false);
        try {
            // Edit personal information
            $this->getDbo()->execute("UPDATE `" . self::CUSTOMER_TABLE . "` SET `firstname` = '" . $this->getDbo()->real_escape_string($data['firstname']) . "', `lastname` = '" . $this->getDbo()->real_escape_string($data['lastname']) . "', `telephone` = '" . $this->getDbo()->real_escape_string($data['telephone']) . "', `mobile` = '" . $data['mobile'] . "', `fax` = '" . $this->getDbo()->real_escape_string($data['fax']) . "' WHERE `customer_id` = '" . (int)$this->getId() . "';");
            // Edit billing information
            $this->getDbo()->execute("UPDATE `" . self::ADDRESS_TABLE . "` SET `firstname` = '" . $this->getDbo()->real_escape_string($data['firstname']) . "', `lastname` = '" . $this->getDbo()->real_escape_string($data['lastname']) . "', `company` = '" . $this->getDbo()->real_escape_string($data['company']) . "', `address_1` = '" . $data['address_1'] . "', `address_2` = '" . $this->getDbo()->real_escape_string($data['address_2']) . "', `city` = '" . $this->getDbo()->real_escape_string($data['city']) . "', `zipcode` = '" . $this->getDbo()->real_escape_string($data['zipcode']) . "', `country_id` = '" . (int)$data['country_id'] . "', `zone_id` = '" . (int)$data['zone_id'] . "', `date_modified` = now() WHERE `customer_id` = '" . (int)$this->getId() . "';");
            $this->getDbo()->commit();
            return new static($this->getId());
        } catch (Exception $e) {
            $this->getDbo()->rollback();
        }
    }

    public function editCustomerShippingAddress(array $data)
    {
        $this->getDbo()->autocommit(false);
        try {
            // Edit shipping address information
            $this->getDbo()->execute("UPDATE `" . self::SHIPPING_ADDRESS_TABLE . "` SET `firstname` = '" . $this->getDbo()->real_escape_string($data['firstname']) . "', `lastname`  = '" . $this->getDbo()->real_escape_string($data['lastname']) . "', `address_1` = '" . $this->getDbo()->real_escape_string($data['address_1']) . "', `address_2` = '" . $this->getDbo()->real_escape_string($data['address_2']) . "', `phone` = '" . $this->getDbo()->real_escape_string($data['phone']) . "', `fax` = '" . $this->getDbo()->real_escape_string($data['fax']) . "', `city` = '" . $this->getDbo()->real_escape_string($data['city']) . "', `zipcode` = '" . $this->getDbo()->real_escape_string($data['zipcode']) . "', `country_id` = '" . (int)$data['country_id'] . "', `zone_id` = '" . (int)$data['zone_id'] . "', `date_modified` = now() WHERE `customer_id` = '" . (int)$this->getId() . "';");
            $this->getDbo()->commit();
            return new static($this->getId());
        } catch (\Exception $e) {
            $this->getDbo()->rollback();
        }
    }

    /**
     *
     * @param array $data
     * @return type
     */
    public function editCustomer(array $data)
    {
        $this->getDbo()->autocommit(false);
        try {
            // Edit personal information
            $this->getDbo()->execute("UPDATE `" . self::CUSTOMER_TABLE . "` SET `firstname` = '" . $this->getDbo()->real_escape_string($data['firstname']) . "', `lastname`  = '" . $this->getDbo()->real_escape_string($data['lastname']) . "', `email`     = '" . $this->getDbo()->real_escape_string($data['email']) . "', `telephone` = '" . $this->getDbo()->real_escape_string($data['telephone']) . "', `fax` = '" . $this->getDbo()->real_escape_string($data['fax']) . "', `date_modified` = now() WHERE `customer_id` = '" . (int)$this->getId() . "';");
            // Edit billing information
            $this->getDbo()->execute("UPDATE `" . self::ADDRESS_TABLE . "` SET `firstname` = '" . $this->getDbo()->real_escape_string($data['firstname']) . "', `lastname`  = '" . $this->getDbo()->real_escape_string($data['lastname']) . "', `company` = '" . $this->getDbo()->real_escape_string($data['company']) . "', `address_1` = '" . $this->getDbo()->real_escape_string($data['address_1']) . "', `address_2` = '" . $this->getDbo()->real_escape_string($data['address_2']) . "', `city` = '" . $this->getDbo()->real_escape_string($data['city']) . "', `zipcode` = '" . $this->getDbo()->real_escape_string($data['zipcode']) . "', `country_id` = '" . (int)$data['country_id'] . "', `zone_id` = '" . (int)$data['zone_id'] . "', `date_modified` = now() WHERE `customer_id` = '" . (int)$this->getId() . "';");
            // Edit default shipping address
            $this->getDbo()->execute("UPDATE `" . self::SHIPPING_ADDRESS_TABLE . "` SET `firstname` = '" . $this->getDbo()->real_escape_string($data['firstname']) . "', `lastname` = '" . $this->getDbo()->real_escape_string($data['lastname']) . "', `address_1` = '" . $this->getDbo()->real_escape_string($data['address_1']) . "', `address_2` = '" . $this->getDbo()->real_escape_string($data['address_2']) . "', `city` = '" . $this->getDbo()->real_escape_string($data['city']) . "', `zipcode` = '" . $this->getDbo()->real_escape_string($data['zipcode']) . "', `country_id` = '" . (int)$data['country_id'] . "', `zone_id` = '" . (int)$data['zone_id'] . "', `date_modified` = now() WHERE `customer_id` = '" . (int)$this->getId() . "';");
            $this->getDbo()->commit();
            return new static($this->getId());
        } catch (Exception $e) {
            $this->getDbo()->rollback();
            print_r($e);
        }
    }

    /**
     *
     * @param type $email
     * @param type $passw
     * @return type
     */
    public function editPassword($email, $passw)
    {
        $hashPassword = password_hash($passw, PASSWORD_BCRYPT, array("cost" => 10));
        $this->getDbo()->execute("UPDATE `" . self::CUSTOMER_TABLE . "` SET `password` = '" . $this->getDbo()->real_escape_string($hashPassword) . "' WHERE `email` = '" . $this->getDbo()->real_escape_string($email) . "';");
        return $this->getDbo()->affected_rows();
    }

    /**
     *
     * @param type $newsletter
     * @return type
     */
    public function editNewsletter($newsletter)
    {
        $this->getDbo()->execute("UPDATE `" . self::CUSTOMER_TABLE . "` SET `newsletter` = '" . (int)$newsletter . "' WHERE `customer_id` = " . (int)$this->getId());
        return $this->getDbo()->affected_rows();
    }

    /**
     *
     * @return type
     */
    public function getAddress()
    {
        $this->getDbo()->execute("SELECT * FROM `" . self::ADDRESS_TABLE . "` WHERE `" . $this->pk . "` = " . $this->getId() . ";");
        return $this->getDbo()->fetchObject();
    }

    /**
     *
     * @param type $data
     * @return \AccountCustomer
     */
    public function addShippingAddress(array $data)
    {
        if (count(array_filter($data)) <= 0) {
            throw new \Exception(sprintf('Error occurs for shipping information in file: "%s" on line: "%d"', __FILE__, __LINE__));
        }
        $this->getDbo()->autocommit(false);
        try {
            $this->getDbo()->execute("INSERT INTO `" . self::SHIPPING_ADDRESS_TABLE . "` SET
                `customer_id` = " . $this->getId() . ",
                `firstname` = '" . $this->getDbo()->real_escape_string($data['firstname']) . "',
                `lastname` = '" . $this->getDbo()->real_escape_string($data['lastname']) . "',
                `address_1` = '" . $this->getDbo()->real_escape_string($data['address_1']) . "',
                `address_2` = '" . $this->getDbo()->real_escape_string($data['address_2']) . "',
                `phone` = '" . $this->getDbo()->real_escape_string($data['phone']) . "',
                `mobile` = '" . $this->getDbo()->real_escape_string($data['mobile']) . "',
                `fax` = '" . $this->getDbo()->real_escape_string($data['fax']) . "',
                `city` = '" . $this->getDbo()->real_escape_string($data['city']) . "',
                `zipcode` = '" . $this->getDbo()->real_escape_string($data['zipcode']) . "',
                `country_id` = '" . (isset($data['country_id']) ? (int)$data['country_id'] : 0) . "',
                `zone_id` = '" . (isset($data['zone_id']) ? (int)$data['zone_id'] : 0) . "',
                `date_added` = now()
                ON DUPLICATE KEY UPDATE
                `firstname` = '" . $this->getDbo()->real_escape_string($data['firstname']) . "',
                `lastname` = '" . $this->getDbo()->real_escape_string($data['lastname']) . "',
                `address_1` = '" . $this->getDbo()->real_escape_string($data['address_1']) . "',
                `address_2` = '" . $this->getDbo()->real_escape_string($data['address_2']) . "',
                `phone` = '" . $this->getDbo()->real_escape_string($data['phone']) . "',
                `mobile` = '" . $this->getDbo()->real_escape_string($data['mobile']) . "',
                `fax` = '" . $this->getDbo()->real_escape_string($data['fax']) . "',
                `city` = '" . $this->getDbo()->real_escape_string($data['city']) . "',
                `zipcode` = '" . $this->getDbo()->real_escape_string($data['zipcode']) . "',
                `country_id` = '" . (isset($data['country_id']) ? (int)$data['country_id'] : 0) . "',
                `zone_id` = '" . (isset($data['zone_id']) ? (int)$data['zone_id'] : 0) . "',
                `date_modified` = now();");
            $this->getDbo()->commit();
        } catch (\Exception $e) {
            $this->getDbo()->rollback();
            print_r($e);
        }
        return $this;
    }

}
