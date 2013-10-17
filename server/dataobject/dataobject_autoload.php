<?php
require_once(PATH_PEAR.'DataObject.php');
require_once(PATH_SERVER.'abstracts/DB_DataObject_Wrapper.php');
require_once(PATH_SERVER.'abstracts/DB_Session_Wrapper.php');

$config = parse_ini_file('dataobject_config.ini', TRUE);
$config["DB_DataObject"]["class_location"] = PATH_SERVER.'dataobject/'.$config["DB_DataObject"]["class_location"];
$config["DB_DataObject"]["schema_location"] = PATH_SERVER.'dataobject/'.$config["DB_DataObject"]["schema_location"];
if ( strpos(PATH_SERVER, 'development') ) {
	$config["DB_DataObject"]["database"] = "pgsql://lougis_webuser:lp123qweasdzxc@lounaispaikka4.utu.fi/atp_dev";
} else {
	$config["DB_DataObject"]["database"] = "pgsql://lougis_webuser:lp123qweasdzxc@lounaispaikka4.utu.fi/aluetietopalvelu";
}

foreach($config as $class=>$values) {
    $options = &PEAR::getStaticProperty($class, 'options');
    $options = $values;
}

/**
 * Autoload LouGIS Dataobject classes .
 *
 * @param 	string $class_name
 * @author 	Pyry Liukas <pyry.liukas@lounaispaikka.fi>
 */
function dataobjectAutoload($class_name) {
	$do =& DB_DataObject::factory($class_name);
} //end __autoload

spl_autoload_register('dataobjectAutoload');
?>
