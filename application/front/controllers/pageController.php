<?php

defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

use library\Escaper\Escaper;
use library\Captcha\Captcha;

class PageController extends BaseController
{

    const CAPTCHA_SESSION_KEY = 'kasoko_captcha';

    /**
     * Method to decorate the site homepage.
     * This will be called and executed rendered by the hand of the provided {$link}
     * @return void
     */
    public function index()
    {
        if (null === $page = Page::getBySlug($this->getArg())) {
            render('error404.phtml', array(__('unknown page')));
            exit();
        }
        $this->page = $page;
        $this->title = $page->title;
    }

    /**
     * Method to decorate a site page.
     * This will be called and executed rendered by the hand of the provided {$link}
     * @param string $link
     * @return void
     */
    public function view($slug)
    {
        if (null === $page = Page::getBySlug($this->getArg())) {
            render('error404.phtml', array(__('unknown page')));
            exit();
        }
        $this->page = $page;
        $this->title = $page->title;
        $this->content = $page->content;
    }

    public function contact()
    {
        $this->title = __('contact');
        $this->form_token = Security::token();

        if ($this->getRequest()->isPost()) {

            $contactData = $this->getRequest()->postParam('contact');
            if (false === Security::check($contactData['form_token'])) {
                throw new \Exception('Invalid token');
            }

            if (false === filter_var($contactData['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Invalid e-mail');
            }

            if (false === $this->checkCaptcha($contactData['captcha'])) {
                throw new \Exception('Invalid captcha');
            }

            $i18n = new I18n();
            $escaper = new Escaper;

            $email = $contactData['email'];
            $firstname = $escaper->escapeHtml($contactData['firstname']);
            $lastname = $escaper->escapeHtml($contactData['lastname']);
            $message = $escaper->escapeHtml($contactData['message']);

            $contactHtml = file_get_contents(APP_PATH . '/mails/' . $i18n->getLanguage() . '/contact.html');
            $contactFormHtml = file_get_contents(APP_PATH . '/mails/' . $i18n->getLanguage() . '/contact_form.html');
            $customer = AccountCustomer::getByEmail($email);
            if ($customer) {
                $search = array('{firstname}', '{lastname}', '{email}', '{message}', '{shop_logo}', '{shop_name}', '{shop_url}');
                $replace = array($customer->firstname, $customer->lastname, $customer->email, $message, \Configuration::get('shop_logo'), \Configuration::get('shop_name'), \Configuration::get('frontend_baseUrl'));
            } else {
                $search = array('{firstname}', '{lastname}', '{email}', '{message}', '{shop_logo}', '{shop_name}', '{shop_url}');
                $replace = array($firstname, $lastname, $email, $message, \Configuration::get('shop_logo'), \Configuration::get('shop_name'), \Configuration::get('frontend_baseUrl'));
            }
            $contactFormHtml = \str_replace($search, $replace, $contactFormHtml);
            $contactHtml = \str_replace($search, $replace, $contactHtml);

            $this->sendEmail($contactFormHtml);
            $this->sendEmail($contactHtml);
        }
    }

    /**
     * Check if the captcha is valid
     *
     * @param string $value
     * @return boolean
     */
    protected function checkCaptcha($value)
    {
        $excepted = $this->getSession()->get(self::CAPTCHA_SESSION_KEY);
        return Stringify::compareStrings($excepted, $value);
    }

    /**
     * Send email message
     *
     * @param string $content
     * @return boolean
     */
    protected function sendEmail($content)
    {
        $mail = new Mailer(true);
        try {
            $mail->AddAddress('endayiragije@gmail.com', 'Emmanuel Ndayiragije');
            $mail->SetFrom('noreply@kazinduzidev.com', 'Kasoko Dev');
            $mail->AddReplyTo('endayiragije@yahoo.fr', 'Emmanuel Ndayiragije');
            $mail->Subject = __('account.welcome');
            $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
            $mail->MsgHTML($content);
            return $mail->Send();
        } catch (phpmailerException $e) {
            echo $e->errorMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}
