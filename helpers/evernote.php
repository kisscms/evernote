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
		
	}
	
	// get the logged in user object
	function  me(){
		// check cache first?
		//$_SESSION['cache']['evernote']['user']['store']
		if( empty($_SESSION['cache_evernote_user_store']) ){
			
			var_dump($this->config['host']);
			
			$client = new THttpClient($this->config['host'], $this->config['port'], "/edam/user", $this->config['protocol']);
			$protocol = new TBinaryProtocol($client);
			$store = new UserStoreClient($protocol, $protocol);
			// save to session
			$_SESSION['cache_evernote_user_store'] = $store;
			
		} else {
			$store = $_SESSION['cache_evernote_user_store'];
		}
		
		return $store;
		
	}
	
	// REST methods
	function  get( $type="", $params=array() ){
		
		// check cache before....
		//...
		switch( $type ){
			case "notebooks":
				$results = $this->getNotebooks($params);
			break;
			case "notes":
				$results = $this->getNotes($params);
			break;
			
		}
		
		return $results;
		
	}

	function  post() {
		
	}
	
	function  put() {
		
	}
	
	function  delete() {
		
	}
	
	
	// Helpers
	function getNotebooks($params) {
		
		// params
		$token = $this->creds['oauth_token'];
		$user = $this->me();
		
		$noteStoreUrl = $user->getNoteStoreUrl($token);
		$parts = parse_url($noteStoreUrl);
		
		$client = new THttpClient($this->config['host'], $this->config['port'], $parts['path'], $this->config['protocol']);
		$protocol = new TBinaryProtocol($client);
		$store = new NoteStoreClient($protocol, $protocol);
		
		try {
			
		  $notebooks = $store->listNotebooks( $token );
		  $result = array();
		  if (!empty($notebooks)) {
			foreach ($notebooks as $notebook) {
				$result[] = array( "guid" => $notebook->guid, 
			  							"name" => $notebook->name); 
			}
		  }
			//Successfully listed content owner\'s notebooks
			return $result;
		} catch (EDAMSystemException $e) {
			var_dump( $e->getMessage() );
		} catch (EDAMUserException $e) {
		  var_dump( $e->getMessage() );
		} catch (EDAMNotFoundException $e) {
		  var_dump( $e->getMessage() );
		} catch (Exception $e) {
		  var_dump( 'Error listing notebooks: ' . $e->getMessage() );
		}
		return false;
	}
  
	function getNotes($params){
		
		// params
		$results = array();
		
		try {
				
			$token = $this->creds['oauth_token'];
			$user = $this->me();
			
			$noteStoreUrl = $user->getNoteStoreUrl($token);
			$parts = parse_url($noteStoreUrl);
			
			$client = new THttpClient($this->config['host'], $this->config['port'], $parts['path'], $this->config['protocol']);
			$protocol = new TBinaryProtocol($client);
			$store = new NoteStoreClient($protocol, $protocol);
			
			$filter = new \EDAM\NoteStore\NoteFilter();
			$filter->notebookGuid = $params['guid'];
			
			$notes = $store->findNotes($token, $filter, 0, 100); // Fetch up to 100 notes
			  if (!empty($notes->notes)) {
					  foreach ($notes->notes as $note) {
							  // findNotes gets note metadata, but not the actual content
							  // To get the content, we load the note itself (but not attached resources in this example)
							  $result = $store->getNote($token, $note->guid, true, true, false, false);
							  // get the media resoutces for the note
							  $resources = $result->resources;
								if (!empty($resources)) {
										foreach ($resources as $resource) {
												$media = array();
												// get the raw binary attachment, which could be an image, audio file, etc
												$media['bytes'] = $resource->data->body;
												$media['type'] = $resource->mime;
												// add to the results list
												$result['resources'][] = $media;
										}
								}
								$results[] = $result;
					  }
			  }
			 
			 return $results; 
			
		 } catch (Exception $e) {
			var_dump( 'Error listing notebooks: ' . $e->getMessage() );
		}
		 
		return false;
	}
	
}

?>