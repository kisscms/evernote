<?php

// load the data of a file or user...
class Resource extends Controller {

	function __construct($controller_path,$web_folder,$default_controller,$default_function)  {

		// main objects
		$this->api['evernote'] = new Evernote();

		// continue to the default setup
		parent::__construct($controller_path,$web_folder,$default_controller,$default_function);

	}

	function index( $params ) {

		// pickup the page id from the params
		$id = ( is_array($params) ) ? $params[0] : $params;
		// get the data for the resource
		$this->data = $this->api['evernote']->get("resource", array( "guid" => $id) );
		// render the content
		$this->render();

	}

	function render(){
		if( !$this->data ) exit;
		header('content-type: '. $this->data->mime);
		echo $this->data->data->body;
		exit;

	}

}

?>