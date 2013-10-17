<?php


define('PATH_PUBLIC', dirname(__FILE__).'/');
define('PATH_TEMPLATE', PATH_PUBLIC.'template/');
define('PATH_404_FILE', PATH_PUBLIC.'404.php');

require_once('../server/config.php');
require_once('../server/utility/CMS/CmsPublic.php');

global $Site, $Session, $Lang, $Cms;

$Session = new \Lougis_session();

//handle site

$_SESSION['site_id'] = 'ymparisto';
$Site = new \Lougis_site( $_SESSION['site_id'] );
$_SESSION['lang_id'] = 'fi';
$Lang = new \Lougis_language( $_SESSION['lang_id'] );

$Cms = new \Lougis\Utility\CmsPublic($Site, $Lang);
$Cms->showRequestPage();

//echo PATH_PUBLIC;
?>