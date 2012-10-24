<?php


//===============================================
// Configuration
//===============================================

if( class_exists('Config') && method_exists(new Config(),'register')){ 

	// Register variables
	// - Client credentials
	Config::register("evernote", "key", 		"01234567890");
	Config::register("evernote", "secret", 		"012345678901234567890123456789");
	// Replace this value with https://www.evernote.com to use Evernote's production server
	Config::register("evernote", "server", 		"https://sandbox.evernote.com");
	// Replace this value with www.evernote.com to use Evernote's production server
	Config::register("evernote", "host", 		"sandbox.evernote.com");
	Config::register("evernote", "port", 		"443");
	Config::register("evernote", "protocol", 	"https");
	
  
	// Definitions
	define('EVERNOTE_SERVER', 					$GLOBALS['config']['evernote']['server']);
	define('NOTESTORE_HOST', 					$GLOBALS['config']['evernote']['host']);
	define('NOTESTORE_PORT', 					$GLOBALS['config']['evernote']['port']);
	define('NOTESTORE_PROTOCOL', 			$GLOBALS['config']['evernote']['protocol']);  

}

?>