<?php
require_once('../../server/config.php');

$Chart = new \Lougis_chart($_REQUEST['id']);
$Width = ( !isset($_REQUEST['w']) || empty($_REQUEST['w']) ) ? 500 : intval($_REQUEST['w']);
$Height = ( !isset($_REQUEST['h']) || empty($_REQUEST['h']) ) ? 300 : intval($_REQUEST['h']);
$Caa = $Chart->toChartArray();

if ( empty($Chart->created_date) ) {
	echo "Tilastoa ei lyötynyt";die;
}
?><!DOCTYPE html>
<html>
<head>
	<title><?=$Chart->title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="all" />
	<meta name="author" content="Lounaispaikka - www.lounaispaikka.fi" />
	<meta http-equiv="X-UA-Compatible" content="IE=8">
    <link rel="stylesheet" type="text/css" href="/js/ext/resources/css/ext-all.css" />
    <script type="text/javascript" src="/js/ext/ext-all.js"></script>
    </head>
    <body>
    <div id="chartDiv" style="width: <?=$Width?>px;height: <?=$Height?>px"></div>
    <script type="text/javascript">
    Ext.onReady(function () {
    	var chartObj = <?=json_encode($Caa)?>
    	
    	var storeFields = [];
		Ext.each(chartObj.data.fields, function(field, idx) {
			var storeField = {
				name: field.dataindex,
				type: field.type
			}
			storeFields.push(storeField);
		}, this);
		
	    var chartStore = Ext.create('Ext.data.ArrayStore', {
	        autoDestroy: true,
	        fields: storeFields,
	        data: chartObj.data.data
	    });
    	
    	chartObj.config.store = chartStore;
	    chartObj.config.width = <?=$Width?>;
	    chartObj.config.height = <?=$Height?>;
    	
    	Ext.create('Ext.panel.Panel', {
    		frame: false,
    		border: 0,
	    	renderTo: 'chartDiv',
	    	items: [ chartObj.config ]
    	});
    });
    </script>
    </body>
</html>