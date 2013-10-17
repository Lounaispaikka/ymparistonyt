<?php
global $Site, $Lang, $Cms, $Pg;

require_once(PATH_SERVER.'utility/CMS/CmsPublic.php');
?><!DOCTYPE html>
<html>
<head>
	<title><?=$Site->title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="all" />
	<meta name="author" content="Lounaispaikka - www.lounaispaikka.fi" />
	
    <link rel="stylesheet" type="text/css" href="/js/ext/resources/css/ext-all.css" />
    <script type="text/javascript" src="/js/ext/ext-all.js"></script>
	<script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
	
	<link rel="stylesheet" type="text/css" href="/css/aluetietopalvelu.css" /> 
	
</head>
<body> 
<div id="northBar">
	<div id="northWrap">
		<ul id="northNav">
			<li><a href="#">Aluetietopalvelu</a></li>
			<li><a href="#">Kyselyt</a></li>
			<li><a href="#">Tilastot</a></li>
			<li><a href="#">Kartat</a></li>
		</ul>
	</div>
	<img id="logo" src="/img/lounaispaikka-logo.png" />
</div>
<div id="site"> 
	<div id="header" class="ymparisto">
		Ympäristö Nyt<br/><small>Lounais-Suomen ympäristön tila ja seuranta</small>
	</div>
	<div id="middle">