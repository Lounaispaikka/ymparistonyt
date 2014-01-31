<?php

require_once('../server/config.php');

global $Site, $Session, $Language, $User, $Session, $infomsg, $errormsg;

try {

	if ( !isset($_REQUEST['nosession']) ) $Session = new \Lougis_session();
	
	//Parse the frontModule, actionName and _REQUEST array form _SERVER['REQUEST_URI']
	if ( isset($_REQUEST['frontend']) ) {
		$path = '/run/'.$_REQUEST['frontend'];	
		$path = substr($path, 1, strlen($path)-2);
	} else {
		$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);	
		$path = substr($path, 1, strlen($path)-2);	
	}
	$pathArray = explode('/', $path);
	if ( $pathArray[0] != 'run' ) throw new Exception("Run missing from module path.");
	array_shift($pathArray);
	
	$actionName = array_pop($pathArray);
	$frontModule = "../server/frontend/".implode('/', $pathArray).".php";
	$frontClass = "\\Lougis\\frontend\\".implode('\\', $pathArray);
	parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $_GET);
	$_REQUEST = array_merge($_GET, $_POST);
	
	if ( isset($_REQUEST['rundebug']) ) {
		print_r_html($_REQUEST);
		print_r_html($_SERVER);
		die;
	}
	
	//$Session = new Lougis_user_session();
	
	$Site = new \Lougis_site( $_SESSION['site_id'] );
	//$Language = new Lougis_language($_SESSION['lang_id']);
	if ( !empty($_SESSION['user_id']) ) $User = new Lougis_user($_SESSION['user_id']);
	
	header('Cache-Control: public');
	
	//print_r_html($frontModule);die;
	
	if ( !file_exists($frontModule) ) throw new Exception("Module does not exist.");
	require_once($frontModule);
	
	$Front = new $frontClass;
	if ( !method_exists($Front, $actionName) ) throw new Exception($frontClass."::".$actionName."() method does not exist");
	call_user_func( array($Front, $actionName) );

} catch (Exception $e) {

	die($e->getMessage());

} //end try-catch

?>