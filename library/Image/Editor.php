<?php

/**
 * Kazinduzi Framework (http://framework.kazinduzi.com/)
 *
 * @author    Emmanuel Ndayiragije <endayiragije@gmail.com>
 * @link      http://kazinduzi.com
 * @copyright Copyright (c) 2010-2013 Kazinduzi. (http://www.kazinduzi.com)
 * @license   http://kazinduzi.com/page/license MIT License
 * @package   Kazinduzi
 */

namespace library\Image;

abstract class Editor
{

    private static $instance;
    protected $file = null;
    protected $size = null;
    protected $mime_type = null;
    protected $default_mime_type = 'image/jpeg';
    protected $quality = 90;

    public function __construct($file = null)
    {
        $this->file = $file;
    }

    /**
     * Get singleton instance
     *
     * @param string $editor
     */
    public static function instance($editor = 'Gd')
    {
        if (!self::$instance) {
            $editorClassName = '\library\Image\\' . $editor . '_Editor';
            self::$instance = new $editorClassName();
        }
        return self::$instance;
    }

    abstract public function load();

    abstract public function save($dest_filename = null);

    abstract public function resize($max_w, $max_h, $crop = false);

    abstract public function multi_resize($sizes);

    abstract public function crop($src_x, $src_y, $src_w, $src_h, $dst_w = null, $dst_h = null, $src_abs = false);

    abstract public function rotate($angle);

    abstract public function flip($horz, $vert);

    abstract public function stream($mime_type = null);

    /**
     *
     * @param type $quality
     * @return \Library\Image\Editor
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;
        return $this;
    }

    /**
     *
     * @param type $suffix
     * @param type $dest_path
     * @param type $extension
     * @return type
     */
    public function generateFilename($suffix = null, $dest_path = null, $extension = null)
    {
        // $suffix will be appended to the destination filename, just before the extension
        if (!$suffix) {
            $suffix = $this->getSuffix();
        }
        $info = pathinfo($this->file);
        $dir = $info['dirname'];
        $ext = $info['extension'];
        $name = basename($this->file, ".$ext");
        $new_ext = strtolower($extension ? $extension : $ext);
        if (!is_null($dest_path) && $_dest_path = realpath($dest_path)) {
            $dir = $_dest_path;
        }
        return $dir . DS . "{$name}-{$suffix}.{$new_ext}";
    }

    /**
     * Builds and returns proper suffix for file based on height and width.
     *
     * @since 3.5.0
     * @access public
     *
     * @return string suffix
     */
    public function getSuffix()
    {
        if (!$this->getSize()) {
            return false;
        }
        return "{$this->size['width']}x{$this->size['height']}";
    }

    public function getSize()
    {
        return $this->size;
    }

    /**
     * Sets current image size
     *
     * @param integer $width
     * @param integer $height
     * @return boolean
     */
    protected function updateSize($width = null, $height = null)
    {
        $this->size = array(
            'width' => (int) $width,
            'height' => (int) $height
        );
        return $this;
    }

}
