<?php

$dbh = new PDO("pgsql:host=lounaispaikka4.utu.fi;port=5432;dbname=mip_dev;user=lougis_webuser;password=lp123qweasdzxc");

$table = $_REQUEST['table'];
$schema = $_REQUEST['schema'];

if($schema == "Rakennusinventointi 2011") {
    $tables = array(
        "Kiinteistöt" => "kiinteistö",
		'Kunnat' => "kunta",
        "Kylät" => "kylä",
        "Rakennukset" => "rakennus",
        'Rakennusten suunnittelijat/rakentajat' => "rakennus_rakentaja",
        'Suunnittelijat/rakentajat' => "rakentaja"
    );

    $table = "rakennusinventointi_2011.{$tables[$table]}";
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
    $res[] = ucfirst(str_replace("_", " ", $column));
}
header('Content-type: application/json');
echo json_encode($res);

?>