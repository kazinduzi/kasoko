<?php

defined('KAZINDUZI_PATH') || exit('No direct script access allowed');

/**
 * Kazinduzi Framework (http://framework.kazinduzi.com/)
 *
 * @author    Emmanuel Ndayiragije <endayiragije@gmail.com>
 * @link      http://kazinduzi.com
 * @copyright Copyright (c) 2010-2013 Kazinduzi. (http://www.kazinduzi.com)
 * @license   http://kazinduzi.com/page/license MIT License
 * @package   Kazinduzi
 */
class Theme
{

    protected $themeFileInfo;
    protected $templates;

    public function __construct(\SplFileInfo $fileinfo)
    {
        $this->themeFileInfo = $fileinfo;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->themeFileInfo->getFilename();
    }

    /**
     *
     * @return type
     */
    public function getFileinfo()
    {
        return $this->themeFileInfo;
    }

    /**
     *
     * @param type $suffix
     * @return type
     */
    public function getTemplates($suffix = 'phtml')
    {
        $themePathname = $this->themeFileInfo->getPathname();
        $iterator = new GlobIterator($themePathname . '/*.' . $suffix, FilesystemIterator::KEY_AS_PATHNAME);
        foreach ($iterator as $templateFileinfo) {
            $this->templates[] = $templateFileinfo;
        }
        return $this->templates;
    }

    /**
     *
     * @param string $name
     * @return \Theme
     */
    public static function getByName($name)
    {
        $themePathname = THEME_PATH . DS . $name;
        return new Theme(new \SplFileInfo($themePathname));
    }

    /**
     *
     * @param string $path
     * @return \Theme
     */
    public static function getByPath($path)
    {
        return new static(new \SplFileInfo($path));
    }

}
