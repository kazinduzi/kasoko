<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * Description of Page
 *
 * @author Emmanuel_Leonie
 */
class Pages extends Model
{

    const MODEL_TABLE = 'pages';

    public static $current_page;
    public static $current_module;
    public $table = self::MODEL_TABLE;
    protected $id;

    /**
     * constructor accepts an array or an id
     *
     * @param null $id
     * @throws Exception
     * @internal param array|id $value
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public static function getInstance($class = __CLASS__, array $options = array())
    {
        static $instance;
        if ($instance === null) {
            $instance = new self;
        }
        return $instance;
    }

    /**
     *
     * @param type $id
     * @return type
     */
    public static function children($id)
    {
        $pages = array();
        foreach (Kazinduzi::db()->fetchObject("SELECT * FROM `pages` WHERE parent_id = " . $id . " AND visible_in_menu = true") as $page) {
            $pages[] = $page;
        }
        return $pages;
    }

    /**
     *
     * @return string
     */
    public static function activeQuery()
    {
        return '(visible = "yes" OR (visible = "date" AND start_date <= NOW() AND end_date >= NOW()))';
    }

    /**
     *
     * @return type
     */
    public function getAll()
    {
        return parent::find('pages');
    }

    /**
     *
     * @param type $id
     * @return type
     */
    public function getChildren($id)
    {
        $pages = array();
        foreach (Kazinduzi::db()->fetchObject("SELECT * FROM `" . $this->table . "` WHERE parent_id = " . $id . " AND visible_in_menu = true") as $page) {
            $pages[] = $page;
        }
        return $pages;
    }

    /**
     *
     * @return type
     */
    public function activeMenuMainPages()
    {

        foreach (parent::find($this->table, array("WHERE" => "parent_id = 0 AND visible != 'no' AND visible_in_menu = true", "ORDERBY" => "position ASC")) as $page) {
            $pages[] = (object) $page;
        }
        return (array) $pages;
    }

    /**
     *
     * @return type
     */
    public function mainPages()
    {
        foreach (parent::find($this->table, array("WHERE" => "parent_id = 0")) as $key => $value) {
            $mainpage = new StdClass();
            if (is_array($value)) {
                $mainpage = arrayToObject($value);
            } else {
                $mainpage->$key = $value;
            }
            $main_pages[] = $mainpage;
        }
        return (array) $main_pages;
    }

    /**
     *
     * @return type
     */
    public function getDefaultPage()
    {
        $defaultPage = arrayFirst(parent::find($this->table, array("WHERE" => "link = 'index' AND visible != 'no'")));
        self::$current_page = $defaultPage;
        return $defaultPage;
    }

    /**
     *
     * @param type $link
     * @return type
     */
    public function getPage($link = 'index')
    {
        $page = arrayFirst(parent::find($this->table, array("WHERE" => "link = '" . ($link) . "' AND visible != 'no' AND module_id = 0", "Limit" => 1)));
        if (empty($page)) {
            render('error404');
            exit(1);
        }
        self::$current_page = $page;
        return $page;
    }

    /**
     *
     * @param type $module
     * @return type
     */
    public function getModule($module)
    {
        $module = arrayFirst(Model::find($this->table, array("WHERE" => "link = '{$module}' AND visible != 0", "Limit" => 1)));
        if (empty($module)) {
            render('error404');
            exit(1);
        }
        self::$current_module = $module;
        return $module;
    }

    /**
     *
     * @return type
     */
    public function getCurrentPage()
    {
        return isset(self::$current_page) ? self::$current_page : self::$current_module;
    }

    /**
     *
     * @param int|type $site_id
     * @param string|type $qry
     * @return type
     */
    public function getAllPages($site_id = 0, $qry = '')
    {
        return Pages::model()->findAll();
    }

    /**
     *
     * @return type
     */
    public function getAllModules()
    {
        return Modules::model()->findAll();
    }

}
