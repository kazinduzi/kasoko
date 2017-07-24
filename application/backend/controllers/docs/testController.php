<?php defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * Description of docController
 *
 * @author Emmanuel_Leonie
 */
class DocsTestController extends Admin_controller 
{
    public function index() 
    {        
        $this->Template = new Template('docs/xxxx_', 'phtml', array(1,2,3,4,5));
        $this->Template->setLayout('admin/layout');
        $this->Template->title = 'dfdfg';
        $this->Template->id = 56;        
        
    }

}