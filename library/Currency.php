<?php

/**
 * Kazinduzi Framework (http://framework.kazinduzi.com/)
 *
 * @author    Emmanuel Ndayiragije <endayiragije@gmail.com>
 * @link      http://kazinduzi.com
 * @copyright Copyright (c) 2010-2013 Kazinduzi. (http://www.kazinduzi.com)
 * @license   http://kazinduzi.com/page/license MIT License
 * @package   Kazinduzi
 */

namespace library;

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class Currency extends \Model
{

    const CURRENCY_TABLE = 'currency';

    /**
     * The value for the updated_at field.
     * @var string
     */
    protected $table = self::CURRENCY_TABLE;
    protected $current, $default;

    /**
     * 
     * @return type
     * @throws \RuntimeException
     */
    public function getDefaultCurrency()
    {
	if (null === $this->default) {
	    $this->default = static::find(self::CURRENCY_TABLE, array('WHERE' => '`default`=1'));
	    if (null === $this->default) {
		throw new \RuntimeException("No default currency is defined. Please define one.");
	    }
	}
	return $this->default[0];
    }

    /**
     * 
     * @param type $code
     * @return type
     * @throws \RuntimeException
     */
    public function getCurrencyByCode($code)
    {
	$currency = static::find(self::CURRENCY_TABLE, array('WHERE' => sprintf("`code`='%s'", $code)));
	if (null === $currency) {
	    throw new \RuntimeException("No default currency is defined. Please define one.");
	}
	return $currency[0];
    }

    /**
     * 
     * @return 
     */
    public function getCurrent()
    {
	$currentCode = empty($_SESSION['currency']) ? $this->getDefaultCurrency()->code : $_SESSION['currency'];
	return $this->getCurrencyByCode($currentCode);
    }

    /**
     * Get the [code] column value.
     *
     * @return string 
     */
    public function getCode()
    {
	return $this->code;
    }

    /**
     * Get the [rate] column value.
     *
     * @return double
     * @throws PropelException
     */
    public function getRate()
    {
	if (false === filter_var($this->rate, FILTER_VALIDATE_FLOAT)) {
	    throw new \Exception('Currency::rate is not float value');
	}

	return $this->rate;
    }

}
