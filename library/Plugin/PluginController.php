<?php
namespace library\Plugin;

use Controller;
use Request;
use framework\Plugin\ViewInterface;
use framework\Plugin\View;

/**
 * Description of AbstractController
 *
 * @author Emmanuel Ndayiragije <endayiragije@gmail.com>
 */
abstract class PluginController
{
	/**
	 * @var \Plugin
	 */
	protected $module;
		
	/**
	 *
	 * @var Controller
	 */
	protected $callerController;
	
	/*
	 * @var Request
	 */
	protected $request;


	/**
	 * Constructor
	 */
	public function __construct($module)
	{
		$this->module = $module;
		$this->init();
	}
	
	/**
	 * Initialise a module
	 */
	protected function init()
	{
		
	}
	
	/**
	 * Get request
	 * 
	 * @return Request
	 */
	public function getRequest()
	{
		return $this->request ?: $this->getCallerController()->getRequest();
	}


	/**
	 * 
	 * @param PluginCallback $module
	 * @return \Plugin\PluginController
	 */
	public function setPlugin(PluginCallback $module)
	{
		$this->module = $module;
		return $this;
	}
	
	/**
	 * 
	 * @return type
	 */
	public function getPlugin()
	{
		return $this->module;
	}

	/**
	 * 
	 * @param Controller $callerController
	 * @return \Plugin\PluginController
	 */
	public function setCallerController(\Controller $callerController)
	{
		$this->callerController = $callerController;
		return $this;
	}

	/**
	 * Get the caller controller, mainly a CMS's controller
	 * 
	 * @return Controller
	 */
	public function getCallerController()
	{
		return $this->callerController;
	}
	
	/**
     * 
     * @return \Theme
     */
    public function getCurrentTheme()
    {
		return \Theme::getByName(\Configuration::get('theme:active'));        	
    }

	/**
	 * 
	 * @return string
	 */
	public function getDefaultAction()
    {
        return 'index';
    }
	
	/**
     * Execute an action on the controller.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return Response
     */
	public function callAction($method, $parameters)
	{
		return call_user_func_array([$this, $method], $parameters);
	}
	
	/**
	 * Config module attached to page
	 */
	abstract public function config();
		
	/**
	 * 
	 * @param \Plugin\ViewInterface $view
	 * @return type
	 */
	protected function renderView(ViewInterface $view)
	{
		return $view->render();
	}
	
	/**
	 * 
	 * @param type $template
	 * @param array $variables
	 * @return type
	 */
	protected function renderTemplate($template, array $variables = [])
	{
		$view = new View($template);
		$view->setPlugin($this->module);
		
		$themeTemplateDir = $this->getCurrentTheme()->getPathname() . '/templates';
		
		if (is_dir($themeTemplateDir)) {
			$view->addViewsDirectory($themeTemplateDir);
		}
		
		$view->addViewsDirectory($this->module->getPath() . '/templates/');
		
        foreach ($variables as $key => $value) {
            $view->$key = $value;
        }

        return $view->render();
	}
	
}
