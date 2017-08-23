<?php
namespace library\Plugin;

use Exception;

/**
 * Plugin View class for MVC pattern
 *
 * @author Emmanuel Ndayiragije <endayiragije@gmail.com>
 */
class View implements ViewInterface
{
	private $template = '';
	protected $module;
	protected $templateVars = [];
	protected $viewsDir = [];

    /**
     * Constructor
	 * 
     * @param string $template
     * @param array $templateVars
     */
    public function __construct($template = '', array $templateVars = []) 
    {		
		$this->template = $template;		
		
        if (!empty($templateVars)) {
            $this->templateVars += $templateVars;
        }
    }
	
	/**
	 * Set the module
	 * 
	 * @param mixed $module
	 * @return \framework\Plugin\View
	 */
	public function setPlugin($module)
	{
		$this->module = $module;
		return $this;
	}
	
	/**
	 * Get the module
	 * 
	 * @return mixed
	 */
	public function getPlugin()
	{
		return $this->module;
	}

	/**
	 * Find template filepath
	 * 
	 * @return string
	 */
	protected function findTemplateFilepath()
	{
		foreach ($this->viewsDir as $dir) {
			foreach (['.phtml', '.twig', '.php', '.tpl'] as $ext) {
				$templateFilepath = $dir . DS . $this->template.$ext;
				if (file_exists($templateFilepath)) {
					return $templateFilepath;
				}
			}			
		}
	}

	/**
	 * Add Views Directory
	 * 
	 * @param string $directory
	 * @return \framework\Plugin\View
	 */
	public function addViewsDirectory($directory) 
	{
		if ( ! in_array($directory, $this->viewsDir)) {
			$this->viewsDir[] = $directory;
		}
		return $this;
	}

	/**
     * Returns the template filepath.
     *
     * @return string
     */
    public function getTemplateFile()
    {
        $templateFilepath = $this->findTemplateFilepath();
        if (!is_file($templateFilepath) || !is_readable($templateFilepath)) {
            throw new \InvalidArgumentException("The template '$templateFilepath' is invalid.");
        }
		return $templateFilepath;
    }
	
	/**
     * Returns the variables to bind to the template when rendering.
     *
     * @param array $override Template variable override values. Mainly useful
	 *  when including View templates in other templates.
     * @return array
     */
    public function getTemplateVars(array $override = [])
    {
        return $override + $this->templateVars;
    }

    /**
     *
     * @param type $key
     * @param type $value
     * @return \View
     */
    public function set($key, $value) 
    {
        $this->__set($key, $value);
        return $this;
    }

    /**
     *
     * @param type $key
     * @return type
     */
    public function get($key) 
    {
        return $this->__get($key);
    }

    /**
     *
     * @param type $name
     * @param type $value
     * @return \View
     */
    public function __set($name, $value) 
    {
        $this->templateVars[$name] = $value;
        return $this;
    }

    /**
     *
     * @param type $name
     * @return type
     * @throws \InvalidArgumentException
     */
    public function __get($name) 
    {
        if ( ! isset($this->templateVars[$name])) {
            return null;
        }
        $context = $this->templateVars[$name];
        return $context instanceof \Closure ? $context($this) : $context;
    }

    /**
     *
     * @param type $name
     * @return type
     */
    public function __isset($name) 
    {
        return isset($this->templateVars[$name]);
    }

    /**
     *
     * @param type $name     
     * @throws \InvalidArgumentException
     */
    public function __unset($name) 
    {
        if (!isset($this->templateVars[$name])) {
            throw new \InvalidArgumentException("Unable to unset the key '$name'.");
        }
        unset($this->templateVars[$name]);        
    }
	
	/**
     * Assign value to a variable for use in a template
     * @param string|array $var
     * @param mixed $value
     * @ignore
     */
    public function assign($var, $value = null)
    {
        if (is_string($var)) {
            $this->$var = $value;
        } elseif (is_array($var)) {
            foreach ($var as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    /**
	 * Get the evaluated contents of the view.
	 * 
     * @return string     
	 */
    public function fetch() 
    {
		$obLevel = ob_get_level();
		
        ob_start();
		
		extract($this->templateVars, EXTR_SKIP | EXTR_REFS);
		
        try	{
            require $this->getTemplateFile();
        } catch (Exception $e) {
            $this->handleViewException($e, $obLevel);
        } catch (Throwable $e) {
            $this->handleViewException(new FatalThrowableError($e), $obLevel);
        }
		
        return ob_get_clean();
    }
	
	/**
	 * 
	 * @param Exception $e
	 * @param integer $obLevel
	 * @throws Exception
	 */
	protected function handleViewException(Exception $e, $obLevel)
    {
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }
        throw $e;
    }
	
	/**
	 * 
	 * @return string
	 */
	public function render()
	{
		return $this->fetch();
	}
}
