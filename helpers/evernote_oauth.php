<?php
// FIX - to include the base OAuth lib not in alphabetical order
require_once( APP . "plugins/oauth/helpers/kiss_oauth.php" );

/* Evernote for KISSCMS */
class Evernote_OAuth extends KISS_OAuth_v1 {
	
	function  __construct( $api="evernote", $url=EVERNOTE_SERVER ) {
		
		$this->url = array(
			'authorize' 		=> $url ."/OAuth.action", 
			'request_token' 	=> $url ."/oauth", 
			'access_token' 		=> $url ."/oauth", 
		);
		
		parent::__construct( $api, $url );
		
	}
	
	function save( $response ){
		//...
		// save to the user session 
		$_SESSION['oauth']['evernote'] = $response;
		
	}
	
}