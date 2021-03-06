<?php

defined('KAZINDUZI_PATH') || exit('No direct script access allowed');

class Image
{

    private $image;
    private $type;

    /**
     * constructor for the Image processing with GD Library
     * @param string $filename
     */
    public function __construct($filename)
    {
        if (!$this->isGD())
            throw new Exception('GD Library is not loaded');
        $this->load($filename);
    }

    /**
     * check if GD Library is loaded, and support typical image types like (JPG, PNG, GIF, WBMP)
     * @return bool
     */
    public function isGD()
    {
        if (extension_loaded('gd') && imagetypes() & IMG_PNG && imagetypes() & IMG_GIF && imagetypes() & IMG_JPG && imagetypes() & IMG_WBMP)
            return true;
        else
            return false;
    }

    /**
     * Load the image from the provided image
     * @access private
     * @param string $filename
     */
    private function load($filename)
    {
        list($width, $height, $type, $attr) = getimagesize($filename);
        $this->type = $type;
        if ($type == IMAGETYPE_JPEG) {
            $this->image = imagecreatefromjpeg($filename);
        } else if ($type == IMAGETYPE_GIF) {
            $this->image = imagecreatefromgif($filename);
        } else if ($type == IMAGETYPE_PNG) {
            $this->image = imagecreatefrompng($filename);
        }
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        if (is_resource($this->image))
            imagedestroy($this->image);
    }

    /**
     * Resizes the image to the given Height
     * @param int $height
     * @return Image
     */
    public function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
        return $this;
    }

    /**
     * get the Height of the image
     * @return int
     */
    public function getHeight()
    {
        return imagesy($this->image);
    }

    /**
     * get the width of the image
     * @return int
     */
    public function getWidth()
    {
        return imagesx($this->image);
    }

    /**
     * resize the image to the given  sizes
     * @param int $width
     * @param int $height
     */
    public function resize($width, $height)
    {
        $new_image = imagecreatetruecolor($width, $height);
        // apply the transparancy for PNG image
        if (IMAGETYPE_PNG == $this->getType()) {
            imagealphablending($this->image, false);
            imagefill($this->image, 0, 0, imagecolorallocatealpha($this->image, 0, 0, 0, 127));
            imagesavealpha($this->image, true);
        } // apply the transparancy for GIF image
        else if (IMAGETYPE_GIF == $this->getType()) {
            imagecolortransparent($this->image, imagecolorallocate($this->image, 0, 0, 0));
            imagetruecolortopalette($this->image, true, 256);
        }
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
        return $this;
    }

    /**
     * get the type of the image
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Resize the image to the given width
     * @param int $width
     * @return Image
     */
    public function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
        return $this;
    }

    /**
     * Scales the image with the provided scale
     * @param int $scale
     * @return Image
     */
    public function scale($scale)
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getImageAsString()
    {
        $data = null;
        ob_start();
        $this->output();
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    /**
     * output the image to the buffer
     * @param int $type
     */
    public function output($raw = false)
    {
        if (headers_sent()) {
            throw new RuntimeException('Cannot show image, headers have already been sent');
        }

        if ($this->getType() == IMAGETYPE_JPEG) {
            ($raw) ? header('Content-type: image/jpeg') : null;
            imagejpeg($this->image);
        } else if ($this->getType() == IMAGETYPE_GIF) {
            ($raw) ? header('Content-type: image/gif') : null;
            imagegif($this->image);
        } else if ($this->getType() == IMAGETYPE_PNG) {
            ($raw) ? header('Content-type: image/png') : null;
            imagepng($this->image);
        }
    }

    /**
     * save the image
     * @param string $filename
     * @param int $compression
     * @param int $permissions
     */
    public function save($filename, $compression = 75, $permissions = null)
    {
        // Make sure the directory is writeable
        if (!is_writeable(dirname($filename))) {
            @chmod(dirname($filename), 0777);
            // Throw an exception if not writeable
            if (!is_writeable(dirname($filename))) {
                throw new RuntimeException('File is not writeable, and could not correct permissions: ' . $filename);
            }
        }
        if ($this->getType() == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $filename, $compression);
        } else if ($this->getType() == IMAGETYPE_GIF) {
            imagegif($this->image, $filename);
        } else if ($this->getType() == IMAGETYPE_PNG) {
            imagepng($this->image, $filename);
        }

        if ($permissions != null) {
            chmod($filename, $permissions);
        }

        return $this;
    }

}

/*
 * Example of usage
 * $Image = new Image($filename);
 * $Image->resizeToWidth($width);
 * $Image->save();
 */
