<?php

/**
 * Description of B Model
 * @package Kazinduzi
 * @author Emmanuel_Leonie
 */
class B extends Model
{

    public $table = 'B';
    public $belongsTo = array(
        'A' => array(
            'foreign_key' => 'a_id',
        ),
    );
    protected $id;

    public static function getInstance()
    {
        static $Instance;
        if ($Instance === null) {
            $Instance = new static;
        }
        return $Instance;
    }

}
