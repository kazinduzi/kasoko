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

class Imagick_Editor extends Editor
{

    protected $image;

    public function __construct()
    {
	echo 'Imagick';
    }

    /**
     * Set the file to be edited
     *
     * @param string $file
     * @return \library\Image\Gd_Editor
     */
    public function setFile($file)
    {
	$this->file = $file;
	$this->load();
    }

    public function load()
    {
	
    }

    public function resize($max_w, $max_h, $crop = false)
    {
	
    }

    public function multi_resize($sizes)
    {
	
    }

    public function crop($src_x, $src_y, $src_w, $src_h, $dst_w = null, $dst_h = null, $src_abs = false)
    {
	
    }

    public function rotate($angle)
    {
	
    }

    public function flip($horz, $vert)
    {
	
    }

    public function stream($mime_type = null)
    {
	
    }

    public function save($dest_filename = null, $mime_type = null)
    {
	
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
	if ($this->image) {
	    // we don't need the original in memory anymore
	    $this->image->clear();
	    $this->image->destroy();
	}
    }

    /**
     *
     * @param type $args
     * @return boolean
     */
    public static function test($args = array())
    {
	if (!extension_loaded('imagick') || !class_exists('Imagick') || !class_exists('ImagickPixel')) {
	    return false;
	}
	if (version_compare(phpversion('imagick'), '2.2.0', '<')) {
	    return false;
	}
	$required_methods = array(
	    'clear',
	    'destroy',
	    'valid',
	    'getimage',
	    'writeimage',
	    'getimageblob',
	    'getimagegeometry',
	    'getimageformat',
	    'setimageformat',
	    'setimagecompression',
	    'setimagecompressionquality',
	    'setimagepage',
	    'scaleimage',
	    'cropimage',
	    'rotateimage',
	    'flipimage',
	    'flopimage',
	);
	// Now, test for deep requirements within Imagick.
	if (!defined('imagick::COMPRESSION_JPEG')) {
	    return false;
	}
	if (array_diff($required_methods, get_class_methods('Imagick'))) {
	    return false;
	}
	return true;
    }

}
