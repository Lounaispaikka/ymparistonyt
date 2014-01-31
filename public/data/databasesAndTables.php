<?php
	$tables = array(
		array(
			"id" => 0,
			"schema" => 'rakennusinventointi_2011',
			"name" => "kiinteistö",
            "attributes" => getAttributes("rakennusinventointi_2011", 'kiinteistö')
		),
		array(
			"id" => 1,
			"schema" => 'rakennusinventointi_2011',
			"name" => 'kunta',
            "attributes" => getAttributes("rakennusinventointi_2011", 'kunta')
		),
		array(
			"id" => 2,
			"schema" => 'rakennusinventointi_2011',
			"name" => 'kylä',
            "attributes" => getAttributes("rakennusinventointi_2011", 'kylä')
		),
		array(
			"id" => 3,
			"schema" => 'rakennusinventointi_2011',
			"name" => 'rakennus',
            "attributes" => getAttributes("rakennusinventointi_2011", 'rakennus')
		),
		array(
			"id" => 4,
			"schema" => 'rakennusinventointi_2011',
			"name" => 'rakennus_rakentaja',
            "attributes" => getAttributes("rakennusinventointi_2011", 'rakennus_rakentaja')
		),
		array(
			"id" => 5,
			"schema" => 'rakennusinventointi_2011',
			"name" => 'rakentaja',
            "attributes" => getAttributes("rakennusinventointi_2011", 'rakentaja')
		)
	);

	$res = array(
		"tables" => $tables,
		"success" => true
	);

	header('Content-type: application/json');
	echo json_encode($res);

function getAttributes($schema, $table) {
    $dbh = new PDO("pgsql:host=lounaispaikka4.utu.fi;port=5432;dbname=mip_dev;user=lougis_webuser;password=lp123qweasdzxc");
    if($schema == "rakennusinventointi_2011") {
        $table = "rakennusinventointi_2011.{$table}";
        $query = "SELECT * FROM {$table} LIMIT 1";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $columns = array_keys($stmt->fetch(PDO::FETCH_ASSOC));
    }
    else if($table == "Puutarhat") {
        $columns = array(
            "id",
            "kiinteistö_id",
            "puutarhan_tyyppi",
            "istutukset"
        );
    }
    else {
        $columns = array(
            "id",
            "puutarha_id",
            "kivilaji",
            "muoto",
            "kulttuurihistoriallinen_merkitys"
        );
    }



    $res = array();
    foreach($columns as $column) {
        $res[] = array(
            "schema" => $schema,
            "table" => $table,
            "attribute" => $column,
            "dataType" => "text"
        );
    }
    return $res;
}


?>