<?php

namespace models\Product;

use models\Attribute;

class AttributeValue extends \Model
{

    /**
     * @var array
     */
    public $belongsTo = [
        'product' => [
            'model' => '\\Product',
            'foreign_key' => 'product_id',
        ],
    ];
    public $hasMany = [
        'product_attribute_configurations' => [
            'model' => '\\models\\Product\\ProductAttributeConfiguration',
            'foreign_key' => 'product_attributes_id',
        ],
    ];

    /**
     * @var string
     */
    protected $table = 'product_attributes';

    /**
     * @return bool
     */
    public function hasPriceImpact()
    {
        return !empty((float) $this->price_impact);
    }

    /**
     * @return bool
     */
    public function hasQuantityImpact()
    {
        return !empty((float) $this->quantity_impact);
    }

    /**
     * @return float
     */
    public function getPriceImpact()
    {
        return isset($this->price_impact) ? (float) $this->price_impact : 0.00;
    }

    /**
     * @return float
     */
    public function getQuantityImpact()
    {
        return isset($this->quantity_impact) ? (float) $this->quantity_impact : 0.00;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $attributes = [];
        if (0 < count($this->getProductAttributeConfigurations())) {
            foreach ($this->getProductAttributeConfigurations() as $variation) {
                $attributes[] = $variation->attribute;
            }
        }
        return $attributes;
    }

    /**
     * @return array
     */
    public function getProductAttributeConfigurations()
    {
        return $this->product_attribute_configurations;
    }

}
