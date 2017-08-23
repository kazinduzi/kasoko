<?php

defined('KAZINDUZI_PATH') or die('No direct access script allowed');

/**
 * Description of Session_default
 *
 * @author Emmanuel_Leonie
 */
final class SessionDefault extends Session
{

    public function __construct($configs = null)
    {
        $configs = !isset($configs) ? self::$configs : $configs;
        if (!$this->ua) {
            $this->ua = Request::getInstance()->user_agent();
        }
        if (!$this->ip) {
            $this->ip = Request::getInstance()->ip_address();
        }

        //echo __CLASS__;
        //print_r($this->configs);
    }

}
