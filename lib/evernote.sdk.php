<?php 

// Include the Evernote API from the lib subdirectory. 
// $evernote_sdk simply contains the contents of /php/lib from the Evernote API SDK

$evernote_sdk = SDK. "evernote/1.22/";

require_once($evernote_sdk ."autoload.php");
require_once($evernote_sdk ."Thrift.php");
require_once($evernote_sdk ."transport/TTransport.php");
require_once($evernote_sdk ."transport/THttpClient.php");
require_once($evernote_sdk ."protocol/TProtocol.php");
require_once($evernote_sdk ."protocol/TBinaryProtocol.php");
require_once($evernote_sdk ."packages/Errors/Errors_types.php");
require_once($evernote_sdk ."packages/Types/Types_types.php");
require_once($evernote_sdk ."packages/UserStore/UserStore.php");
require_once($evernote_sdk ."packages/UserStore/UserStore_constants.php");
require_once($evernote_sdk ."packages/NoteStore/NoteStore.php");
require_once($evernote_sdk ."packages/Limits/Limits_constants.php");


// Import the classes that we're going to be using
#use EDAM\UserStore\UserStoreClient;
#use EDAM\NoteStore\NoteStoreClient;
#use EDAM\Types\Data, EDAM\Types\Note, EDAM\Types\Resource, EDAM\Types\ResourceAttributes;
#use EDAM\Error\EDAMSystemException, EDAM\Error\EDAMUserException, EDAM\Error\EDAMErrorCode;


?>