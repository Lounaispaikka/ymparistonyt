<?php 
require_once(PATH_TEMPLATE.'ymparisto/include_header.php'); 
require_once(PATH_SERVER.'utility/LouGIS/Compiler.php');

global $Site, $Cms;

$Class = null;
$LeftCol = false;
$RightCol = false;
$Pg = $Cms->getPage();
if ( $Cms->currentPageHasParent() || $Cms->currentPageHasChildren() ) { 
	$Parent = $Cms->findCurrentPageTopParent( );
	$LeftCol = true;
}
if ( $Cms->hasRightColumn() || $Pg->page_type == 'ohjelma' || $Pg->page_type == 'toimenpide' ) {
	$RightCol = true;
}
/* Tavoitteet content diviss‰
 * if ( $Cms->hasRightColumn() ) {
	$RightCol = true;
}
 */

if ( $LeftCol && $RightCol ) {
	$Class = "col3";
} elseif( $LeftCol ) {
	$Class = "col2";
} elseif( $RightCol ) {
        $Class = "col4";
}


$TavoitteetPrinted = false;
?>
<div id="breadcrumb"><? $Cms->outputBreadcrumb() ?></div>
<? if ( $LeftCol ) { ?>
<div id="leftCol" class="<?=$Class?>">
	<? $Cms->outputLeftNavigation($Parent); ?>
</div>
<? } ?>
<? if ( $RightCol ) { ?>
<div id="rightCol" class="<?=$Class?>">
        
                <? 
                $PageNews = $Pg->getNews();
                $ColCon = $Pg->getColumnHtml();
                if (count($PageNews) > 0 || !empty($ColCon)) {
                ?>
                <div id="rightColGrey">
                <?
                       
                if ( count($PageNews) > 0 ) {
                ?>
                <div id="pageNews">
                        <h1>Ajankohtaiset</h1>
                        <? foreach($PageNews as $News) { ?>
                        <p><a href="/fi/ajankohtaista/?nid=<?=$News->id?>#n<?=$News->id?>"><?=$News->title?></a></p>
                        <? } ?>
                </div>
                <? } ?>
                <?
                //$ColCon = $Pg->getColumnHtml();
                if ( !empty($ColCon) ) echo $ColCon;
                ?>
                </div>
        <? }
	if ( !$TavoitteetPrinted &&  ( $Pg->page_type == 'ohjelma' || $Pg->page_type == 'toimenpide' )/*$Pg->page_type == 'strategia'*/  ) {
		echo '[AUTOMAATTINEN_YLEMMAT_TAVOITTEET]';
		$TavoitteetPrinted = true;
	}
	?>
</div>
<? } ?>


<div id="content" class="<?=$Class?>">
<?/*
 * Tavoitteet content-diviss‰
if ( !$TavoitteetPrinted && ( $Pg->page_type == 'ohjelma' || $Pg->page_type == 'toimenpide' ) ) {
	echo '[AUTOMAATTINEN_YLEMMAT_TAVOITTEET]';
}*/
?>
{PAGE_CONTENT}
<?
if ( $Pg->page_type == 'toimenpide' && $Kysely = \Ymparisto_arviointi_kysely::hasPublishedArviointi($Pg->id) ) {
	$Kierros = new \Ymparisto_arviointi_kierros($Kysely->kierros_id);
	$KaikkiArvioinnit = $Kysely->getArvioinnitArray();
	$PublicArvioinnit = array();
	$Arvot = array();
	foreach( $KaikkiArvioinnit as $Arviointi ) {
		if ( !empty($Arviointi['arvio_arvo']) ) {
			$PublicArvioinnit[] = $Arviointi;
			$Arvot[] = $Arviointi['arvio_arvo'];
		}
	}
	$AvgArvo = array_sum($Arvot)/count($Arvot);
	$ChartVal = intval($AvgArvo*10);
	$RoundVal = intval($AvgArvo);
	$ArvoTeksti = \Ymparisto_arviointi_vastaus::arvo2teksti( $RoundVal );
	?>
	<div id="arvioinnit">
		<div id="yhteenveto">
			<div id="yhteenveto-chart"></div>
			<h2 style="margin-top: 0"><?=$Kierros->title3?></h2>
			<br/><br/>
			<p>Asiantuntijoita: <?=count($Arvot)?></p>
			<p>Asiantuntijoiden arvio toteutumisesta:</p>
			<h2>Toimenpide tulee toteutumaan - <?=$ArvoTeksti?></h2>
		</div>
		<hr class="clr" />
		<? foreach( $PublicArvioinnit as $Idx => $Arvio ) { 
		?>
			<div class="tarviointi">
			<img class="sulje" id="abtn<?=$Arvio['id']?>" src="/img/close.png" alt="" title="Sulje arviointi" onclick="hideArviointi(<?=$Arvio['id']?>);" />
			<p style="color: #51a83c;"><?=date('d.m.Y', $Arvio['arvio_date'])?> <?=$Arvio['user_name']?></p>
			<div id="ac<?=$Idx?>" class="minichart"></div>
			<div id="ashort<?=$Arvio['id']?>" class="ashort">
			<p><?
			$Text = \Ymparisto_arviointi_vastaus::getPlainPerustelu($Arvio['arvio_perustelu']);
			//Jotta ilman arviointiteksti‰ olevat arviomittarit pysyisiv‰t "laatikossa"
                        if(strlen($Text) === 0) {
                                echo '<br/><br/><br/><br/>';
                        }
                        else {
                                echo substr($Text, 0, 300);
			if ( strlen($Text) > 300 ) { ?>
				...<br/>
				<a onclick="showArviointi(<?=$Arvio['id']?>);">Lue koko arviointi &raquo;</a>
			<? } 
                        }?>
                               
			</p>
			</div>
			<div id="atext<?=$Arvio['id']?>" class="atext"><?=$Arvio['arvio_perustelu']?></div>
			<? if ( $Idx != count($PublicArvioinnit)-1 ) echo '<hr class="clr" />'; ?>
			</div>
		<? } ?>
	</div>
	<?
	$Co = new \Lougis\utility\Compiler("toimenpide-ui-extjs", "js");
	$Co->addJs("/js/ymparisto/toimenpide.ui.extjs.js");
	if ( isset($_REQUEST['debug']) && strpos(PATH_SERVER, 'development') != false ) {
		$Co->outputFilesScriptTags();
	} else {
		$Co->outputScriptHtml();
	}
	?>
	<script type="text/javascript">
	Ext.onReady(function () {
		createArvioChart(<?=$ChartVal?>, 'yhteenveto-chart', 230, 120);
		<? foreach( $PublicArvioinnit as $Idx => $Arvio ) { 
			$PublicChartArvo = intval($Arvio['arvio_arvo']*10);
		?>
		createArvioChart(<?=$PublicChartArvo?>, 'ac<?=$Idx?>', 120, 80);
		<? } ?>
	});
	</script>
	<?
}
?>

<div id="social">

	<div id="email">
	<a href="mailto:?Subject=<?=$Site->title?> - <?=$Pg->title?>&Body=<?=$Pg->getPageFullUrl()?>">S√§hk√∂posti</a>
	</div>

	<div id="fb">
	<iframe src="//www.facebook.com/plugins/like.php?href=<?=urlencode($Pg->getPageFullUrl())?>&amp;locale=fi_FI&amp;layout=button_count&amp;show_faces=false&amp;width=125&amp;action=recommend&amp;colorscheme=light&amp;height=20" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:125px; height:21px;" allowTransparency="true"></iframe>
	</div>
	
	<div id="twitter">
	<a href="https://twitter.com/share" class="twitter-share-button" data-lang="fi" data-hashtags="ymparistonyt">Twiittaa</a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	</div>
	
</div>

<? require_once(PATH_PUBLIC.'ymparisto/kommentointi.php'); ?>
</div>
<? require_once(PATH_TEMPLATE.'ymparisto/include_footer.php'); ?>
