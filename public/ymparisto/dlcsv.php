<?
if(is_numeric($_GET['id'])) {

	define('PATH_PUBLIC', dirname(__FILE__).'/');
	define('PATH_TEMPLATE', PATH_PUBLIC.'template/');
	define('PATH_404_FILE', PATH_PUBLIC.'404.php');

	require_once('../../server/config.php');
	require_once('../../server/utility/CMS/CmsPublic.php');

	$id = $_GET['id'];
	$chart = new lougis_chart($id);

	$chart->parseJsonToCsvFile();

}
else { 
    header('Location: ../404.php');
    die();
}