<?php
require_once('../server/config.php');
require_once(PATH_SERVER.'utility/LouGIS/Compiler.php');

global $Site, $Session;

//handle site

$Session = new \Lougis_session();
$_SESSION['site_id'] = 'ymparisto';
$_SESSION['lang_id'] = 'fi';

$Site = new \Lougis_site( $_SESSION['site_id'] );

if ( !isset($_SESSION['user_id']) ) header('Location: /login/');

$Co = new \Lougis\utility\Compiler("hallinta", "js");

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fi" lang="fi"> 
   <head>
        <!--<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />-->
        <meta charset="utf-8">
     <meta http-equiv="X-UA-Compatible" content="chrome=1" />
     <meta http-equiv="X-UA-Compatible" content="IE=8">
        <title><?=$Site->title?> hallinta</title>
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
		
        <link rel="stylesheet" type="text/css" href="/js/ext407/resources/css/ext-all.css" />
        <link rel="stylesheet" type="text/css" href="/js/ext407/examples/ux/css/CheckHeader.css" />
        <link rel="stylesheet" type="text/css" href="/css/scrollbars.css" />
        <link rel="stylesheet" type="text/css" href="/css/viewport.css" />
        <link rel="stylesheet" type="text/css" href="/css/hallinta.css" />
        
        <script type="text/javascript" src="/js/ext407/builds/ext-core.js"></script>
        <script type="text/javascript" src="/js/ext407/ext-all.js"></script>
        <? /*<script type="text/javascript" src="/js/ext/ext-all-debug-w-comments.js"></script> */ ?>
        <script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="/js/ext407/examples/ux/CheckColumn.js"></script>
        <script type="text/javascript">
		if (!window.console || !console.firebug)
		{
			var names = ["log", "debug", "info", "warn", "error", "assert", "dir", "dirxml",
			"group", "groupEnd", "time", "timeEnd", "count", "trace", "profile", "profileEnd"];

			window.console = {};
			for (var i = 0; i < names.length; ++i)
				window.console[names[i]] = function() {}
		}

		</script>
        
             
        

        <script type="text/javascript">
        	var siteId = '<?=$Site->id?>';
          Ext.Loader.setConfig({
                enabled: true,
                paths: {
                    'Lougis': '/js/lougis'
                }
            }); 
        </script>
<? /*
        <script type="text/javascript" src="/js/lougis/lib/general.functions.js"></script>
        <script type="text/javascript" src="/js/lougis/Fn.js"></script>
        <script type="text/javascript" src="/js/lougis/History.js"></script>
        <script type="text/javascript" src="/js/lougis/model/User.js"></script>
        <script type="text/javascript" src="/js/lougis/store/Users.js"></script>
        <script type="text/javascript" src="/js/lougis/model/Group.js"></script>
        <script type="text/javascript" src="/js/lougis/store/Groups.js"></script>
        <script type="text/javascript" src="/js/lougis/view/Panel.js"></script>
        <script type="text/javascript" src="/js/lougis/view/Navigation.js"></script>
		<? /*<script type="text/javascript" src="/js/lougis/view/Tabs.js"></script> 
        <script type="text/javascript" src="/js/lougis/view/TabViewport.js"></script>
        <script type="text/javascript" src="/js/lougis/view/UsersAndGroups.js"></script>
        <script type="text/javascript" src="/js/lougis/view/CMS.js"></script>
         <script type="text/javascript" src="/js/lougis/view/News.js"></script>
          <script type="text/javascript" src="/js/lougis/view/Charts.js"></script>
           <script type="text/javascript" src="/js/ymparisto/view/Toimenpide.js"></script>
            <script type="text/javascript" src="/js/lougis/controller/Navigation.js"></script>
            <script type="text/javascript" src="/js/lougis/controller/TabContent.js"></script>
            <script type="text/javascript" src="/js/lougis/controller/Profile.js"></script>
            <script type="text/javascript" src="/js/lougis/app.js"></script> */ ?>
        
    	
         <?      $Co->addJs("/js/lougis/lib/general.functions.js");
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
			//$Co->outputFilesScriptTags();
			$Co->outputScriptHtml();
		} else {
			$Co->outputScriptHtml();
		} ?>
		
    </head>
    <body>
        <div id="header-content"><strong><?=$Site->title?></strong> hallinta</div>
        <form id="history-form" class="x-hide-display">
            <input type="hidden" id="x-history-field" />
            <iframe id="x-history-frame"></iframe>
        </form>
    </body>
</html>
