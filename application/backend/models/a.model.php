<?php

/**
 * Description of A Model
 *
 * @package Kazinduzi
 *
 * @author Emmanuel_Leonie
 */
class A extends Model
{

    public $table = 'A';
    public $hasMany = array(
        'B' => array(
            'model' => 'B',
            //'through' => 'entries_comments',
            'foreign_key' => 'a_id',
        ),
    );
    protected $id;

    /**
     *
     * @staticvar self $Instance
     * @param type $class
     * @param type $opts
     * @return \self
     */
    public static function getInstance()
    {
        static $Instance;
        if ($Instance === null) {
            $Instance = new static;
        }
        return $Instance;
    }
}