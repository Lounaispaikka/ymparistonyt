#!/usr/bin/php
<?php
require_once('../server/config.php');

$Usr = new \Lougis_user();

$TA = file('toimenpiteet_ja_asiantuntijat.csv');

for($i=1; $i < count($TA); $i++) {

	$Roa = str_getcsv($TA[$i]);
	$Tid = intval($Roa[0]);
	$Virkamiehet = explode(',', str_replace(' ', '', trim($Roa[3])));
	$Admins = array();
	$Admins[] = str_replace(' ', '', trim($Roa[4]));
	$Spare = str_replace(' ', '', trim($Roa[5]));
	if ( !empty($Spare) ) $Admins[] = $Spare;
	
	$Asiantuntijat = array();
	foreach($Virkamiehet as $email) {
		$Ea = explode('@', $email);
		$user = $Ea[0];
		$domain = $Ea[1];
		$Ua = explode('.', $user);
		$First = ucwords($Ua[0]);
		$Last = ucwords($Ua[1]);
		$Da = explode('.', $domain);
		
		$Usr = new \Lougis_user();
		$Usr->email = $email;
		$Count = $Usr->count();
		if ( $Count == 0 ) {
			$Usr->firstname = $First;
			$Usr->lastname = $Last;
			$Usr->date_created = date(DATE_W3C);
			$Usr->password = \Lougis_user::hashPasswd($Usr->firstname.'2012nyt');
			$Usr->save();
			$Ug = new \Lougis_group_user();
			$Ug->group_id = 4;
			$Ug->user_id = $Usr->id;
			$Ug->group_admin = false;
			$Ug->date_added = date(DATE_W3C);
			$Ug->save();
			echo $Usr->email." added as user ".$Usr->id."\r\n";
		} else {
			echo $Usr->email." already exists\r\n";
		}
		
	}

}


?>