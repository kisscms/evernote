<?php
// Thrift framework exports
use EDAM\UserStore\UserStoreClient;
use EDAM\NoteStore\NoteStoreClient;
use EDAM\Types\Data, EDAM\Types\Note, EDAM\Types\Resource, EDAM\Types\ResourceAttributes;
use EDAM\Error\EDAMSystemException, EDAM\Error\EDAMUserException, EDAM\Error\EDAMErrorCode;

/* Evernote for KISSCMS */
class Evernote {
	
	private $api;
	private $oauth;
	private $config;
	private $creds;
	private $store;
	private $cache;
	
	function  __construct() {
		// main URL
		$this->api = EVERNOTE_SERVER;
		
		// load all the necessery subclasses
		$this->oauth = new Evernote_OAuth();
		
		$this->config = $GLOBALS['config']['evernote'];
		
		
		// get/update the creds
		$this->creds = $this->oauth->creds();
		
		//var_dump( $this->creds);
		
		$userClient = new THttpClient($this->config['host'], $this->config['port'], "/edam/user", $this->config['protocol']);
		$userStoreProtocol = new TBinaryProtocol($userClient);
		$userStore = new UserStoreClient($userStoreProtocol, $userStoreProtocol);
		
		$noteStoreUrl = $userStore->getNoteStoreUrl($this->creds['oauth_token']);
		$parts = parse_url($noteStoreUrl);
		
		$client = new THttpClient($this->config['host'], $this->config['port'], "/shard/s1/notestore", $this->config['protocol']);
		$protocol = new TBinaryProtocol($client);
		$this->store = new NoteStoreClient($protocol, $protocol);
		
	}
	
	// REST methods
	function  get( $service="", $params=array() ){
		
		// check cache before....
		//...
		
		$url = $this->api . $service .".json";
		
		$results = $this->oauth->request($url, 'GET', $params);
		
		return json_decode( $results );
		
	}
	
	function listNotebooks() {
		// get token
		$token = $this->creds['oauth_token'];
		var_dump( $this->store->listNotebooks( $token ) );
		
		try {
			
		  $notebooks = $this->store->listNotebooks( $token );
		  $result = array();
		  if (!empty($notebooks)) {
			foreach ($notebooks as $notebook) {
			  $result[] = $notebook->name;
			}
		  }
		  var_dump( $result );
			//Successfully listed content owner\'s notebooks
			return TRUE;
		} catch (EDAMSystemException $e) {
			var_dump( $e->getMessage() );
		} catch (EDAMUserException $e) {
		  var_dump( $e->getMessage() );
		} catch (EDAMNotFoundException $e) {
		  var_dump( $e->getMessage() );
		} catch (Exception $e) {
		  var_dump( 'Error listing notebooks: ' . $e->getMessage() );
		}
		return FALSE;
	}
  
	function  post() {
		
	}
	
	function  put() {
		
	}
	
	function  delete() {
		
	}
	
	
}

?>