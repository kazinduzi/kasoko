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

namespace library\Product;

use Product;

/**
 * Product image class
 */
class Image
{

    const PRODUCT_IMAGE_TABLE = 'product_image';

    protected $dbo;
    protected $id;
    protected $product;
    protected $image;
    protected $thumb;
    protected $cover;
    protected $title;
    protected $description;
    protected $shortDescription;
    protected $postscriptum;
    protected $productImageTable = self::PRODUCT_IMAGE_TABLE;

    /**
     * 
     * @param mixed $id
     */
    public function __construct($id = null)
    {
	if (null !== $id) {
	    $this->load($id);
	}
    }

    /**
     * 
     * @param mixed $id
     */
    private function load($id)
    {
	if (is_int($id)) {
	    $this->getDbo()->setQuery(sprintf("select * from `" . $this->productImageTable . "` where `id` = %d", $id));
	    $row = $this->getDbo()->fetchObjectRow();
	    $this->id = $row->id;
	    $this->title = $row->title;
	    $this->product = new Product($row->product_id);
	    $this->image = $row->image;
	    $this->thumb = $row->thumb;
	    $this->cover = $row->cover;
	    $this->description = $row->description;
	    $this->shortDescription = $row->short_description;
	    $this->postscriptum = $row->postscriptum;
	    $this->position = $row->position;
	} elseif (is_object($id)) {
	    $this->id = $id->id;
	    $this->title = $id->title;
	    $this->product = new Product($id->product_id);
	    $this->image = $id->image;
	    $this->thumb = $id->thumb;
	    $this->cover = $id->cover;
	    $this->description = $id->description;
	    $this->shortDescription = $id->short_description;
	    $this->postscriptum = $id->postscriptum;
	    $this->position = $id->position;
	}
    }

    /**
     * 
     * @return type
     */
    public function getDbo()
    {
	if (null === $this->dbo) {
	    $this->dbo = \Kazinduzi::db();
	}
	return $this->dbo;
    }

    public function getId()
    {
	return $this->id;
    }

    /**
     * 
     * @param \Model $product
     * @return \library\Product\Image
     */
    public function setProduct(\Model $product)
    {
	$this->product = $product;
	return $this;
    }

    /**
     * 
     * @return type
     */
    public function getProduct()
    {
	return $this->product;
    }

    /**
     * 
     * @param strinh $image
     * @return \library\Product\Image
     */
    public function setImage($image)
    {
	$this->image = $image;
	return $this;
    }

    /**
     * 
     * @return string
     */
    public function getImage()
    {
	return $this->image;
    }

    /**
     * 
     * @param string $thumb
     * @return \library\Product\Image
     */
    public function setThumb($thumb)
    {
	$this->thumb = $thumb;
	return $this;
    }

    /**
     * 
     * @return string
     */
    public function getThumb()
    {
	return $this->thumb;
    }

    /**
     * Set image as cover
     * 
     * @param boolean $cover
     * @return \library\Product\Image
     */
    public function setCover($cover)
    {
	if (true === $cover) {
	    $this->resetProductCover();
	}
	$this->cover = (bool) $cover;
	return $this;
    }

    /**
     * Is the image a cover
     * @return boolean
     */
    public function isCover()
    {
	return (bool) $this->cover;
    }

    /**
     * 
     * @param string $title
     * @return \library\Product\Image
     */
    public function setTitle($title)
    {
	$this->title = $title;
	return $this;
    }

    /**
     * 
     * @return string
     */
    public function getTitle()
    {
	return $this->title;
    }

    /**
     * 
     * @param string $description
     * @return \library\Product\Image
     */
    public function setDescription($description)
    {
	$this->description = $description;
	return $this;
    }

    /**
     * 
     * @return string
     */
    public function getDescription()
    {
	return $this->description;
    }

    /**
     * 
     * @param string $short
     * @return \library\Product\Image
     */
    public function setShort($short)
    {
	$this->shortDescription = $short;
	return $this;
    }

    /**
     * 
     * @return string
     */
    public function getShort()
    {
	return $this->shortDescription;
    }

    /**
     * 
     * @param string $postscriptum
     * @return \library\Product\Image
     */
    public function setPostscriptum($postscriptum)
    {
	$this->postscriptum = $postscriptum;
	return $this;
    }

    /**
     * 
     * @return string
     */
    public function getPostscriptum()
    {
	return $this->postscriptum;
    }

    public function setPosition($position)
    {
	$this->position = $position;
	return $this;
    }

    /**
     * 
     * @return integer
     */
    public function getPosition()
    {
	return $this->position;
    }

    /**
     * Save
     */
    public function save()
    {
	if (!empty($this->id)) {
	    $queryStatement = "update `" . $this->productImageTable . "` SET "
		    . "`product_id` = %d,"
		    . "`image` = '%s',"
		    . "`thumb` = '%s', "
		    . "`cover` = %d,"
		    . "`title` = '%s',"
		    . "`description` = '%s',"
		    . "`short_description` = '%s',"
		    . "`postscriptum` = '%s' WHERE `id` = %d";
	    $this->getDbo()->autocommit(false);
	    try {
		$this->getDbo()->setQuery(
			sprintf($queryStatement, $this->product->getId(), $this->getImage(), $this->getThumb(), (int) $this->isCover(), $this->getTitle(), $this->getDescription(), $this->getShort(), $this->getPostscriptum(), $this->id)
		);
		$this->getDbo()->execute();
		$this->getDbo()->commit();
	    } catch (Exception $e) {
		$this->getDbo()->rollback();
		throw $e;
	    }
	} else {
	    $queryStatement = "replace into `" . $this->productImageTable . "` set "
		    . "`product_id` = %d, "
		    . "`image` = '%s',"
		    . "`thumb` = '%s', "
		    . "`cover` = %d,"
		    . "`title` = '%s',"
		    . "`description` = '%s',"
		    . "`short_description` = '%s',"
		    . "`postscriptum` = '%s'";
	    $this->getDbo()->autocommit(false);
	    try {
		$this->getDbo()->setQuery(
			sprintf($queryStatement, $this->product->getId(), $this->getImage(), $this->getThumb(), (int) $this->isCover(), $this->getTitle(), $this->getDescription(), $this->getShort(), $this->getPostscriptum()
			)
		);
		$this->getDbo()->execute();
		$this->getDbo()->commit();
	    } catch (Exception $e) {
		$this->getDbo()->rollback();
		throw $e;
	    }
	}
    }

    /**
     * Delete the file
     * @return boolean
     */
    public function delete()
    {
	if (empty($this->id)) {
	    return false;
	} else {
	    $this->getDbo()->autocommit(false);
	    try {
		$this->getDbo()->setQuery(sprintf('delete from `%s` where id = %d', $this->productImageTable, $this->id));
		$this->getDbo()->execute();
		$this->getDbo()->commit();
		$this->deleteFile();
	    } catch (Exception $e) {
		$this->getDbo()->rollback();
		print_r($e);
	    }
	}
    }

    /**
     * Delete the file from the filesystem
     * 
     * @return boolean
     */
    protected function deleteFile()
    {
	$linkImage = KAZINDUZI_PATH . $this->getImage();
	$linkThumb = KAZINDUZI_PATH . $this->getThumb();
	@unlink(str_replace('..', '', $linkImage));
	@unlink(str_replace('..', '', $linkThumb));
	return true;
    }

    /**
     * Reset the cover setting for this product
     * 
     * @return void
     */
    protected function resetProductCover()
    {
	$this->getDbo()->autocommit(false);
	try {
	    $this->getDbo()->setQuery(sprintf('UPDATE `%s` SET `cover` = 0 WHERE `product_id` = %d', $this->productImageTable, $this->getProduct()->getId()));
	    $this->getDbo()->execute();
	    $this->getDbo()->commit();
	} catch (\Exception $e) {
	    $this->getDbo()->rollback();
	    print_r($e);
	}
    }

}
