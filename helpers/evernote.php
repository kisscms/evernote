<?php
/* Evernote for KISSCMS */
class Evernote {
	
	private $api;
	private $oauth;
	private $config;
	private $creds;
	private $cache;
	
	function  __construct() {
		// main URL
		$this->api = EVERNOTE_SERVER;
		
		// load all the necessery subclasses
		$this->oauth = new Evernote_OAuth();
		
		$this->config = $GLOBALS['config']['evernote'];
		// get/update the creds
		$this->creds = $this->oauth->creds();
		
	}
	
	// REST methods
	function  get( $service="", $params=array() ){
		
		// check cache before....
		//...
		
		$url = $this->api . $service .".json";
		
		$results = $this->oauth->request($url, 'GET', $params);
		
		return json_decode( $results );
		
	}
	
	
	function  post() {
		
	}
	
	function  put() {
		
	}
	
	function  delete() {
		
	}
	
	
}

?>