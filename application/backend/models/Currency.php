<?php

namespace models;

/**
 * Description of Currency
 *
 * @author Emmanuel Ndayiragije <endayiragije@gmail.com>
 */
class Currency extends \Model
{
    protected $table = 'currency';
    protected $default;

    /**
     * Attribute constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
    }
    
    /**
     *
     * @return type
     * @throws \RuntimeException
     */
    public function getDefaultCurrency()
    {
        if (null === $this->default) {
            $this->default = static::find($this->table, array('WHERE' => '`default`=1'));
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
        $currency = static::find($this->table, array('WHERE' => sprintf("`code`='%s'", $code)));
        if (null === $currency) {
            throw new \RuntimeException("No default currency is defined. Please define one.");
        }
        return $currency[0];
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
