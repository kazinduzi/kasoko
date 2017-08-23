<?php

defined('KAZINDUZI_PATH') or die('No direct access script allowed');

/**
 * Description of file
 *
 * @author Emmanuel_Leonie
 */
final class SessionFile extends Session
{

    /**
     *
     * @var type
     */
    private static $savePath;

    /**
     *
     * @param array $configs
     */
    public function __construct(array $configs = null)
    {
        $configs = !isset($configs) ? self::$configs : $configs;
        self::$savePath = KAZINDUZI_PATH . DIRECTORY_SEPARATOR . 'tmp';
        session_save_path(self::$savePath);
    }

    /**
     * Returns a value indicating whether to use custom session storage.
     * This method overrides the parent implementation and always returns true.
     * @return boolean whether to use custom storage.
     */
    public function getUseCustomStorage()
    {
        return true;
    }

    /**
     *
     * @param type $savePath
     * @param type $sessionName
     * @return boolean
     */
    public function openSession($savePath, $sessionName)
    {
        //$savePath = self::$savePath;
        if (!is_dir(self::$savePath)) {
            mkdir(self::$savePath, 0777);
        }
        //$sessionName = self::$configs['session_name'];
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function closeSession()
    {
        return true;
    }

    /**
     *
     * @param type $id
     * @return type
     */
    public function readSession($id)
    {
        $file = self::$savePath . DIRECTORY_SEPARATOR . $id . '.session';
        return file_exists($file) ? unserialize(file_get_contents($file)) : array();
    }

    /**
     *
     * @param type $id
     * @param type $data
     * @return type
     */
    public function writeSession($id, $data)
    {
        if (!is_dir(self::$savePath)) {
            mkdir(self::$savePath, 0777, true);
        }
        return file_put_contents(self::$savePath . DIRECTORY_SEPARATOR . $id . '.session', serialize($data));
    }

    /**
     *
     * @param type $id
     * @return boolean
     */
    public function destroySession($id)
    {
        $sess_file = self::$savePath . DIRECTORY_SEPARATOR . $id . '.session';
        if (file_exists($sess_file)) {
            unlink($sess_file);
        }
        return true;
    }

    /**
     *
     * @param type $maxlifetime
     * @return boolean
     */
    public function gcSession($maxlifetime)
    {
        foreach (glob(self::$savePath . DS . "*.session") as $sess_file) {
            if (filemtime($sess_file) + $maxlifetime < time() && file_exists($sess_file)) {
                unlink($sess_file);
            }
        }
        return true;
    }

}
