<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12-6-2016
 * Time: 23:52
 */
namespace models;

class Attribute extends \Model
{
    public $belongsTo = [
        'AttributeGroup' => array(
            'model' => '\models\AttributeGroup',
            'foreign_key' => 'attributegroup_id',
        ),
    ];
    protected $table = 'attribute';

    /**
     * Attribute constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        if (isset($this->value)) {
            return $this->value;
        }
    }

    /**
     * @return string
     */
    public function getValueLabel()
    {
        if (isset($this->value_label)) {
            return $this->value_label;
        }
    }
}