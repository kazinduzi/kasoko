<?php

namespace Helpers;

use library\Image\Editor as ImageEditor;

class Image
{

    /**
     * Generate the thumbnail
     * 
     * @param string $filename
     * @param integer $width
     * @param integr $height
     * @return integer
     */
    static public function getThumbnail($filename, $width, $height)
    {
        $file_path = BASE_PATH . '/' . $filename;
        $imageEditor = ImageEditor::getInstance();
        $config = $imageEditor->getConfig();
        $ext = pathinfo($file_path, PATHINFO_EXTENSION);
        $basename = basename($file_path, ".$ext");
        $thumb_filename = strtolower($basename . '-' . $width . 'x' . $height . '.' . $ext);
        if (!is_writable($config->get('thumbnail_path'))) {
            throw new \Exception(sprintf('Folder <em>%s</em> is not writtable', $config->get('thumbnail_path')));
        } elseif (file_exists($config->get('thumbnail_path') . '/' . $thumb_filename)) {
            return $config->get('thumbnail_dir') . '/' . $thumb_filename;
        } else {
            $imageEditor->setFile($file_path);
            $imageEditor->resize($width, $height, true)->save($config->get('thumbnail_path') . '/' . $thumb_filename);
            return $config->get('thumbnail_dir') . '/' . $thumb_filename;
        }
    }

}
