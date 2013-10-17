<?php

global $Site, $Page;

$TopNews = $Site->getNews(6);
//$RecentCharts = $Site->getPubInds(4);

require_once(PATH_TEMPLATE.'ymparisto/include_header.php'); 
?>
<div id="breadcrumb"><? $Cms->outputBreadcrumb(); ?><a href="/fi/sanasto/" style="position:absolute;right:106px; font-size: 12px;">Sanasto</a></div>
<div id="content">
	<div id="front-content">
	{PAGE_CONTENT}
	</div>
	<div id="front-news">
		<h1>Ajankohtaista</h1>
		<ul>
		<? foreach($TopNews as $News) { ?>
		<li>
		<a href="/fi/ajankohtaista/?nid=<?=$News->id?>#n<?=$News->id?>">
		<h1><?=$News->title?></h1>
		<span class="newsInfo"><?=date("d.m.Y", strtotime($News->created_date))?></span>
		<p><?=$News->description?></p>
		</a>
		</li>
		<? } ?>
		</ul>
		<p><a href="/fi/ajankohtaista/">Lis&auml;&auml; ajankohtaista...</a></p>
	</div>
</div>
<? require_once(PATH_TEMPLATE.'ymparisto/include_footer.php'); ?>