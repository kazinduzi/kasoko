<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Response 
{
    
    private static $instance;    
    protected $headers = array();    
    protected $mime_types = array();    
    protected $output;

    public static function getInstance() 
    {
        if (empty(self::$instance)) {
            return self::$instance = new self();
        }
        return self::$instance;
    }
    
    public  function __construct() 
    {
        $this->mime_types = array(
                'text/html' => 'html',
                'application/xhtml+xml' => 'html',
                'application/xml' => 'xml',
                'text/xml' => 'xml',
                'text/javascript' => 'js',
                'application/javascript' => 'js',
                'application/x-javascript' => 'js',
                'application/json' => 'json',
                'text/x-json' => 'json',
                'application/rss+xml' => 'rss',
                'application/atom+xml' => 'atom',
                '*/*' => 'html',                
                'default' => 'html',
            );        
    }    
    
    public function setOutput($output) 
    {
        $this->output = $output;
        return $this;
    }
    
    public function getOutput() 
    {
        return $this->output;
    }
    
    public function headers() 
    {
        return $this->headers;
    }
    
    public function add_header($header) 
    {
        $this->headers[] = $header;
        return $this;
    }
}