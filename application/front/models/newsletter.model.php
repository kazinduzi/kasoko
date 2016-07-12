<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class Newsletter extends \Model
{

    const TABLE_NAME = 'newsletter';

    protected $table = self::TABLE_NAME;

    /**
     * Subscribe
     *
     * @param array $data
     * @return boolean
     * @throws Exception
     */
    public static function subscribe(array $data)
    {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid email');
        }
        try {
            $dbo = \Kazinduzi::db();
            $dbo->autocommit(false);
            $email = $dbo->real_escape_string($data['email']);
            $query = sprintf("INSERT INTO `%s` SET `email` = '%s', `status` = 1, `created` = now() ON DUPLICATE KEY UPDATE `email` = `email`;", self::TABLE_NAME, $email);
            $dbo->setQuery($query)->execute();
            $dbo->commit();
            return true;
        } catch (Exception $ex) {
            $dbo->rollback();
            return false;
        }
    }

}
