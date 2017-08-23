<?php



use Request;
use Response;

class NewsletterController extends Admin_controller
{

    public function __construct(Request $req, Response $res)
    {
        parent::__construct($req, $res);
        $this->Template->setViewSuffix('phtml');
    }

    public function index()
    {
        
    }

    public function subscribers()
    {
        
    }

}
