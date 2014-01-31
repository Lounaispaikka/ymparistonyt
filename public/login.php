<?php

require_once('../server/config.php');
	
global $Site, $Session;

$Session = new \Lougis_session();
$_SESSION['site_id'] = 'ymparisto';
$Site = new \Lougis_site( $_SESSION['site_id'] );

$errMsg = "";
if ( isset($_GET['logout']) ) $errMsg = "Olet kirjautunut ulos onnistuneesti.";

$redir = ( !empty($_REQUEST['redir']) ) ? $_REQUEST['redir'] : "admin";

require_once(PATH_SERVER.'utility/UsersAndGroups/User.php');
if(isset($_REQUEST['email']) && isset($_REQUEST['password']) && !empty($_REQUEST['email']) && !empty($_REQUEST['password']) && $_REQUEST['email'] != "") {
    $user = new \Lougis\utility\User();
    $success = $user->login($_REQUEST['email'], $_REQUEST['password']);
    if(!$success) {
    	$errMsg = "Kirjautuminen epäonnistui, ole hyvä ja yritä uudelleen.";
    } else {
    	switch($redir) {
    		case 'admin':
    		default:
    			$_SESSION['admin_login'] = true;
    			header('Location: /hallinta/');
    		break;
    	}
    }
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fi" lang="fi">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="chrome=1" />
        <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
        <title><?=$Site->title?></title>

        <link rel="stylesheet" type="text/css" href="/js/ext/resources/css/ext-all.css" />
        <link rel="stylesheet" type="text/css" href="/css/reset.css" />
        <link rel="stylesheet" type="text/css" href="/css/scrollbars.css" />
        <link rel="stylesheet" type="text/css" href="/css/viewport.css" />
        <script type="text/javascript" src="/js/ext/builds/ext-core.js"></script>
        <script type="text/javascript" src="/js/ext/ext-all-debug.js"></script>
        <title>GIFlood</title>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <!--<link rel="stylesheet" type="text/css" href="/css/lougis.ui.css"/>-->
        <style type="text/css">
            body {
                color: white;
                margin: 10px;
                font-family: "Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;
                font-size: 16px;
            }
            a {
                color: #3286ce;
                text-decoration: none;
            }
            a:hover {
                color: #16ff2c;
                text-decoration: underline;
            }

            #lgCenter {
                color: #3286ce;
                display: block;
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                text-align: center;
                padding: 15% 0 0 0;
                z-index: 10000;
            }
            .centeredTable {
                width: 100px;
                margin-left: auto;
                margin-right: auto;
            }
            td {
               padding: 3px;
            }

        </style>
    </head>
    <body>
        <div id="lgCenter">
            <div id="lgPassword">
                <? if( !empty($errMsg) ) { ?>
                    <h1><?=$errMsg?></h1>
                <? } ?>
                <h1 style="font-size: 24px"><?=$Site->title?> hallinta</h1>
                <br/>
                <form name="password" action="#" method="POST">
                	<input type="hidden" name="redir" value="<?=$redir?>" />
                    <table class="centeredTable">
                        <tr><td>Sähköposti:</td><td><input type="email" name="email"/></td></tr>
                        <tr><td>Salasana:</td><td><input type="password" name="password" /></td></tr>
                        <tr><td></td><td class="centeredInput"><input type="submit" value="Kirjaudu"/></td></tr>
                    </table>
                </form>
                <? if ( isset($_GET['debug']) ) {
                	print_r_html($Site);
                	print_r_html($Session);
                	print_r_html($_SESSION);
                	}	
                ?>
            </div>
        </div>
    </body>
</html>