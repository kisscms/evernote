<?php

// load the data of a file or user...
class Main extends KISS_Auth {
	
	function __construct($controller_path,$web_folder,$default_controller,$default_function)  {
		
		// main objects
		$this->api['evernote'] = new Evernote();
		
		// continue to the default setup
		parent::__construct($controller_path,$web_folder,$default_controller,$default_function);
		
	}
	
	function index( $params ) {
		
		// check if we have a logged in state
		if( $this->api['evernote']->login() ){
			
			$this->data["body"][]['notebooks'] = $this->api['evernote']->get("notebooks");
			
		}
		
		$this->render();
		
	}
	
	function credentials( $params ){
		
		$this->data['template'] = "credentials.php";
		
		$this->render();
		
	}
	
}

?>