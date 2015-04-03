<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class NewsletterController extends BaseController
{

    public function __construct(Request $req = null, Response $res = null)
    {
	parent::__construct($req, $res);
	$this->Template->setViewSuffix('phtml');
    }

    public function index()
    {
	$this->Template->setFilename('newsletter/form');
	$this->token = Security::token();
	$this->title = __('messages.newsletter');
    }

    public function subscribe()
    {
	$success = false;
	if ($this->getRequest()->isPost()) {
	    $newsletterData = $this->getRequest()->postParam('newsletter');
	    if (\Security::check($newsletterData['form_token'])) {
		$success = \Newsletter::subscribe($newsletterData);
	    }
	}
	echo json_encode($success);
	die();
    }

}
