<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * Kazinduzi Framework (http://framework.kazinduzi.com/)
 *
 * @author    Emmanuel Ndayiragije <endayiragije@gmail.com>
 * @link      http://kazinduzi.com
 * @copyright Copyright (c) 2010-2013 Kazinduzi. (http://www.kazinduzi.com)
 * @license   http://kazinduzi.com/page/license MIT License
 * @package   Kazinduzi
 */
class Dispatcher
{

    public function dispatch()
    {
        $Front = FrontController::getInstance();
        $Front->loadController();
        if ($Front->getCallableController() instanceof Controller) {
            $Front->getCallableController()->before();
            $Front->getCallableController()->run();
            $Front->getCallableController()->after();
        }
    }

}
