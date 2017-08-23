<?php

/**
 * Description of FrontController
 *
 * @author Emmanuel_Leonie
 */
class FrontController
{

    private static $instance;
    private $controller;
    private $file;
    private $action;
    private $configs;
    private $CallableController;
    private $args = array();
    private $params = array();
    private $Response;
    private $Request;

    public function __construct(Request $Request, Response $Response)
    {
        $this->Request = $Request;
        $this->Response = $Response;
        $this->configs = Kazinduzi::getConfig()->toArray();
        $this->params = $this->Request->getParams();
        $this->checkRequestRoute();
    }

    /**
     *
     * @return null
     */
    private function checkRequestRoute()
    {
        if (Kazinduzi::$is_cli) {
            // Default protocol for command line is cli://
            $protocol = 'cli';
            // Get the command line options
            $options = CLI::options('route', 'method', 'get', 'post', 'referrer');
            if (isset($options['route'])) {
                // Use the specified route
                $route = $options['route'];
            } else if (true === $route) {
                $route = '';
            }

            // Use the specified method
            $method = isset($options['method']) ? strtoupper($options['method']) : 'GET';

            // Overload the global GET data
            if (isset($options['get'])) {
                parse_str($options['get'], $_GET);
            }

            // Overload the global POST data
            if (isset($options['post'])) {
                parse_str($options['post'], $_POST);
            }

            if (isset($options['referrer'])) {
                $referrer = $options['referrer'];
            }
        }

        if ($this->Request->getParam('rt')) {
            $route = $this->Request->getParam('rt');
        } elseif (null != $this->Request->serverParam('REQUEST_URI')) {
            $route = preg_replace('#\/admin\/?#', '', strtok($this->Request->serverParam('REQUEST_URI'), '?'));
        }

        // If route is present find which controller and action
        if (isset($route)) {
            $route = str_replace(array('//', '../'), '/', trim($route, '/'));
            $routeParts = array_filter(explode('/', rtrim($route, '/')));
        }

        // If no path is provided, thus http://localhost | http://hostname,
        // then we make sure to get default controller and default action
        if (empty($routeParts)) {
            $this->controller = $this->configs['default_controller'];
            $this->action = $this->configs['default_action'];
            return;
        }


        ###############################################################################

        /*
         * From the requested route, we fetch the most outer controller which match the requested action.
         * This will prevent the earlier matched controller met when walking through the route.
         * Example: {/path/to/outer}, with this requested route.
         *
         * If the controller PathToController does exists,
         * then the requested action outer will not be reached.
         * Thus, to surrender this, we MUST match the PathToOuterController most controller, if not we pop off the last part of the route
         * and we keep it for the action & arguments
         */
        $routeArgs = array();
        do {
            $controllerPath = implode('/', $routeParts);
            $controllerFilePath = APP_PATH . DS . 'controllers' . DS . str_replace('../', '', $controllerPath) . 'Controller.php';
            if (is_file($controllerFilePath)) {
                $this->file = $controllerFilePath;
                $this->controller = Inflector::camelize($controllerPath);
                break;
            }
            array_push($routeArgs, array_pop($routeParts));
        } while (!empty($routeParts));

        $routeParts = array_reverse($routeArgs);

        if (isset($routeParts) && !empty($routeParts[0])) {
            $this->action = $routeParts[0];
            array_shift($routeParts);
            $this->args = (array) $routeParts;
        }

        #########################################################################################
        if (empty($this->action)) {
            $this->action = $this->configs['default_action'];
        }
        if ($this->configs['default_controller'] === strtolower($this->controller)) {
            if ($this->getAction() == $this->configs['default_action']) {
                $this->action = $this->configs['default_action'];
            }
        }
        unset($routeParts, $route, $request_uri);
    }

    /**
     * Transform an "action" token into a method name
     * @return string
     * @internal param string $action
     */
    public function getAction()
    {
        $action = str_replace(array('.', '-', '_'), ' ', $this->action);
        $action = ucwords($action);
        $action = str_replace(' ', '', $action);
        $action = lcfirst($action);
        return $action;
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            return self::$instance = new self(Request::getInstance(), Response::getInstance());
        }
        return self::$instance;
    }

    /**
     *
     * @return \class|null
     */
    public function loadController()
    {
        $controller = $this->getController();
        if (empty($controller)) {
            render('error404.phtml', array('No controller is available'));
            exit(1);
        }
        //require_once CONTROLLERS_PATH . DS . Inflector::pathize($controller) . 'Controller.php';
        $class = ucfirst($controller . 'Controller');
        $this->CallableController = $class::getInstance($this->Request, $this->Response);
        if (!in_array($this->getAction(), get_class_methods(ucfirst($controller) . 'Controller'))) {
            $data = array('Method <b>' . $this->getAction() . '</b> is not defined in the controller <b>' . ucfirst($controller) . 'Controller</b>');
            render('error404.phtml', $data);
            exit(1);
        }
        if (is_callable(array($this->CallableController, $this->getAction()))) {
            $args = array_values($this->getArgs());
            $this->CallableController->setAction($this->getAction());
            $this->CallableController->setArgs($args);
        }
        return $this;
    }

    /**
     *
     * @return type
     */
    public function getController()
    {
        return $this->controller;
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
     * @return type
     */
    public function getCallableController()
    {
        return $this->CallableController;
    }

    public function getControllerToPath()
    {
        return Inflector::pathize($this->controller);
    }

    /**
     * @return mixed
     */
    public function getActionToView()
    {
        return $this->action;
    }

    /**
     *
     * @return type
     */
    public function getRequest()
    {
        return $this->Request;
    }

    /**
     *
     * @return type
     */
    public function getResponse()
    {
        return $this->Response;
    }

}
