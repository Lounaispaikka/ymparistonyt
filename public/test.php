<?php
require_once('../server/config.php');
require_once(PATH_SERVER.'utility/LouGIS/Compiler.php');

global $Site, $Session;

//handle site

$Session = new \Lougis_session();
$_SESSION['site_id'] = 'ymparisto';
$_SESSION['lang_id'] = 'fi';

$Site = new \Lougis_site( $_SESSION['site_id'] );

//if ( !isset($_SESSION['user_id']) ) header('Location: /login/');

$Co = new \Lougis\utility\Compiler("hallinta", "js");

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fi" lang="fi">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="chrome=1" />
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
        <title><?=$Site->title?> hallinta</title>

        <link rel="stylesheet" type="text/css" href="/js/ext/resources/css/ext-all.css" />
        <link rel="stylesheet" type="text/css" href="/js/ext/examples/ux/css/CheckHeader.css" />
        <link rel="stylesheet" type="text/css" href="/css/scrollbars.css" />
        <link rel="stylesheet" type="text/css" href="/css/viewport.css" />
        <link rel="stylesheet" type="text/css" href="/css/hallinta.css" />
        <script type="text/javascript" src="/js/ext/builds/ext-core.js"></script>
        <script type="text/javascript" src="/js/ext/ext-all-debug.js"></script>
		<script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="/js/ext/examples/ux/CheckColumn.js"></script>
        
        <script type="text/javascript">
        	var siteId = '<?=$Site->id?>';
            Ext.Loader.setConfig({
                enabled: false,
                paths: {
                    'Lougis': '/js/lougis'
                }
            });
        </script>
        
    	<?
    	$Co->addJs("/js/lougis/lib/general.functions.js");
		$Co->addJs("/js/lougis/Fn.js");
		$Co->addJs("/js/lougis/History.js");
		$Co->addJs("/js/lougis/model/User.js");
		$Co->addJs("/js/lougis/store/Users.js");
		$Co->addJs("/js/lougis/model/Group.js");
		$Co->addJs("/js/lougis/store/Groups.js");
		$Co->addJs("/js/lougis/view/Panel.js");
		$Co->addJs("/js/lougis/view/Navigation.js");
		$Co->addJs("/js/lougis/view/Tabs.js");
		$Co->addJs("/js/lougis/view/TabViewport.js");
		$Co->addJs("/js/lougis/view/UsersAndGroups.js");
		$Co->addJs("/js/lougis/view/CMS.js");
		$Co->addJs("/js/lougis/view/News.js");
		$Co->addJs("/js/lougis/view/Charts.js");
		$Co->addJs("/js/ymparisto/view/Toimenpide.js");
		$Co->addJs("/js/lougis/controller/Navigation.js");
		$Co->addJs("/js/lougis/controller/TabContent.js");
		$Co->addJs("/js/lougis/controller/Profile.js");
		$Co->addJs("/js/lougis/app.js");
		if ( isset($_REQUEST['debug']) && strpos(PATH_SERVER, 'development') != false ) {
			$Co->outputFilesScriptTags();
		} else {
			$Co->outputScriptHtml();
		}
    	?>
    	
    </head>
    <body>
    	<div id="header-content"><strong><?=$Site->title?></strong> hallinta</div>
        <form id="history-form" class="x-hide-display">
            <input type="hidden" id="x-history-field" />
            <iframe id="x-history-frame"></iframe>
        </form>
    </body>
</html>
