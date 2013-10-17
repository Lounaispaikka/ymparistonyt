#!/usr/bin/php
<?php
$scriptPath = realpath(__DIR__);
require_once(realpath($scriptPath.'/../../server/config.php'));
require_once(realpath($scriptPath.'/../../server/utility/Ymparisto/Kysely.php'));
define('ARVIO_VIESTIT_TMP', '/cache/aluetietopalvelu/ymparisto/arvioinnit/');
define('ARVIO_VIESTIT_LOCKFILE', ARVIO_VIESTIT_TMP.'send_arviointi_viestit.lock');

try {
	
	$Kierros = new \Ymparisto_arviointi_kierros(2);
	
	//if ( lockfileExists() ) throw new \Exception("Process running");
	//createLockfile();
	$Kyselyt = $Kierros->getKyselyt();
	foreach($Kyselyt as $Kys) {
		\Lougis\utility\ymparisto\Kysely::sendKyselyMuistutukset($Kys->id);
	}	
	//removeLockfile();
	
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