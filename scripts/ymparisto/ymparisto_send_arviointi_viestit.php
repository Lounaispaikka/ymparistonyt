#!/usr/bin/php
<?php
$scriptPath = realpath(__DIR__);
require_once(realpath($scriptPath.'/../../server/config.php'));
require_once(realpath($scriptPath.'/../../server/utility/Ymparisto/Kysely.php'));
define('ARVIO_VIESTIT_TMP', '/cache/aluetietopalvelu/ymparisto/arvioinnit/');
define('ARVIO_VIESTIT_LOCKFILE', ARVIO_VIESTIT_TMP.'send_arviointi_viestit.lock');

try {
	
	$Kierros = new \Ymparisto_arviointi_kierros();
	$Kierros->in_process = true;
	$Count = $Kierros->find();
	
	if ( $Count < 1 ) {
		if ( lockfileExists() ) removeLockfile();
		throw new \Exception("Ei asiantuntijoita");
	}
	//if ( lockfileExists() ) throw new \Exception("Process running");
	createLockfile();
	while( $Kierros->fetch() ) {
		$Kr = clone($Kierros);
		$Kyselyt = $Kr->getKyselyt();
		foreach($Kyselyt as $Kys) {
			\Lougis\utility\ymparisto\Kysely::sendKyselyMessages($Kys->id);
		}
		$Kr->started_date = date(DATE_W3C);
		$Kr->in_process = false;
		$Kr->notes = "Kierroksen arviointikyselyt lahetetty asiantuntijoille ".date("d.m.Y H:i:s");
		if ( !$Kr->save() ) throw new \Exception("Kierrosta ei voitu tallentaa!");
	}
	removeLockfile();
	
} catch(\Exception $e) {
	echo $e->getMessage()."\r\n";
}

function lockfileExists() {
	return file_exists(ARVIO_VIESTIT_LOCKFILE);
}
function removeLockfile() {
	return unlink(ARVIO_VIESTIT_LOCKFILE);
}
function createLockfile() {
	if ( !file_exists(ARVIO_VIESTIT_TMP) ) mkdir(ARVIO_VIESTIT_TMP, 0775, true);
	return file_put_contents(ARVIO_VIESTIT_LOCKFILE, date(DATE_W3C));
}

?>