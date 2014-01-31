<?php
require_once('../config.php');
define('PATH_TEMPLATE', PATH_PUBLIC.'template/');
define('PATH_404_FILE', PATH_PUBLIC.'404.php');

require_once('../../server/config.php');
require_once(PATH_SERVER.'utility/Ymparisto/Kysely.php');
require_once(PATH_SERVER.'utility/LouGIS/Compiler.php');
require_once(PATH_SERVER.'utility/CMS/CmsYmparisto.php');

global $Site, $Session, $Lang, $Cms, $Page, $LayoutConf;

try {

	//handle site
	$Session = new \Lougis_session();
	
	$_SESSION['site_id'] = 'ymparisto';
	$Site = new \Lougis_site( $_SESSION['site_id'] );
	$_SESSION['lang_id'] = 'fi';
	$Lang = new \Lougis_language( $_SESSION['lang_id'] );
	
	$Vas = new \Ymparisto_arviointi_vastaus($_REQUEST['vastaus_id']);
	if ( empty($Vas->kysely_id) ) throw new \Exception("Vastausta ei voitu ladata.");
	if ( !$Vas->checkVastausHash($_REQUEST['hash']) ) throw new \Exception("Vastausavain ei täsmää!");
	$User = new \Lougis_user($Vas->user_id);
	if ( empty($User->email) ) throw new \Exception("Vastaajaa ei voitu ladata.");
	$Kysely = new \Ymparisto_arviointi_kysely($Vas->kysely_id);
	if ( empty($Kysely->page_id) ) throw new \Exception("Kyselyä ei voitu ladata.");
	$Kierros = new \Ymparisto_arviointi_kierros($Kysely->kierros_id);
	if ( empty($Kierros->created_date) ) throw new \Exception("Kierrosta ei voitu ladata.");
	$Toimenpide = new \Lougis_cms_page($Kysely->page_id);
	if ( empty($Toimenpide->url_name) ) throw new \Exception("Toimenpidettä ei voitu ladata.");
	//$KyselyData = \Lougis\utility\ymparisto\Kysely::loadToimenpideData($Vas->kysely_id);
	$Vastaajat = $Kysely->getAsiantuntijatArray();
	$Vastuuhenkilot = $Kysely->getAdmins();
		
	$Page = $Toimenpide;
	$Cms = new \Lougis\utility\CmsYmparisto();
	
	$LayoutConf['outputTopNav'] = false;
	require_once(PATH_TEMPLATE.'ymparisto/include_header.php');
	
	if ( count($Vastuuhenkilot) == 0 ) {
		$Nina = new \Lougis_user('nina.myllykoski@ely-keskus.fi', 'email');
		$Vastuuhenkilot[] = $Nina->toArray();
	}
	
	$_SESSION['user_id'] = $User->id;
	
	//$title = \Lougis\utility\ymparisto\Kysely::getTitleTpl($Ohjelma->title.' - '.$Toimenpide->title);
	$title = $Kysely->getKyselyTitle();
	
	if ( empty($Vas->arvio_arvo) ) $Vas->arvio_arvo = 2.5;
	
	$VasDate = null;
	if ( !empty($Vas->arvio_date) ) $VasDate = date('d.m.Y - H:i:s', strtotime($Vas->arvio_date));
	
	$arvioData = array(
		"vastaus_id" => $Vas->id,
		"user_id" => $User->id,
		"user_name" => $User->firstname.' '.$User->lastname.' - '.$User->organization,
		"arvio_arvo" => ($Vas->arvio_arvo*10),
		"arvio_perustelu" => $Vas->arvio_perustelu,
		"arvio_date" => $VasDate
	);
	
	?>

	<script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
	
	<div id="content">
	<div style="float:right">
	<a href="<?=$Toimenpide->getPageUrl()?>" target="_blank">Linkki toimenpide-sivulle</a>
	</div>
	<h1>Arvioitava toimenpide</h1>
	<blockquote>
	<?
		echo $Cms->processYmparistoTemplate( $Toimenpide->getContentHtml() );
	?>
	</blockquote>
	<h2>Toimenpiteen etenemisen arviointi</h2>
	
	<?
	if ( isset($_REQUEST['debug']) ) {
		print_r_html($Vas);
		print_r_html($User);
		print_r_html($Kysely);
		print_r_html($Kierros);
		print_r_html($Vastuuhenkilot);	
		print_r_html($Toimenpide);
	} ?>
	<div style="margin: 0 0 0 50px; width: 600px">
	
	<?=$Kierros->content1?>
	<? /*
	<p>Arvioi toimenpidettä oman työsi/toimintasi vinkkelistä. Anna arvio, miten toimenpide tulee toteutumaan Lounais-Suomessa vuoden 2013 loppuun mennessä ja kirjoita perustelu arviollesi. Perusteluissa voit antaa myös esimerkkejä ja numerotietoa.</p>
	*/ ?>
	<p>
	<? if ( count($Vastuuhenkilot) == 1 ) { 
		$Vu = $Vastuuhenkilot[0];
	?>
		Tämän toimenpiteen arvioinnin vastuuhenkilö on <a href="mailto:<?=$Vu['email']?>"><?=$Vu['firstname']?> <?=$Vu['lastname']?> - <?=$Vu['organization']?></a>. Häneltä voit kysyä tarvittaessa lisätietoja.
	<? } else { ?>
		
	<? } ?>
	</p>
	<p>
	<? if ( count($Vastaajat) > 1 ) { ?>
		<p>Tätä toimenpidettä arvioivat myös:</p>
		<ul style="list-style: circle;list-style-type: circle; margin: 0; padding: 0 10px 0 20px">
		<? foreach($Vastaajat as $Va) { 
			if ( $Va['email'] != $User->email ) {
		?>
		<li><?=$Va['firstname']?> <?=$Va['lastname']?> - <?=$Va['organization']?></li>
		<? 
			}
		} ?>
		</ul>
	<? } ?>
	</p>
	
	<div id="vastausDiv" style="margin-bottom: 20px;width: 600px"></div>

	<p>
	Voit tallentaa arvioinnin myös keskeneräisenä ja palata muokkaamaan sitä vastaanottamasi linkin kautta aina arviointikierroksen päättymiseen asti (<?=date('d.m', strtotime($Kierros->closing_date))?>).<br/>
	Arviot julkaistaan Ympäristö Nyt –palvelussa arviointiajan päätyttyä. Kiitos!</p>
	</div>
	
	<script type="text/javascript">
	var arvioData = <?=json_encode($arvioData)?>;
	var kyselyTitle = '<?=trim($Kierros->title2)?>';
	</script>
	<?
	$Co = new \Lougis\utility\Compiler("arviointi", "js");
	$Co->addJs("/js/ymparisto/arviointi.ui.extjs.js");
	if ( isset($_REQUEST['debug']) && strpos(PATH_SERVER, 'development') != false ) {
		$Co->outputFilesScriptTags();
	} else {
		$Co->outputScriptHtml();
	}
	?>
	</div>
	<?
	
} catch(\Exception $e) {
	echo "Virhe:".$e->getMessage().", ota yhteyttä ylläpitoon<br/>";
	echo "Ylläpito: ymparistonyt at lounaispaikka . fi";
}

require_once('../template/ymparisto/include_footer.php');
?>