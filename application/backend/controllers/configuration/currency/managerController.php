<?php

use library\Currency;

class ConfigurationCurrencyManagerController extends Admin_controller
{

    public function __construct(\Request $req = null, \Response $res = null)
    {
	parent::__construct($req, $res);
	$this->Template->setViewSuffix('phtml');
    }

    public function index()
    {
	$currency = new Currency();
	$this->Template->setFilename('configuration/currencies');
	$this->title = __('configuration');
	$this->currencies = $currency->findAll();
    }

    public function updateRate()
    {
	// http://rate-exchange.appspot.com/currency?from=USD&to=BIF
	// https://gist.github.com/Fluidbyte/2973986
	if ($this->Request->isXmlHttpRequest() && \Security::check($this->Request->getParam('token'))) {
	    foreach (Currency::getInstance()->findAll() as $currency) {
		$data = $this->fetchExchangeRateByCode($currency);
		if (is_object($data) && $data->to === $currency->getCode()) {
		    $currency->getDbo()->autocommit(false);
		    try {
			$currency->getDbo()->setQuery('UPDATE `currency` SET `rate` = \'' . $data->rate . '\', `date_updated` = now() WHERE `code` = \'' . $data->to . '\';');
			$currency->getDbo()->execute();
			$currency->getDbo()->commit();
		    } catch (\Exception $e) {
			$currency->getDbo() - rollback();
			print_r($e);
		    }
		}
	    }
	    die(json_encode(array('success' => true)));
	} else {
	    throw new \Exception('Unsupported request');
	}
    }

    /**
     * Fetch the exchange rate from http://rate-exchange.appspot.com/currency?from=USD&to=BIF
     * 
     * @param \library\Currency $currency
     * @return stdObject
     */
    protected function fetchExchangeRateByCode(Currency $currency)
    {
	$exchangeRateUrl = 'http://rate-exchange.appspot.com/currency?from=' . Currency::getInstance()->getDefaultCurrency()->getCode() . '&to=' . $currency->getCode();
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $exchangeRateUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$exchangeRate = curl_exec($ch);
	curl_close($ch);
	$data = json_decode($exchangeRate);
	if ($data->to && $data->from && $data->rate) {
	    return $data;
	}
	return;
    }

}
