<?php

namespace library;

/**
 * Kazinduzi Framework (http://framework.kazinduzi.com/)
 *
 * @author    Emmanuel Ndayiragije <endayiragije@gmail.com>
 * @link      http://kazinduzi.com
 * @copyright Copyright (c) 2010-2013 Kazinduzi. (http://www.kazinduzi.com)
 * @license   http://kazinduzi.com/page/license MIT License
 * @package   Kazinduzi
 */
use ReflectionClass;
use DirectoryIterator;
use \Kazinduzi;
use library\Utils\XmlUtils;

class Plugin
{
    const TABLE_MODULE = 'plugin';
    const MODULE_CATEGORY_PAYMENT = 'payments_gateways';

    protected $id;
    protected $name;
    protected $path;
    protected $type;
    protected $active;
    protected $installed;
    protected $attributes = [
        'id' => 0,
        'name' => '',
        'displayName' => '',
        'version' => '',
        'description' => '',
        'author' => '',
        'img' => '',
        'confirmUninstall' => '',
        'type' => '',
    ];

    /**
     * @var Database instance
     */
    protected $dbo;

    /**
     * Constructor
     */
    public function __construct($moduleId = null)
    {
        if ($moduleId) {
            $this->loadFromDatabase($moduleId);
        }
        $this->path = dirname((new ReflectionClass(static::class))->getFileName());
        $this->attributes = array_merge($this->attributes, $this->loadFromDisk());
    }

    /**
     * 
     * @return type
     */
    private function loadFromDisk()
    {
        $dom = XmlUtils::loadFile($this->getPath() . '/config.xml');
        return XmlUtils::convertDomElementToArray($dom->documentElement);
    }

    /**
     * Load the module by id or name
     * 
     * @param integer|string $moduleId
     */
    private function loadFromDatabase($moduleId)
    {
        if (is_numeric($moduleId)) {
            $query = $this->getDbo()->select('*')->from(self::TABLE_MODULE)->where(sprintf("id = '%d'", (int)$moduleId))->buildQuery();
        }
        elseif (is_string($moduleId)) {
            $query = $this->getDbo()->select('*')->from(self::TABLE_MODULE)->where(sprintf("name = '%s'", $moduleId))->buildQuery();
        }
        
        if (null !== $row = $query->fetchAssocRow()) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->type = $row['type'];
            $this->version = $row['version'];
            $this->active = $row['active'];
            $this->installed = $row['installed'];
        }
    }

    /**
     * Get database object
     * 
     * @return \Database
     */
    public function getDbo()
    {
        if (!$this->dbo) {
            $this->dbo = \Kazinduzi::db()->clear();
        }
        return $this->dbo;
    }

    /**
     * Get module id
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->attributes['name'];
    }

    /**
     * Get module_path
     * 
     * @return string
     */
    public function getPath()
    {
        if (!$this->path) {
            $this->path = dirname((new ReflectionClass(static::class))->getFileName());
        }
        return $this->path;
    }

    /**
     * Get module's type
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get installed version
     * 
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Check module is active
     * 
     * @return bool
     */
    public function isActive()
    {
        return $this->active == true;
    }

    /**
     * 
     * @param array $params
     * @return string
     */
    public function getAdminUri(array $params = array())
    {
        return '/admin/modulemanager/module?module=' . $this->getName() . (empty($params) ? NULL : '&' . http_build_query($params));
    }

    /**
     * Add attributes
     * 
     * @param array $attributes
     */
    public function addAttributes(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
    }

    /**
     * 
     * @return type
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get attribute
     * 
     * @param string $name
     * @return string
     */
    public function getAttribute($name)
    {
        return $this->attributes[$name];
    }

    /**
     * Check if the module is installed
     * 
     * @return boolean
     */
    public function isInstalled()
    {
        return $this->installed == true;
    }

    /**
     * Install module
     * 
     * @return boolean
     * @throws \Exception
     */
    public function install()
    {
        if ($this->isInstalled()) {
            throw new \Exception($this->getName() . ' is already installed.');
        }
        require $this->getPath() . '/install.php';
        Kazinduzi::db()->clear();
        Kazinduzi::db()->autocommit(false);
        try {
            Kazinduzi::db()->setQuery(sprintf("INSERT INTO `%s` SET `name` = '%s', `installed` = 1", self::TABLE_MODULE, $this->getName()));
            Kazinduzi::db()->execute();
            Kazinduzi::db()->commit();
            return $this;
        } catch (\Exception $e) {
            Kazinduzi::db()->rollback();
            throw $e;
        }

        return false;
    }

    /**
     * Uninstall the module
     * 
     * @return $this
     * @throws \Exception
     */
    public function uninstall()
    {
        if ($this->isInstalled()) {
            require $this->getPath() . '/uninstall.php';
            Kazinduzi::db()->clear();
            Kazinduzi::db()->autocommit(false);
            try {
                Kazinduzi::db()->setQuery(sprintf("UPDATE `%s` SET `installed` = 0 WHERE `name` = '%s'", self::TABLE_MODULE, \Kazinduzi::db()->real_escape_string($this->getName())));
                Kazinduzi::db()->execute();
                Kazinduzi::db()->commit();
                return $this;
            } catch (\Exception $e) {
                Kazinduzi::db()->rollback();
            }
        } else {
            throw new \Exception('Can\'t the module #' . $this->getName() . '# uninstall an uninstalled module.');
        }
    }

    /**
     * Check if module can be upgraded
     * 
     * @return bool
     */
    public function canBeUpgraded()
    {
        return version_compare($this->getVersion(), $this->getAttribute('version'), '<');
    }

    /**
     * Get module list
     * 
     * @return array
     */
    public static function getList()
    {
        $moduleDir = array();
        foreach (new DirectoryIterator(PLUGINS_PATH) as $file) {
            if ($file->isDot()) {
                continue;
            }
            if ($file->isDir()) {
                $moduleDir[] = $file->getFilename();
            }
        }
        return $moduleDir;
    }

    /**
     * Get installed modules
     * 
     * @staticvar \Database $dbo
     * @return array
     */
    public static function getAllInstalled()
    {
        static $dbo;
        $dbo = Kazinduzi::db();
        $query = $dbo->select('*')
                        ->from(self::TABLE_MODULE)
                        ->where('installed = 1')->buildQuery();
        $installedPlugins = array();
        if (null !== $rows = $dbo->fetchAssocList()) {
            foreach ($rows as $row) {
                $moduleClassName = $row['classname'];
                $installedPlugins[] = new $moduleClassName($row['id']);
            }
        }
        return $installedPlugins;
    }

    /**
     * Get active modules
     * 
     * @staticvar \Database $dbo
     * @return array
     */
    public static function getAllActive()
    {
        static $dbo;
        $dbo = Kazinduzi::db();
        $query = $dbo->select('*')->from(self::TABLE_MODULE)->where('active = 1')->buildQuery();
        $activated = array();
        if (null !== $rows = $dbo->fetchAssocList()) {
            foreach ($rows as $row) {
                $moduleClassName = $row['classname'];
                $activated[] = new $moduleClassName($row['id']);
            }
        }
        return $activated;
    }

}
