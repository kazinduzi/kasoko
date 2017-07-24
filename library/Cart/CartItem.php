<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 17-7-2016
 * Time: 0:44
 */

namespace library\Cart;


class CartItem
{

    /**
     * The rowID of the cart item.
     * @var string
     */
    public $rowId;
    /**
     * The ID of the cart item.
     * @var int|string
     */
    public $id;
    /**
     * The quantity for this cart item.
     * @var int|float
     */
    public $qty;
    /**
     * The name of the cart item.
     * @var string
     */
    public $name;
    /**
     * The price without TAX of the cart item.
     * @var float
     */
    public $price;
    /**
     * The options for this cart item.
     * @var array
     */
    public $options;
    /**
     * The FQN of the associated model.
     * @var string|null
     */
    private $associatedModel = null;
    /**
     * The tax rate for the cart item.
     * @var int|float
     */
    private $taxRate = 0;


    /**
     * CartItem constructor.
     * @param $id
     * @param $name
     * @param $price
     * @param array $options
     */
    public function __construct($id, $name, $price, array $options = [])
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Please supply a valid identifier.');
        }
        if (empty($name)) {
            throw new \InvalidArgumentException('Please supply a valid name.');
        }
        if (! is_numeric($price)) {
            throw new \InvalidArgumentException('Please supply a valid price.');
        }

        $this->id = $id;
        $this->name = $name;
        $this->price = floatval($price);
        $this->options = new \ArrayIterator($options);
        $this->rowId = $this->generateRowId($id, $options);

    }

    /**
     * @param $name
     * @return float|int|null
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        if ($name === 'priceTax') {
            return $this->price + $this->tax;
        }

        if ($name === 'subtotal') {
            return $this->qty * $this->price;
        }

        if ($name === 'total') {
            return $this->qty * ($this->priceTax);
        }

        if ($name === 'tax') {
            return $this->price * ($this->taxRate / 100);
        }

        if ($name === 'taxTotal') {
            return $this->tax * $this->qty;
        }

        if ($name === 'model') {

            return new $this->associatedModel($this->id);
        }

        return null;

    }

    /**
     * Returns the formatted price without TAX.
     *
     * @param int    $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     * @return string
     */
    public function price($decimals = 2, $decimalPoint = '.', $thousandSeperator = ',')
    {
        return number_format($this->price, $decimals, $decimalPoint, $thousandSeperator);
    }

    /**
     * Returns the formatted price with TAX.
     *
     * @param int    $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     * @return string
     */
    public function priceTax($decimals = 2, $decimalPoint = '.', $thousandSeperator = ',')
    {
        return number_format($this->priceTax, $decimals, $decimalPoint, $thousandSeperator);
    }

    /**
     * Returns the formatted subtotal.
     * Subtotal is price for whole CartItem without TAX
     *
     * @param int    $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     * @return string
     */
    public function subtotal($decimals = 2, $decimalPoint = '.', $thousandSeperator = ',')
    {
        return number_format($this->subtotal, $decimals, $decimalPoint, $thousandSeperator);
    }

    /**
     * Returns the formatted total.
     * Total is price for whole CartItem with TAX
     *
     * @param int    $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     * @return string
     */
    public function total($decimals = 2, $decimalPoint = '.', $thousandSeperator = ',')
    {
        return number_format($this->total, $decimals, $decimalPoint, $thousandSeperator);
    }

    /**
     * Returns the formatted tax.
     *
     * @param int    $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     * @return string
     */
    public function tax($decimals = 2, $decimalPoint = '.', $thousandSeperator = ',')
    {
        return number_format($this->tax, $decimals, $decimalPoint, $thousandSeperator);
    }

    /**
     * Returns the formatted tax.
     *
     * @param int    $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     * @return string
     */
    public function taxTotal($decimals = 2, $decimalPoint = '.', $thousandSeperator = ',')
    {
        return number_format($this->taxTotal, $decimals, $decimalPoint, $thousandSeperator);
    }

    /**
     * Set the quantity for this cart item.
     *
     * @param int|float $qty
     */
    public function setQuantity($qty)
    {
        if (! is_numeric($qty))
            throw new \InvalidArgumentException('Please supply a valid quantity.');

        $this->qty = $qty;
    }



    /**
     * @param $taxRate
     * @return $this
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * Associate the cart item with the given model.
     *
     * @param mixed $model
     * @return CartItem
     */
    public function associate($model)
    {
        $this->associatedModel = is_string($model) ? $model : get_class($model);

        return $this;
    }

    /**
     * Create a new instance from the given array.
     *
     * @param array $attributes
     * @return CartItem
     */
    public static function fromArray(array $attributes)
    {
        $options = isset($attributes['options']) ? $attributes['options'] : [];
        return new static($attributes['id'], $attributes['name'], $attributes['price'], $options);
    }

    /**
     * Create a new instance from the given attributes.
     *
     * @param int|string $id
     * @param string    $name
     * @param float     $price
     * @param array     $attributes
     * @return CartItem
     */
    public static function fromAttributes($id, $name, $price, array $attributes = [])
    {
        return new static($id, $name, $price, $attributes);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'rowId' => $this->rowId,
            'id' => $this->id,
            'name' => $this->name,
            'qty' => $this->qty,
            'price' => $this->price,
            'options' => $this->options,
            'tax' => $this->tax,
            'subtotal' => $this->subtotal
        ];
    }

    /**
     * Generate a unique id for the cart item.
     *
     * @param string $id
     * @param array  $options
     * @return string
     */
    protected function generateRowId($id, array $options)
    {
        ksort($options);

        return md5($id . serialize($options));
    }


}