<?php
// Thrift framework exports
//use Evernote;
//use EDAM\UserStore\UserStoreClient;
//use EDAM\NoteStore\NoteStoreClient;
//use EDAM\Types\Data, EDAM\Types\Note, EDAM\Types\Resource, EDAM\Types\ResourceAttributes;
//use EDAM\Error\EDAMSystemException, EDAM\Error\EDAMUserException, EDAM\Error\EDAMErrorCode;

/* Evernote for KISSCMS */
class Evernote {

	private $api;
	public $oauth;
	private $config;
	private $creds;
	private $store;
	private $cache;

	function  __construct() {
		// main URL
		$this->api = EVERNOTE_SERVER;

		$this->config = $GLOBALS['config']['evernote'];

		// load all the necessery subclasses
		$this->init();

		return $this;
	}

	// check if we have a valid login
	public function login(){

		// get/update the creds
		$this->creds = $this->oauth->creds();

		$valid = ( !empty($this->creds) && empty($this->creds['oauth_callback_confirmed']) );

		// return the state
		return $valid;

	}

	function init(){
		// load all the necessery subclasses
		$this->oauth = new Evernote_OAuth();

		// get/update the creds
		$this->creds = $this->oauth->creds();
		// create client
		$token = ( is_array($this->creds) && array_key_exists('oauth_token', $this->creds) ) ? $this->creds['oauth_token'] : false;
		$this->client = ( $token ) ? new Evernote\Client( array('token' => $token, 'serviceHost' => $this->config['host']) ) : false;

	}

	function me(){
		// prerequisites
		if( !$this->client ) return false;
		$token = $this->creds['oauth_token'];
		$user = $this->getUserStore();
		return (array) $user->getUser($token);
	}

	// REST methods
	function get( $type="", $params=array() ){
		// exit now if there are no credentials
		if( empty( $this->creds ) ) return false;

		// check cache before....
		//...
		switch( $type ){
			case "notebooks":
				$results = $this->getNotebooks($params);
			break;
			case "notes":
				$results = $this->getNotes($params);
			break;
			case "resource":
				$results = $this->getResource($params);
			break;

		}

		return $results;

	}

	function post() {
		// exit now if there are no credentials
		if( empty( $this->creds ) ) return false;

		//...
		switch( $type ){
			case "notebook":
				$results = $this->postNotebook($params);
			break;
			case "note":
				$results = $this->postNote($params);
			break;

		}

		return $results;

	}

	function put() {
		// exit now if there are no credentials
		if( empty( $this->creds ) ) return false;

	}

	function delete() {
		// exit now if there are no credentials
		if( empty( $this->creds ) ) return false;

	}

	// Stores
	function store(){
		// a switch/case...
	}

	function getNoteStore(){
		// check cache first?
		//$_SESSION['cache']['evernote']['note']['store']
		if( empty($_SESSION['cache_evernote_note_store']) ){

			$store = $this->client->getNoteStore();
			// save to session
			$_SESSION['cache_evernote_note_store'] = $store;

		} else {
			$store = $_SESSION['cache_evernote_note_store'];
		}

		return $store;

	}

	// get the logged in user object
	function getUserStore(){
		// check cache first?
		//$_SESSION['cache']['evernote']['user']['store']
		if( empty($_SESSION['cache_evernote_user_store']) ){

			$store = $this->client->getUserStore();
			// save to session
			$_SESSION['cache_evernote_user_store'] = $store;

		} else {
			$store = $_SESSION['cache_evernote_user_store'];
		}

		return $store;

	}

	// Helpers
	function getNotebooks($params) {
		// prerequisites
		if( !$this->client ) return;
		// params
		//$token = $this->creds['oauth_token'];
		//$user = $this->getUserStore();

		//$noteStoreUrl = $user->getNoteStoreUrl($token);
		//$parts = parse_url($noteStoreUrl);

		//$client = new Evernote\Client(array('token' => $token, 'serviceHost' => $this->config['host']));
		$store = $this->getNoteStore();
		$result = array();

		try {

			$notebooks = $store->listNotebooks();

			$result = array();
			if (!empty($notebooks)) {
				foreach ($notebooks as $notebook) {
					$result[] = array(
						"guid" => $notebook->guid,
						"name" => $notebook->name
					);
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
			//var_dump( 'Error listing notebooks: ' . $e->getMessage() );
		}
		return $result;
	}

	function getNotes($params){

		// prerequisites
		if( !$this->client ) return;
		// variables
		$results = array();

		// default params
		$defaults = array(
				'resources' => false,
				'recognition' => false,
				'alternateData' => false
		);

		try {

			$token = $this->creds['oauth_token'];
			//$user = $this->getUserStore();

			//$noteStoreUrl = $user->getNoteStoreUrl($token);
			//$parts = parse_url($noteStoreUrl);

			//$client = new Evernote\Client(array('token' => $token, 'serviceHost' => $this->config['host']));
			$store = $this->getNoteStore();

			$filter = new \EDAM\NoteStore\NoteFilter();
			$filter->notebookGuid = $params['guid'];

			// merge given params with defaults
			$options = array_merge($defaults,  $params);

			$notes = $store->findNotes($token, $filter, 0, 100); // Fetch up to 100 notes
			if (!empty($notes->notes)) {
					  foreach ($notes->notes as $note) {
							  // findNotes gets note metadata, but not the actual content
							  // To get the content, we load the note itself (with content but no attached resources)
							  $result = (array) $store->getNote($token, $note->guid, true, $options['resources'], $options['recognition'], $options['alternateData']);
							  // get the media resoutces for the note
							  $resources = $result['resources'];
							  /*
							  if (!empty($resources)) {
										foreach ($resources as $k=>$resource) {
												$media = array();
												// get the raw binary attachment, which could be an image, audio file, etc
												$media['bytes'] = $resource->data->body;
												echo base64_decode($media['bytes']);
												$media['type'] = $resource->mime;
												// add to the results list
												$result['resources'][$k] = $media;
										}
								}
								*/
								$results[] = $result;

					  }
			  }

			 return $results;

		 } catch (Exception $e) {
			//var_dump( 'Error listing notebooks: ' . $e->getMessage() );
		}

		return false;
	}

	// return a specific resource
	function getResource($params){
		// prerequisites
		if( !$this->client ) return;

		try {

			//$token = $this->creds['oauth_token'];
			//$user = $this->getUserStore();

			//$noteStoreUrl = $user->getNoteStoreUrl($token);
			//$parts = parse_url($noteStoreUrl);

			//$client = new Evernote\Client(array('token' => $token, 'serviceHost' => $this->config['host']));
			$store = $this->getNoteStore();

			$resource = $store->getResource($token, $params['guid'], true, false, false, false);

			return $resource;

		 } catch (Exception $e) {
			//var_dump( 'Error getting resource: ' . $e->getMessage() );
		}

		return false;

	}


	function postNotebook( $params ){

	}

	// NOT TESTED!
	function postNote( $params ){

		// To create a new note, simply create a new Note object and fill in
		// attributes such as the note's title.
		$note = new Note();
		$note->title = $params['title'];

		// To include an attachment such as an image in a note, first create a Resource
		// for the attachment. At a minimum, the Resource contains the binary attachment
		// data, an MD5 hash of the binary data, and the attachment MIME type. It can also
		// include attributes such as filename and location.
		$filename = $params['file'];
		$image = fread(fopen($filename, "rb"), filesize($filename));
		$hash = md5($image, 1);

		$data = new Data();
		$data->size = strlen($image);
		$data->bodyHash = $hash;
		$data->body = $image;

		// The content of an Evernote note is represented using Evernote Markup Language
		// (ENML). The full ENML specification can be found in the Evernote API Overview
		// at http://dev.evernote.com/documentation/cloud/chapters/ENML.php
		$note->content =
		  '<?xml version="1.0" encoding="UTF-8"?>' .
		  '<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">' .
		  '<en-note>Here is the content:<br/>';

		// When note titles are user-generated, it's important to validate them
		$len = strlen($note->title);
		$min = $GLOBALS['EDAM_Limits_Limits_CONSTANTS']['EDAM_NOTE_TITLE_LEN_MIN'];
		$max = $GLOBALS['EDAM_Limits_Limits_CONSTANTS']['EDAM_NOTE_TITLE_LEN_MAX'];
		$pattern = '#' . $GLOBALS['EDAM_Limits_Limits_CONSTANTS']['EDAM_NOTE_TITLE_REGEX'] . '#'; // Add PCRE delimiters
		if ($len < $min || $len > $max || !preg_match($pattern, $note->title)) {
		  print "\nInvalid note title: " . $note->title . '\n\n';
		  exit(1);
		}

		if($params['resources']){

			foreach( $params['resources'] as $params ){
				$resource = $this->postResource( $params );
				// Now, add the new Resource to the note's list of resources
				$note->resources = array( $resource );

				$note->content .= '<en-media type="image/png" hash="' . $hashHex . '"/>';
			}

		}
		// To display the Resource as part of the note's content, include an <en-media>
		// tag in the note's ENML content. The en-media tag identifies the corresponding
		// Resource using the MD5 hash.
		$hashHex = md5($resource, 0);

		// complete the note...
		$note->content .= '</en-note>';

		// Finally, send the new note to Evernote using the createNote method
		// The new Note object that is returned will contain server-generated
		// attributes such as the new note's unique GUID.
		$createdNote = $noteStore->createNote($authToken, $note);

		// send back the guid
		return $createdNote->guid;

	}

	// NOT TESTED!
	// resource is posted as part of a note...
	function postResource( $params ){

		// if there is no file, exit now
		if( empty( $params['file'] ) ) return;

		$resource = new Resource();
		$resource->mime = $params['mime']; //ex. "image/png"
		$resource->data = $data;
		$resource->attributes = new ResourceAttributes();
		$resource->attributes->fileName = $filename;

		return $resource;
	}


	// A 'specialized' exception handler for our program so that error messages all go to the console
	function en_exception_handler($exception) {
		echo "Uncaught " . get_class($exception) . ":\n";
		if ($exception instanceof EDAMUserException) {
			echo "Error code: " . EDAMErrorCode::$__names[$exception->errorCode] . "\n";
			echo "Parameter: " . $exception->parameter . "\n";
		} else if ($exception instanceof EDAMSystemException) {
			echo "Error code: " . EDAMErrorCode::$__names[$exception->errorCode] . "\n";
			echo "Message: " . $exception->message . "\n";
		} else {
			echo $exception;
		}
	}
	// Usage: set_exception_handler('en_exception_handler');

}

?>