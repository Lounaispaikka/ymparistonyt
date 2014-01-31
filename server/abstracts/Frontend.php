<?php
namespace Lougis\abstracts;
abstract class Frontend {

	public function __construct() {
	
	}
	
	public function htmlHeader() {
		header('Content-type: text/html');
	}
	
	public function jsonHeader( $Cache = true ) {
		
		if ( !$Cache ) {
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		}
		header('Content-type: application/json');
		
	}
	
	public function jsonOut( $Data ) {
		
		$this->jsonHeader();
		echo json_encode($Data);
		
	}
	
	public function jsonHtmlOut( $Data ) {
		
		header('Content-type: text/html');
		echo json_encode($Data);
		
	}
	/*
	public function objectUpdate() {
		
		global $User;
		
		try {
			
			if ( !$User->isAdminLogged() ) throw new \Exception("Virheellinen kirjautuminen. Tarkista oletko kirjautunut hallintaan");
			
			$
			
			$res = array(
				"success" => true,
				"object" => null
			);
			
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	*/
}
?>