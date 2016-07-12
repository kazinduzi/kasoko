<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Collection
 *
 * @author Emmanuel_Leonie
 */
class Collection implements Countable, ArrayAccess, IteratorAggregate
{

    public $objectArray = Array();


    public function doSomething()
    {
        echo "I'm doing something";
    }

    //**these are the required iterator functions   

    public function &offsetGet($offset)
    {
        if ($this->offsetExists($offset)) return $this->objectArray[$offset];
        else return (false);
    }

    public function offsetExists($offset)
    {
        if (isset($this->objectArray[$offset])) return true;
        else return false;
    }

    public function offsetSet($offset, $value)
    {
        if ($offset) $this->objectArray[$offset] = $value;
        else  $this->objectArray[] = $value;
    }

    public function offsetUnset($offset)
    {
        unset ($this->objectArray[$offset]);
    }

    public function count()
    {
        return count($this->objectArray);
    }

    public function &getIterator()
    {
        return new ArrayIterator($this->objectArray);
    }


}