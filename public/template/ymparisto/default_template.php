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
/* Tavoitteet content divissä
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

<div id="breadcrumb"><? $Cms->outputBreadcrumb(); ?></div>
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
                 <?
                if ( $Pg->url_name == 'ymparistoohjelma' ) {
                        $RecentCharts = $Site->getRecentCharts(3);
                        $RecentComments = $Site->getRecentComments(3);
                ?>
                <div id="recentPubCharts">
                       <p><strong>PÃ¤ivitetyt indikaattorit</strong></p>
                        <dl class="recent_charts">
                        <? foreach($RecentCharts as $Ch) { ?>
                                <dt class="recent_charts"><a href="/fi/indikaattorit/?id=<?=$Ch->id?>"><?=$Ch->title?></a></dt>
                        <? } ?>
                        </dl>
                </div>
               <div id="recentComments">
                       <p><strong>Uusimmat kommentit</strong></p>
                        <dl class="recent_charts">         
                        <? foreach($RecentComments as $Comment) { ?>
							<dt class="recent_charts"><a href="/fi/<?=$Comment->url_name?>/<? if(!empty($Comment->part_id)) echo '?id='.$Comment->part_id; ?>">"<?=$Comment->title."\"<br />"?><span class="commentDetails"><? if(!empty($Comment->chart_title)) echo $Comment->chart_title; else echo $Comment->page_title;?><br /> <?=date('d.m.Y H:i:s', strtotime($Comment->date))?></span></a></dt>
                        <? } ?>
                        </dl>
                </div>   
                <? } ?>
                </div> 
        <? }
	if ( !$TavoitteetPrinted && ( $Pg->page_type == 'strategia'  ) ) {
                echo '[AUTOMAATTINEN_YLEMMAT_TAVOITTEET]';
                $TavoitteetPrinted = true;
        }
	?>
</div>
<? } ?>
<div id="content" class="<?=$Class?>">
<?
 // Tavoitteet content-divissä HUOM!!! Tulevat sisällönhallinnasta
/*if ( !$TavoitteetPrinted && ( $Pg->page_type == 'ohjelma' || $Pg->toimenpide == 'page_type' ) ) {
	echo '[AUTOMAATTINEN_YLEMMAT_TAVOITTEET]';
}*/
/*if ( !$TavoitteetPrinted && ( $Pg->page_type == 'strategia'  ) ) {
	echo '[AUTOMAATTINEN_YLEMMAT_TAVOITTEET]';
        $TavoitteetPrinted = true;
}*/
?>
{PAGE_CONTENT}
<?
if ( $Pg->page_type == 'toimenpide' && $Kysely = \Ymparisto_arviointi_kysely::getPublishedArviointi($Pg->id, $_GET["kierros"]) ) {
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

    $kierrokset = \Ymparisto_arviointi_kierros::getKierrokset();
	?>
	<div id="arvioinnit">
        <div id="arvioinnitKierrosValinta">
            Arviointikierrokset:
            <? foreach($kierrokset as $kierros) { ?>
                <a href="?kierros=<?= $kierros->id ?>"><?= $kierros->title1 ?></a>
                <? if ($kierros !== end($kierrokset)) echo '&middot;' ?>
            <? } ?>
        </div>
		<hr class="clr" />
		<div id="yhteenveto">
		<h2 style="margin-top: 0"><?=$Kierros->title3?></h2>
			<br/><br/>
			<div id="yhteenveto-chart"></div>
	<? /*		<h2 style="margin-top: 0"><?=$Kierros->title3 ?></h2><? */?>
				
	<? /*		<!--<p>Asiantuntijoita: <?=count($Arvot)?></p>-->
			<!--<p>Asiantuntijoiden arvio toteutumisesta:</p>-->*/ ?>
		<?/*	<h2>Toimenpide tulee toteutumaan - <?=$ArvoTeksti?></h2> */?>
				<p style="text-align:center;">Toteutuu <?=$ArvoTeksti?></p>
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
			//Jotta ilman arviointitekstiä olevat arviomittarit pysyisivät "laatikossa"
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
	<a href="mailto:?Subject=<?=$Site->title?> - <?=$Pg->title?>&Body=<?=$Pg->getPageFullUrl()?>">Sähköposti</a>
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
