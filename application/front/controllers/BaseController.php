<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

class BaseController extends Controller
{

    /**
     * Constants
     */
    const MODE_LIST = 'list';
    const MODE_GRID = 'grid';

    /**
     * View mode
     * @var array
     */
    protected $modeView = array(
        self::MODE_GRID,
        self::MODE_LIST
    );

    /**
     * Session instance
     *
     * @var \Session
     */
    protected $session;

    /**
     * Constructor
     *
     * @param Request $req
     * @param Response $res
     */
    public function __construct(Request $req = null, Response $res = null)
    {
        parent::__construct($req, $res);
        $this->Template->setTheme(\Theme::getByName('default'));
        $this->Template->setLayout('index');
        $this->mode = $this->getMode();
    }

    /**
     * Get the view mode
     *
     * @return string
     */
    protected function getMode()
    {
        if (null !== $mode = $this->getRequest()->getParam('mode')) {
            $mode = escapeHtml($mode);
            $mode === $this->getSession()->get('mode') ?: $this->getSession()->set('mode', escapeHtml($mode));
        }
        return $this->getSession()->get('mode') ?: self::MODE_GRID;
    }

    /**
     * Get the session object
     *
     * @return \Session
     */
    public function getSession()
    {
        if (null === $this->session) {
            $this->session = Kazinduzi::session();
        }
        return $this->session;
    }

    /**
     * Dummy index action
     *
     */
    public function index()
    {
        return;
    }

    /**
     * Add specific JS files
     *
     * @param array $jsFiles
     * @return \ProductController
     */
    protected function addJsFiles(array $jsFiles)
    {
        $this->javascriptFiles = array_unique($jsFiles);
        return $this;
    }

    /**
     * Add specific CSS files
     *
     * @param array $styles
     * @return \ProductController
     */
    protected function addCssFiles(array $styles)
    {
        $this->cssStyles = array_unique($styles);
        return $this;
    }

}
