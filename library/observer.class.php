<?php defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * Base Observer class
 * @abstract
 */
abstract class Observer
{

    /**
     * Abstract method implemented by children to respond to
     * to changes in Observable object
     * @abstract
     * @return void
     */

    abstract public function update(&$sender, $arg);
}
