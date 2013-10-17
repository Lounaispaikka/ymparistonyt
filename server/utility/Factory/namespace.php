<?php

/**
 * Autoload LouGIS Dataobject classes .
 *
 * @param 	string $class_name
 * @author 	Pyry Liukas <pyry.liukas@lounaispaikka.fi>
 */
function dataobjectAutoload($class_name) {
	$do =& DB_DataObject::factory($class_name);
} //end __autoload


?>