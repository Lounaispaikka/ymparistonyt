#!/usr/bin/php
<?php
require_once('../../server/config.php');

$Usr = new \Lougis_user();

$TA = file('toimenpiteet_ja_asiantuntijat_0607.csv');

for($i=1; $i < count($TA); $i++) {

	$Roa = str_getcsv($TA[$i]);
	$KyselyId = intval($Roa[0]);
	$Virkamiehet = explode(',', str_replace(' ', '', trim($Roa[3])));
	$Admins = array();
	$Admins[] = str_replace(' ', '', trim($Roa[4]));
	$Spare = str_replace(' ', '', trim($Roa[5]));
	if ( !empty($Spare) ) $Admins[] = $Spare;
	
	$Kysely = new \Ymparisto_arviointi_kysely($KyselyId);
	
	$Asiantuntijat = array_merge($Admins, $Virkamiehet);
	foreach($Asiantuntijat as $email) {
		
		$Usr = new \Lougis_user();
		$Usr->email = $email;
		$Usr->find(true);
		if ( !empty($Usr->firstname) ) {
			$Admin = in_array($email, $Admins);
			$AdminTxt = ( $Admin ) ? 'true' : 'false';
			if ( $Kysely->addAsiantuntija($Usr->id, $Admin) ) {
				echo $Usr->email." (".$AdminTxt.") added to kysely ".$Kysely->id."\r\n";
			} else {
				echo $Usr->email." NOT added to kysely ".$Kysely->id."\r\n";
			}
		}
		
	}

}


?>