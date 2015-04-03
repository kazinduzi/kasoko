<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

use library\Captcha\Captcha;

class CaptchaController extends BaseController
{

    const CAPTCHA_SESSION_KEY = 'kasoko_captcha';

    public function index()
    {
	$captcha = new Captcha;
	$captcha->sessionVar = self::CAPTCHA_SESSION_KEY;
	$captcha->imageFormat = 'png';
	$captcha->lineWidth = 2;
	$captcha->scale = 3;
	$captcha->blur = true;
	// Image generation
	$captcha->createImage();
	die();
    }

}
