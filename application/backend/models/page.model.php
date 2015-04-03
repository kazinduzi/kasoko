<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class Page extends Model
{

    const MODEL_TABLE = 'page';

    protected $table = self::MODEL_TABLE;

    /**
     * Get all pages
     * 
     * @return array
     */
    public static function getAll()
    {
        return static::getInstance()->findAll();
    }

}
