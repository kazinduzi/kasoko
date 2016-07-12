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

    public static function getBySlug($slug)
    {
        $sql = sprintf("SELECT * FROM `%s` WHERE `slug` = '%s'", self::MODEL_TABLE, static::getInstance()->getDbo()->real_escape_string($slug));
        $pages = static::getInstance()->findBySql($sql);
        if (count($pages) > 0) {
            return $pages[0];
        }
        return;
    }

}
