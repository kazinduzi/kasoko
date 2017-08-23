<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 2-7-2016
 * Time: 17:15
 */

namespace models\Product;

class ProductAttributeConfiguration extends \Model
{

    public $belongsTo = [
        'attribute' => [
            'model' => '\\models\\Attribute',
            'foreign_key' => 'attribute_id',
        ],
        'product' => [
            'model' => '\\Product',
            'foreign_key' => 'product_id',
        ],
        'product_attributes' => [
            'model' => '\\model\\Product\\AttributeValue',
            'foreign_key' => 'product_attributes_id',
        ],
    ];
    protected $table = 'product_attribute_configuration';

    /**
     * @return \type
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return \type
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @return mixed
     */
    public function save()
    {
        if (!$this->existsByConfigurationAttribute($this->product_attributes_id, $this->attribute_id)) {
            return parent::save();
        }
    }

    /**
     * @param $product_attributes_id
     * @param $attribute_id
     * @return bool
     * @internal param $product_id
     */
    public function existsByConfigurationAttribute($product_attributes_id, $attribute_id)
    {
        $this->getDbo()->query(sprintf("SELECT `id` FROM `{$this->table}` WHERE `product_attributes_id` = %d AND `attribute_id` = %d", $product_attributes_id, $attribute_id));
        $this->getDbo()->fetchAssocList();
        return $this->getDbo()->num_rows > 0;
    }

}
