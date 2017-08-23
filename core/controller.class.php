<?php

defined('KAZINDUZI_PATH') || exit('No direct script access allowed');

/**
 * Kazinduzi Framework (http://framework.kazinduzi.com/)
 *
 * @author    Emmanuel Ndayiragije <endayiragije@gmail.com>
 * @link      http://kazinduzi.com
 * @copyright Copyright (c) 2010-2013 Kazinduzi. (http://www.kazinduzi.com)
 * @license   http://kazinduzi.com/page/license MIT License
 * @package   Kazinduzi
 */
abstract class Controller
{

    const DEFAULT_ACTION = 'index';
    const DEFAULT_CONTROLLER = 'index';

    private static $instance;
    public $Request;
    public $Response;
    public $defaultAction = self::DEFAULT_ACTION;
    protected $registry = null;
    protected $methods = array();
    protected $models;

    /**
     * @var Template
     */
    protected $Template;

    /**
     * @var bool
     */
    private $_in_layout_display = true;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $args;

    /**
     * @var array
     */
    private $params;

    /**
     * Method to construct the controller
     * @param Request $Request
     * @param Response $Response
     */
    protected function __construct(Request $Request = null, Response $Response = null)
    {
        $this->Request = $Request instanceof \Request ? $Request : \Request::getInstance();
        $this->params = $this->Request->getParams();
        $this->Response = $Response instanceof \Response ? $Response : \Response::getInstance();
        $this->Template = new \Template();
        $reflector = new \ReflectionClass(get_class($this));
        $reflectorMethods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($reflectorMethods as $reflectorMethod) {
            $methodName = $reflectorMethod->getName();
            if (!in_array($methodName, get_class_methods('Controller')) || $methodName == self::DEFAULT_ACTION) {
                $this->methods[] = strtolower($methodName);
            }
        }
        $this->init();
    }

    /**
     *
     */
    public function init()
    {
        
    }

    /**
     * Method to get a singleton controller instance.
     *
     * @param Request $Request
     * @param Response $Response
     * @return mixed Controller derivative class.
     * @internal param The $string name for the controller.
     */
    public static function getInstance(Request $Request, Response $Response)
    {
        $controllerClassName = get_called_class();
        if (!empty(self::$instance)) {
            return self::$instance;
        }
        if ($Request instanceof Request && $Response instanceof Response) {
            return self::$instance = new $controllerClassName($Request, $Response);
        } else {
            return self::$instance = new $controllerClassName();
        }
    }

    /**
     * All controllers must contain an index method
     */
    abstract public function index();

    /**
     *
     * @return Template
     */
    public function getTemplate()
    {
        return $this->Template;
    }

    /**
     *
     * @param string $template
     * @return \Controller
     */
    public function setTemplate($template)
    {
        $this->Template->setFilename($template);
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setLayout($name)
    {
        $this->Template->setLayout($name);
        return $this;
    }

    /**
     * Get the controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set the controller
     *
     * @param string $controller
     * @return \Controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @param string $action
     * @return string
     */
    public function defaultAction($action = '')
    {
        if (!empty($action)) {
            $this->defaultAction = $action;
        }
        return $this->defaultAction;
    }

    /**
     *
     * @param int $index
     * @return mixed
     */
    public function getArg($index = 0)
    {
        if (!isset($this->args[$index])) {
            return null;
        }
        return $this->args[$index];
    }

    /**
     * Get HTTP_Request object
     *
     * @return \Request
     */
    public function getRequest()
    {
        return $this->Request ? $this->Request : \Request::getInstance();
    }

    /**
     * Get HTTP_Response
     *
     * @return \Response
     */
    public function getResponse()
    {
        return $this->Response ? $this->Response : Response::getInstance();
    }

    /**
     *
     * @param string $name
     * @param array $config
     * @return \Model
     */
    public function getModel($name = '', $config = array())
    {
        if (empty($name)) {
            $name = $this->getName();
        }
        return $this->createModel($name, $config);
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        if (!$this->name) {
            $matches = null;
            if (!preg_match('/(.*)Controller/i', get_class($this), $matches)) {
                exit('The application fails to get controller name');
            }
            $this->name = strtolower($matches[1]);
        }
        return $this->name;
    }

    /**
     *
     * @param string $name
     * @param array $config
     * @return Model
     */
    protected function createModel($name, $config = array())
    {
        $modelName = preg_replace('/[^A-Z0-9_]/i', '', $name);
        return $result = Model::getInstance($modelName, $config);
    }

    /**
     *
     * @param string $url
     * @param int $status
     */
    public function redirect($url, $status = 302)
    {
        header('Status: ' . $status);
        header('Location: ' . str_replace('&amp;', '&', $url));
        exit();
    }

    /**
     * Method to run the requested action.
     * Execute first the requested action,
     * then wrap the execution of the action within the display method of the template engine
     *
     * @throws Exception
     */
    public function run()
    {
        try {
            $this->executeAction($this->getAction());
            if ($this->isLayoutDisplayed()) {
                $this->Template->setLayout($this->getLayout());
                $this->Template->display();
            } else {
                $this->Template->render();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Execute the requested action
     * First check if the methods {before &| after} are present for this action.
     * the dispatching will be executed as follows:
     * - $this->before(),
     * - $this->executeAction(),
     * - $this->after()
     * @param string $action
     * @throws Exception
     * @return void
     */
    public function executeAction($action)
    {
        try {
            $this->before();
            $this->{$action}($this->getArgs());
            $this->after();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Automatically executed before the controller action. Can be used to set
     * class properties, do authorization checks, and execute other custom code.
     * @return  void
     */
    public function before()
    {
        
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     *
     * @param array $args
     * @return \Controller
     */
    public function setArgs(array $args)
    {
        $this->args = $args;
        return $this;
    }

    /**
     * Automatically executed after the controller action. Can be used to apply
     * transformation to the Request Response, add extra output, and execute
     * other custom code.
     * @return  void
     */
    public function after()
    {
        
    }

    /**
     *
     * @return string
     */
    public function getAction()
    {
        if ($this->action) {
            return $this->action;
        }
        return null;
    }

    /**
     *
     * @param string $action
     * @return \Controller
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     *
     * @return bool
     */
    protected function isLayoutDisplayed()
    {
        return $this->_in_layout_display === true;
    }

    /**
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->Template->getLayout();
    }

    /**
     * Magically get data of the controller
     * @param string $key
     * @return mixed | null
     */
    public function __get($key)
    {
        return $this->Template->__get($key);
    }

    /**
     * Magic set data of the controller
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->Template->__set($key, $value);
    }

    /**
     * Magic method __isset, overloading the isset method
     * @param mixed $name
     * @return boolean
     */
    public function __isset($name)
    {
        return $this->Template->__isset($name);
    }

    /**
     * Magic method to be triggered when unset function is called
     * @param string $name
     * @return void
     */
    public function __unset($name)
    {
        $this->Template->__unset($name);
    }

    /**
     * @param $object
     */
    public function inspect($object)
    {
        $methods = get_class_methods($object);
        $data = get_class_vars(get_class($object));
        $odata = get_object_vars($object);
        $parent = get_parent_class($object);
        $output = 'Parent class: ' . $parent . "\n\n";
        $output .= "Methods:\n";
        $output .= "--------\n";
        foreach ($methods as $method) {
            $meth = new ReflectionMethod(get_class($object), $method);
            $output .= $method . "\n";
            $output .= $meth->__toString();
        }
        $output .= "\nClass data:\n";
        $output .= "-----------\n";
        foreach ($data as $name => $value) {
            $output .= $name . ' = ' . print_r($value, 1) . "\n";
        }
        $output .= "\nObject data:\n";
        $output .= "------------\n";
        foreach ($odata as $name => $value) {
            $output .= $name . ' = ' . print_r($value, 1) . "\n";
        }
        echo '<pre>', $output, '</pre>';
    }

    /**
     *
     * @param bool $flag
     * @return Controller
     */
    protected function setLayoutDisplayed($flag = true)
    {
        $this->_in_layout_display = (bool) $flag;
        return $this;
    }

    /**
     * Forces the user's browser not to cache the results of the current Request.
     *
     * @return void
     * @access protected
     */
    protected function disableCache()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    /**
     * Prevent cloning the controller
     * Declaring this magic method __clone will prevent all attempt to clone this
     * @return void
     */
    private function __clone()
    {
        
    }

}
