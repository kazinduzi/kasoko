<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 6-7-2016
 * Time: 21:12
 */

namespace Helpers\Iterators;

use models\Attribute;
use models\AttributeGroup;

class ProductAttributeConfigurationsIterator extends \IteratorIterator
{

    private $attributes, $attributeGroups;

    /**
     * ProductAttributesIterator constructor.
     * @param \Traversable $iterator
     */
    public function __construct(\Traversable $iterator)
    {
        parent::__construct($iterator);

        foreach ($iterator as $item) {
            $attribute = new Attribute($item->attribute_id);
            $this->attributeGroups[$attribute->AttributeGroup->getId()][$attribute->getId()] = $attribute;
            $this->attributes[$attribute->getId()] = $attribute;
        }
    }

    /**
     * @return array|null
     */
    public function getAssociatedAttributesWithGroups()
    {
        if (!$this->attributeGroups) {
            return null;
        }
        $builtAttrGroupsIterator = new \ArrayIterator(($this->attributeGroups));
        return iterator_to_array($builtAttrGroupsIterator);
    }

    /**
     * @return array|null
     */
    public function getAttributes()
    {
        if (!$this->attributes) {
            return null;
        }
        $productAttributesIterator = new \ArrayIterator($this->attributes);
        return iterator_to_array($productAttributesIterator);
    }

}
