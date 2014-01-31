<?
/*
if ( !isset($_SESSION["start"]) ) {
	
	define("SESSION_NAME", "aluetietopalvelu_dev");
	
	ini_set ("session.use_cookies",1);
	ini_set ("session.gc_maxlifetime", 3600*24);
	ini_set ("session.name", SESSION_NAME);
	ini_set("arg_separator.output", "&amp;"); 

	if ( session_start() ) {
		$session_test = true;
	} else {
		$session_test = false;
	} //end if-else
	
	if ( !$_SESSION["start"] && empty($_COOKIE[SESSION_NAME]) ) {
		session_destroy();
		ini_set ("session.use_trans_sid ",1);
		session_start();
		$_SESSION["sid"] = session_id();
		$_SESSION["start"] = true;
	} else {
		$_SESSION["sid"] = $_COOKIE[SESSION_NAME];
		$_SESSION["start"] = true;
	} //end if

} //end if
*/
?>
