<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

abstract class Controller_OLD
{

    /**
     *
     * @var type
     */
    private static $instance;

    /**
     * @var Request $Request
     */
    public $Request;

    /**
     *
     * @var Response $Response
     */
    public $Response;

    /**
     * @var string the name of the default action. Defaults to 'index'.
     */
    public $defaultAction = 'index';

    /**
     * @registry object
     */
    protected $registry = null;

    /**
     * Array of class methods of the controller
     * @var array
     */
    protected $methods = array();

    /**
     * Variable to hold models for this controller
     * @var Object
     */
    protected $models;

    /**
     *
     * @var type
     */
    protected $Template;

    /**
     *
     * @var type
     */
    private $action;

    /**
     *
     * @var type
     */
    private $args;

    /**
     *
     * @var type
     */
    private $params;

    /**
     * Methot to construct the controller
     * @param Request $Request
     * @param Response $Response
     */
    protected function __construct(Request $Request = null, Response $Response = null)
    {
        // Set Request for this controller
        $this->Request = $Request instanceof Request ? $Request : Request::getInstance();
        // sets the params
        $this->params = $this->Request->getParams();
        // Set Response for this controller
        $this->Response = $Response instanceof Response ? $Response : Response::getInstance();
        // Make or create template for the requested action
        $this->Template = new Template;

        // Get the public methods in this class.
        $reflector = new ReflectionClass(get_class($this));
        $reflectorName = $reflector->getName();
        $reflectorMethods = $reflector->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($reflectorMethods as $reflectorMethod) {
            $methodName = $reflectorMethod->getName();
            if (!in_array($methodName, $methods = get_class_methods('Controller')) || $methodName == 'index') {
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
        // Nothing by default
    }

    /**
     * Method to get a singleton controller instance.
     * @param    string    The name for the controller.
     * @return    mixed    Controller derivative class.
     */
    public static function getInstance($Request = null, $Response = null)
    {
        $controllerClassName = get_called_class();
        if (!empty(self::$instance))
            return self::$instance;
        if ($Request instanceof Request && $Response instanceof Response) {
            return self::$instance = new $controllerClassName($Request, $Response);
        } else {
            return self::$instance = new $controllerClassName();
        }
    }

    /**
     *
     * @return type
     */
    public function getTemplate()
    {
        return $this->Template;
    }

    /**
     *
     * @param type $template
     * @param array $data
     * @return \Controller
     */
    public function setTemplate($template, array $data = array())
    {
        $this->Template->setFilename($template, $data);
        return $this;
    }

    /**
     *
     * @param type $name
     */
    public function setLayout($name)
    {
        $this->Template->setLayout($name);
        return $this;
    }

    /**
     *
     * @param type $action
     * @return type
     */
    public function defaultAction($action = '')
    {
        if (!empty($action))
            $this->defaultAction = $action;
        return $this->defaultAction;
    }

    /**
     *
     * @param int $idx
     * @return mixed
     */
    public function getArg($idx = 0)
    {
        return $this->args[$idx];
    }

    /**
     *
     * @param type $name
     * @param type $config
     * @return type
     */
    public function getModel($name = '', $config = array())
    {
        if (empty($name))
            $name = $this->getName();
        return $this->createModel($name, $config);
    }

    /**
     *
     * @return type
     */
    public function getName()
    {
        if (!$this->name) {
            $r = null;
            if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
                exit('The application fails to get controller name');
            }
            $this->name = strtolower($r[1]);
        }
        return $this->name;
    }

    /**
     *
     * @param type $name
     * @param type $config
     * @return type
     */
    protected function createModel($name, $config = array())
    {
        // Clean the model name
        $modelName = preg_replace('/[^A-Z0-9_]/i', '', $name);
        return $result = Model::getInstance($modelName, $config);
    }

    /**
     * Method to run the requested action.
     * Execute first the requested action,
     * then wrap the execution of the action within the display method of the template engine
     * @return void
     */
    public function run()
    {
        // Set the layout to be rendered for this action
        $this->executeAction($this->getAction());
        $this->Template->setLayout($this->getLayout());
        $this->Template->display();
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
        // Nothing by default
    }

    /**
     *
     * @return type
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     *
     * @param type $args
     * @return \Controller
     */
    public function setArgs($args)
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
        // Nothing by default
    }

    /**
     *
     * @return type
     */
    public function getAction()
    {
        if ($this->action) {
            return $this->action;
        }
        //return $this->defaultAction();
    }

    /**
     *
     * @param type $action
     * @return \Controller
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     *
     * @return type
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
    public function &__get($key)
    {
        return $this->Template->__get($key);
        return null;
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
     * @return mixed
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
     * @all controllers must contain an index method
     */
    abstract public function index();

    /**
     *
     * @param type $object
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
     * @param type $url
     * @param type $status
     */
    protected function redirect($url, $status = 302)
    {
        header('Status: ' . $status);
        header('Location: ' . str_replace('&amp;', '&', $url));
        exit();
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
     * Validate Form against CSRF token
     * @return bool
     */
    protected function validateFormToken()
    {
        if (!($formKey = $this->getRequest()->postParam('csrf_token')) || $formKey != Security::token()
        ) {
            return false;
        }
        return true;
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
