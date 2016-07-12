<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class cmsController extends Admin_controller
{

    public function __construct(Request $req, Response $res)
    {
	parent::__construct($req, $res);
	$this->Template->setViewSuffix('phtml');
    }

    /**
     * Index action
     */
    public function index()
    {
	$this->title = __('Pages overview');
	$this->pages = Page::getAll();
    }

    /**
     * Edit page action
     */
    public function editPage()
    {
	$this->Template->setFilename('cms/edit');
	$pageId = $this->getArg();
	$page = new Page($pageId);
	$this->page = $page;
	$this->title = __('Edit page') . ' ' . $page->title;
	if ($this->getRequest()->isPost()) {
	    try {
		$savemode = $_POST['save_mode'];
		$pageData = $this->getRequest()->postParam('page');
		$page->title = trim($pageData['title']);
			$page->slug = Stringify::slugify($pageData['title']);
		$page->meta_keywords = $pageData['meta_keywords'];
		$page->meta_description = $pageData['meta_description'];
		$page->content = $pageData['content'];
		$page->hidden = (int) $pageData['hidden'];
		$page->modified_by = 2;
		$datetime = new DateTime('now');
		$page->modified = $datetime->format('Y-m-d H:i:s');
		$page->save();
		if ('stay' === $savemode) {
		    $this->redirect('/admin/cms/edit_page/' . $page->getId());
		} else {
		    $this->redirect('/admin/cms');
		}
	    } catch (Exception $e) {
		print_r($e);
	    }
	}
    }

    public function createPage()
    {
	$this->Template->setFilename('cms/create');
	$this->title = __('Create page');
	if ($this->getRequest()->isPost()) {
	    $savemode = $_POST['save_mode'];
	    try {
		$page = new Page();
		$pageData = $this->getRequest()->postParam('page');
		$page->title = trim($pageData['title']);
			$page->slug = Stringify::slugify($pageData['title']);
		$page->meta_keywords = $pageData['meta_keywords'];
		$page->meta_description = $pageData['meta_description'];
		$page->content = $pageData['content'];
		$page->hidden = (int) $pageData['hidden'];
		$page->created_by = 2;
		$datetime = new DateTime('now');
		$page->created = $datetime->format('Y-m-d H:i:s');
		$page->save();
		if ('stay' === $savemode) {
		    $this->redirect('/admin/cms/edit_page/' . $page->getId());
		} else {
		    $this->redirect('/admin/cms');
		}
	    } catch (Exception $e) {
		print_r($e);
	    }
	}
    }

}
