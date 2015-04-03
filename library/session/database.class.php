<?php defined('KAZINDUZI_PATH') or die('No direct access script allowed');

/**
 * Description of Session_default
 *
 * @author Emmanuel_Leonie
 */

final class SessionDatabase extends Session {
    /**
     *
     * @var type
     */
    private $db;

    /**
     * @var type
     */
    public $sessionTableName = 'session';

    /**
     * @var type
     */
    public $autoCreateSessionTable = true;

    /**
     *
     * @var type
     */
    private $oldSessionId;


    /**
     * Returns a value indicating whether to use custom session storage.
     * This method overrides the parent implementation and always returns true.
     * @return boolean whether to use custom storage.
     */
    public function getUseCustomStorage() {
        return true;
    }

    /**
     *
     * @param array $configs
     */
    public function __construct(array $configs = null) {

        $configs = !isset($configs) ? self::$configs : $configs;

        if (isset($configs['session_db_name']) && $configs['session_db_name']) {
            $this->sessionTableName = $configs['session_db_name'];
        }
        // Initialize the Db instance
        $this->db = Kazinduzi::db();

        if (isset($configs['timeout'])) {
            $this->setTimeout($configs['timeout']);
        }

        // If the client 'User-Agent' is not set from the DB session, we fetch the new one from the client request
        if (!$this->ua) {
            $this->ua = Request::getInstance()->user_agent();
        }
        // If the client IP-Address is not set from the DB session, we fetch the new one from the client request
        if (!$this->ip) {
            $this->ip = Request::getInstance()->ip_address();
        }

    }

    /**
     * Creates the session DB table.
     * @param $db the database connection
     * @param string $tableName the name of the table to be created
     */
    private function createSessionTable($db, $tableName) {
        $sql = "CREATE TABLE IF NOT EXISTS `{$tableName}` (
                    `id` char(160) not null COMMENT 'This length is because some session.hash_function like SHA512 produce a 128 chars long string',
                    `expire` int(10) not null,
                    `data` longtext not null,
                    `ip_address` varchar(16) not null default '0.0.0.0',
                    `user_agent` varchar(128) not null,
                    PRIMARY KEY (`id`)
                ) ENGINE=MyISAM default CHARSET=utf8;";
        $db->setQuery($sql);
        $db->execute();
    }

    /**
     * Session open handler.
     * Do not call this method directly.
     * @param string $savePath session save path
     * @param string $sessionName session name
     * @return boolean whether session is opened successfully
     */
    public function openSession($savePath, $sessionName) {
        if ($this->autoCreateSessionTable) {
            $this->createSessionTable($this->db, $this->sessionTableName);
        }
        $sql = sprintf("DELETE FROM `%s` WHERE `expire` < %s", $this->sessionTableName, time());
        $this->db->setQuery($sql);
        return (boolean)$this->db->execute();
    }

    /**
     * Session read handler.
     * Do not call this method directly.
     * @param string $id session ID
     * @return string the session data
     */
    public function readSession($id) {
        $now = time();
        $sql = "SELECT * FROM `{$this->sessionTableName}` WHERE `expire` > '$now' AND `id` = '$id'";
        $this->db->setQuery($sql);
        $data = $this->db->fetchAssocRow();
        if (empty($data)) {
            return array();
        }
        $this->oldSessionId = $data['id'];
        $this->ua = $data['user_agent'];
        $this->ip = $data['ip_address'];
        return $data['data'];
    }

    /**
     * Session write handler.
     * Do not call this method directly.
     * @param string $id session ID
     * @param string $data session data
     * @return boolean whether session write is successful
     */
    public function writeSession($id, $data) {
        // When session_regenerate_id(), update sesion_id in the DB
        if ( $this->oldSessionId && $this->oldSessionId <> $id ) {
            $sql = sprintf("UPDATE `%s` SET `id` = '{$id}' WHERE `id` = '%s'", $this->sessionTableName, $this->oldSessionId);
            $this->db->setQuery($sql);
            $this->db->execute();
        }
        // exception must be caught in session write handler
        // http://us.php.net/manual/en/function.session-set-save-handler.php
        try {
            $sql = sprintf("INSERT INTO `%s` (id, data, expire, ip_address, user_agent) VALUES ('%s', '%s', %s, '%s', '%s')", $this->sessionTableName, $id, $data, time()+$this->getTimeout(), Request::getInstance()->ip_address(), Request::getInstance()->user_agent()) . " ON DUPLICATE KEY UPDATE `data` ='{$data}'";
            $this->db->setQuery($sql);
            $this->db->execute();
        }
        catch (Exception $e) {
            if (KAZINDUZI_DEBUG) {
                echo $e->getMessage();
            }
            // it is too late to log an error message here
            return false;
        }
        return true;
    }
    /**
     * Session destroy handler.
     * Do not call this method directly.
     * @param string $id session ID
     * @return boolean whether session is destroyed successfully
     */
    public function destroySession($id) {
        $sql = sprintf("DELETE FROM `%s` WHERE `id` = '%s'", $this->sessionTableName, $id);
        $this->db->setQuery($sql);
        $this->db->execute();
        setcookie(session_name(), "", time() - 3600);
        return true;
    }

    /**
     * Session GC (garbage collection) handler.
     * Do not call this method directly.
     * @param integer $maxLifetime the number of seconds after which data will be seen as 'garbage' and cleaned up.
     * @return boolean whether session is GCed successfully
     */
    public function gcSession($maxLifetime = 10) {
        if (!$this->db->connected()) {
            return false;
        }
        // Determine the timestamp threshold with which to purge old sessions.
        $past = time() - $maxLifetime;
        $sql = sprintf("DELETE FROM `%s` WHERE `expire` < %s", $this->sessionTableName, (int)$past);
        // Remove expired sessions from the database.
        $this->db->setQuery($sql);
        echo $sql;
        return (boolean) $this->db->execute();
    }

}