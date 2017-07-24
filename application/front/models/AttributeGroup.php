<?php
namespace models;

/**
 * Description of A Model
 *
 * @ORM(
 *     id: integer,
 *     name: string,
 *     required: bool
 *   )
 *
 * @package Kazinduzi
 * @author Emmanuel_Leonie
 */
class AttributeGroup extends \Model
{
    public $hasMany = array(
        'attributes' => array(
            'model' => '\models\Attribute',
            'foreign_key' => 'attributegroup_id',
        ),
    );
    /**
     * @var string
     */
    protected $table = 'attributegroup';

    //protected $name;

    /**
     * AttributeGroup constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
    }


    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required == 1;
    }

    /**
     * Get attributes associated to the group
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Add attribute
     *
     * @param Attribute $attribute
     * @return $this
     */
    public function addAttribute(Attribute $attribute)
    {
        $this->attributes[] = $attribute;
        return $this;
    }

}