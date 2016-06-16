<?php defined('KAZINDUZI_PATH') || exit('No direct script access allowed');
/**
 * Kazinduzi Framework (http://framework.kazinduzi.com/)
 *
 * @author    Emmanuel Ndayiragije <endayiragije@gmail.com>
 * @link      http://kazinduzi.com
 * @copyright Copyright (c) 2010-2013 Kazinduzi. (http://www.kazinduzi.com)
 * @license   http://kazinduzi.com/page/license MIT License
 * @package   Kazinduzi
 */
final class Configuration {

    const CONFIGURATION_TABLE = 'configuration';

    static protected $dbo;

    /**
     * Set the configuration entry
     *
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    static public function set($name, $value) {
        self::$dbo = Kazinduzi::db();
        $value = self::$dbo->escape(serialize($value));
        $name = self::$dbo->escape($name);
        $sql = "INSERT INTO `" . self::CONFIGURATION_TABLE . "` SET `name` = {$name}, `value` = {$value} ON DUPLICATE KEY UPDATE `value` = {$value}";
        self::$dbo->autocommit(false);
        try {
            self::$dbo->setQuery($sql);
            self::$dbo->execute();
            self::$dbo->commit();
            return true;
        } catch(\Exception $e) {
            self::$dbo->rollback();
            print_r($e);
        }
    }

    /**
     * Get the configuration entry
     *
     * @param string $name
     * @return mixed
     */
    static public function get($name) {
        self::$dbo = Kazinduzi::db()->clear();
        self::$dbo->select('*')->from(self::CONFIGURATION_TABLE)->where("`name`='{$name}';");
        self::$dbo->buildQuery();
        $row = self::$dbo->fetchObjectRow();
        if ($row) {
            return $row->value = unserialize($row->value);
        }
        return null;
    }
    
    /**
     * Set the configuration entry by module
     *
     * @param string $name
     * @param string $value
     * @param string $moduleName
     * @return boolean
     */
    static public function setByModule($name, $value, $moduleName) {
        self::$dbo = Kazinduzi::db();
        $value = self::$dbo->escape(serialize($value));
        $name = self::$dbo->escape($name);
        $moduleName = self::$dbo->escape($moduleName);
        $sql = "INSERT INTO `" . self::CONFIGURATION_TABLE . "` SET `name` = {$name}, `value` = {$value}, `module` = {$moduleName} ON DUPLICATE KEY UPDATE `value` = {$value}";
        self::$dbo->autocommit(false);
        try {
            self::$dbo->setQuery($sql);
            self::$dbo->execute();
            self::$dbo->commit();
            return true;
        } catch(\Exception $e) {
            self::$dbo->rollback();
            print_r($e);
        }
    }
    
    /**
     * Get configuration entry by module
     * @param string $name
     * @param string $moduleName
     * @return null
     */
    static public function getByModule($name, $moduleName) {
        self::$dbo = Kazinduzi::db()->clear();
        self::$dbo->select('*')->from(self::CONFIGURATION_TABLE)->where("`name` ='{$name}' AND `module` = '{$moduleName}';");
        self::$dbo->buildQuery();
        $row = self::$dbo->fetchObjectRow();
        if ($row) {
            return $row->value = unserialize($row->value);
        }
        return null;
    }
    
    /**
     * Delete the configuration entry
     * 
     * @staticvar \Database $dbo
     * @param string $name
     */
    static public function delete($name) {
        static $dbo;
        $dbo = Kazinduzi::db()->clear();
        $dbo->autocommit(false);
        try{
            $dbo->setQuery("DELETE FROM " . self::CONFIGURATION_TABLE . " WHERE `name` ='{$name}';");
            $dbo->execute();
            $dbo->commit();
        } catch(\Exception $e) {
            self::$dbo->rollback();
            print_r($e);
        }        
    }
    
    /**
     * 
     * @staticvar \Database $dbo
     * @param string $name
     * @param string $moduleName
     */
    static public function deleteByModule($name, $moduleName) {
        static $dbo;
        $dbo = Kazinduzi::db()->clear();
        $dbo->autocommit(false);
        try{    
            $dbo->setQuery("DELETE FROM " . self::CONFIGURATION_TABLE . " WHERE `name` ='{$name}' AND `module` = '{$moduleName}';");        
            $dbo->execute();
            $dbo->commit();
        } catch(\Exception $e) {
            self::$dbo->rollback();
            print_r($e);
        }
    }
}