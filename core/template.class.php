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
class Template
{
    const DEFAULT_LAYOUT = 'default';
    const DEFAULT_LAYOUT_SUFFIX = 'phtml';
    const DEFAULT_VIEW_SUFFIX = 'tpl';
    const PHTML_EXTENSTION = 'phtml';
    const PHP_EXTENSTION = 'php';

    /**
     * @var type
     */
    protected $FrontController;
    /**
     *
     * @var type
     */
    private $data = array();
    /**
     *
     * @var type
     */
    private $file = false;
    /**
     * @var \Theme
     */
    private $Theme;
    /**
     *
     * @var type
     */
    private $layout = self::DEFAULT_LAYOUT;
    /**
     * @var string
     */
    private $viewSuffix = self::DEFAULT_VIEW_SUFFIX;
    /**
     * @var string
     */
    private $layoutSuffix = self::DEFAULT_LAYOUT_SUFFIX;
    /**
     * @var string
     */
    private $layoutFile;

    /**
     * Constructor of the template
     *
     * @param string $file
     * @param string $suffix
     * @param array $data
     */
    public function __construct($file = null, $suffix = null, array $data = null)
    {
        if ($suffix) {
            $this->setViewSuffix($suffix);
        }
        if (null !== $file) {
            $this->setFilename($file);
        }
        if (null !== $data) {
            $this->data = $data + $this->data;
        }
        $this->FrontController = FrontController::getInstance();
    }

    /**
     * Sets the view filename.
     * @param   string  The template filename
     * @return  View
     * @throws  View_Exception
     */
    public function setFilename($file)
    {
        $path_to_file = APP_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $file . '.' . $this->getViewSuffix();
        if (!is_file($path_to_file) || !is_readable($path_to_file)) {
            throw new InvalidArgumentException("The template [" . $file . "." . $this->getViewSuffix() . "] is invalid.");
        }
        $this->file = realpath($path_to_file);
        return $this;
    }

    /**
     *
     * @return type
     */
    public function getViewSuffix()
    {
        return $this->viewSuffix;
    }

    /**
     *
     * @param string $suffix
     * @return \Template
     */
    public function setViewSuffix($suffix)
    {
        $this->viewSuffix = $suffix;
        return $this;
    }

    /**
     * Singleton for getting instance of the Template for kazinduzi action requested
     * @return Template object
     */
    public static function getInstance($viewFile = null, $viewSuffix = null, array $viewData = array())
    {
        return new Template($viewFile, $viewSuffix, $viewData);
    }

    /**
     * Get the path of the viewpath
     * @return string
     */
    public function getFilename()
    {
        return $this->file;
    }

    /**
     * Assigns a value by reference. The benefit of binding is that values can
     * be altered without re-setting them. It is also possible to bind variables
     * before they have values. Assigned values will be available as a
     * variable within the view file:
     *
     * @param   string   variable name
     * @param   mixed    referenced variable
     * @return  $this
     */
    public function bind($key, &$value)
    {
        $this->data[$key] = &$value;
        return $this;
    }

    /**
     *
     * @param mixed $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     *
     * @set undefined data
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     *
     * @param type $key
     * @param type $value
     * @return Template
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->data[$k] = $v;
            }
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     *
     * @param type $key
     * @return null
     */
    public function get($key)
    {
        if (!isset($this->data[$key])) {
            return null;
        }
        $value = $this->data[$key];
        if ($value instanceof Closure) {
            return $value($this);
        } else {
            return $value;
        }
    }

    /**
     *
     * @param type $name
     * @return type
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * magic unset
     * @param mixed $name
     */
    public function __unset($name)
    {
        if (array_key_exists($name, $this->data)) {
            unset($this->data[$this->name]);
        }
    }

    /**
     * magic __toString()
     * @return type
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @see render the template voor the specific
     * controller
     */
    public function render()
    {
        $view_file = $this->FrontController->getActionToView();

        if (!$this->file) {
            $controller_path = $this->FrontController->getControllerToPath();
            $this->file = APP_PATH . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $controller_path . DIRECTORY_SEPARATOR . $view_file . '.' . $this->getViewSuffix();
        }

        if (!is_file($this->file) || !is_readable($this->file)) {
            header('HTTP/1.1 404 Not Found');
            render($this->getTheme()->getName() . '/elements/error404.phtml', array('Unknown View file: "' . $this->file . '"'));
            exit(1);
        }

        foreach ($this->data as $key => $value) {
            $$key = $value;
        }

        ob_start();
        ob_implicit_flush(false);
        try {
            include(realpath($this->file));
            return ob_get_clean();
        } catch (Exception $e) {
            ob_end_clean();
            print_r($e);
        }
        return ob_get_clean();
    }

    /**
     *
     * @return type
     */
    public function getTheme()
    {
        return $this->Theme;
    }

    /**
     * Set the theme
     *
     * @param \Theme $theme
     * @return \Template
     */
    public function setTheme(\Theme $theme)
    {
        $this->Theme = $theme;
        return $this;
    }

    /**
     * Display the content of the view rendered within the layout
     *
     * @params $content = data from the loaded template ,
     * @throws Exception
     * @internal param $layout = layout template to be used for the MVC
     */
    public function display()
    {
        if ($this->getTheme()) {
            $themePath = $this->getTheme()->getFileinfo()->getPathname();
            $this->layoutFile = $themePath . DS . $this->getLayout() . '.' . $this->getLayoutSuffix();
        } elseif (!is_file($this->layoutFile)) {
            $this->layoutFile = LAYOUT_PATH . DS . $this->getLayout() . '.' . $this->getLayoutSuffix();
        }
        $this->content_for_layout = $this->render();
        extract($this->data, EXTR_SKIP | EXTR_REFS);
        if (!is_readable($this->layoutFile)) {
            header('HTTP/1.1 404 Not Found');
            render($this->getTheme()->getName() . '/elements/error404.phtml', array('Unknown layout file: "' . $this->layoutFile . '"'));
            exit(1);
        }
        try {
            include $this->layoutFile;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * gets the layout for rendering the current view
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * sets the layout to be used for the current template view
     * @param string $layout
     * @return Template
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getLayoutSuffix()
    {
        return $this->layoutSuffix;
    }

    /**
     *
     * @param string $suffix
     * @return \Template
     */
    public function setLayoutSuffix($suffix)
    {
        $this->layoutSuffix = $suffix;
        return $this;
    }

    /**
     * Method to append the CSS stylesheets
     *
     * @return string
     */
    public function appendStylesheets()
    {
        if (!$this->cssStyles || !is_array($this->cssStyles)) {
            return;
        }
        foreach ($this->cssStyles as $css) {
            if (is_array($css)) {
                print '<link rel="stylesheet" href="' . $css[0] . '">' . "\n";
            } else {
                print '<link rel="stylesheet" href="' . $css . '">' . "\n";
            }
        }
    }

    /**
     * Method to append
     * @return string
     */
    public function appendJavascripts()
    {
        if (!$this->javascriptFiles || !is_array($this->javascriptFiles)) {
            return;
        }
        foreach ($this->javascriptFiles as $js) {
            if (is_array($js)) {
                print '<script type="text/javascript" src="' . $js[0] . '"></script>' . "\n";
            } else {
                print '<script type="text/javascript" src="' . $js . '"></script>' . "\n";
            }
        }
    }

}

function sanitize_output($buffer)
{
    $search = array(
        '/\>[^\S ]+/s', //strip whitespaces after tags, except space
        '/[^\S ]+\</s', //strip whitespaces before tags, except space
        '/(\s)+/s'  // shorten multiple whitespace sequences
    );
    $replace = array(
        '>',
        '<',
        '\\1'
    );
    $buffer = preg_replace($search, $replace, $buffer);
    return $buffer;
}
