<?
global $Cms;

$Pg = $Cms->getPage();

$_SESSION['comment_page_id'] = $Pg->id;
//jos indikaattori-sivu $Chart-olio on luotu indikaattorit_uusi-templatessa
if($Pg->url_name == "indikaattorit") {
	$_SESSION['comment_part_id'] = $Chart->id;
}
else if($Pg->url_name == "kylasuunnitelmat")
{
    $_SESSION['comment_part_id'] = isset($_GET['kierros']) ? $_GET['kierros'] : \Ymparisto_arviointi_kysely::getMostRecent($_SESSION['comment_page_id']);
}
else {unset($_SESSION['comment_part_id']);}

if ( !isset($_SESSION['rated_comments']) ) $_SESSION['rated_comments'] = array();

$Comments = \Lougis_cms_comment::getAllForPage($Pg->id, $_SESSION['comment_part_id']);
$Rules = \Lougis_cms_comment::getRules();
?>
<div id="comments" style="margin-bottom: 20px">
<!--<a id="newthread" onclick="showNewMsg();">Kirjoita uusi viesti</a>-->
<h2>Keskustele</h2>
<? if ( count($Comments) > 0 ) { ?>
<ul id="messages">
<? foreach($Comments as $Cm) {  ?>
	<li id="cm<?=$Cm->id?>"><a name="cm<?=$Cm->id?>"></a>
            <div class="commentBubble">
	<?
	$clicked = in_array($Cm->id, $_SESSION['rated_comments']);
	?>
	<div id="lbox<?=$Cm->id?>" class="likebox<?=(($clicked) ? ' clicked' : '' )?>">
		<a class="likethumb" onclick="<?=(($clicked) ? '' : 'likeComment('.$Cm->id.');' )?>" title="Äänestä viestiä (+)">
			<img src="/img/thumbup.png" alt="" class="sulje" /> <span><?=$Cm->likes?></span>
		</a>
		<a class="dislikethumb" onclick="<?=(($clicked) ? '' : 'dislikeComment('.$Cm->id.');' )?>" title="Äänestä viestiä (-)">
			<img src="/img/thumbdown.png" alt="" class="sulje" /> <span><?=$Cm->dislikes?></span>
		</a>
	</div>
	<h5 class="messageTitle"><?=$Cm->title?></h5>
	<p><?=nl2br($Cm->msg)?></p>
	<span class="author"><span><?=$Cm->nick?></span> kirjoitti <?=date('d.m.Y H:i:s', strtotime($Cm->date_created))?></span>
	<a class="replythread" onclick="showReplyBox(<?=$Cm->id?>);">Vastaa</a>
            </div>
	<div id="replybox<?=$Cm->id?>" class="closereplybox"></div>
	<? if ( count($Cm->replys) > 0 ) { ?>
	<ul class="replys">
	<? foreach($Cm->replys as $Reply) { ?>
		<li><a name="cm<?=$Reply->id?>"></a>
                    <div class="commentBubble">
			<?
			$clicked = in_array($Reply->id, $_SESSION['rated_comments']);
			?>
			<div id="lbox<?=$Reply->id?>" class="likebox<?=(($clicked) ? ' clicked' : '' )?>">
				<a class="likethumb" onclick="<?=(($clicked) ? '' : 'likeComment('.$Reply->id.');' )?>" title="Äänestä viestiä (+)">
					<img src="/img/thumbup.png" alt="" class="sulje" /> <span><?=$Reply->likes?></span>
				</a>
				<a class="dislikethumb" onclick="<?=(($clicked) ? '' : 'dislikeComment('.$Reply->id.');' )?>" title="Äänestä viestiä (-)">
					<img src="/img/thumbdown.png" alt="" class="sulje" /> <span><?=$Reply->dislikes?></span>
				</a>
			</div>
			<h5 class="messageTitle"><?=$Reply->title?></h5>
			<p><?=nl2br($Reply->msg)?></p>
			<span class="author"><span><?=$Reply->nick?></span> kirjoitti <?=date('d.m.Y H:i:s', strtotime($Reply->date_created))?></span>
                    </div>
		</li>
	<? } ?>
	</ul>
	<? } ?>
	</li>
<? } ?> 
</ul>
<? } ?>
<div id="newcomment" style="opacity: 1; margin-left: 40px;" class="msgform"> <!-- style="opacity: 1; font-size: 10px;">-->
	<!--<img id="closenewmsg" src="/img/close.png" alt="" title="Sulje" onclick="hideNewMsg();" class="sulje" />-->
	<h2>Uusi viesti</h2>
	<div id="newcommentform">
        </div>
	<?=$Rules?>
</div>
</div>
<?

$Co = new \Lougis\utility\Compiler("comments-ui-extjs", "js");
$Co->addJs("/js/lougis/lib/comments.ui.extjs.js");
if ( isset($_REQUEST['debug']) && strpos(PATH_SERVER, 'development') != false ) {
	$Co->outputFilesScriptTags();
} else {
	$Co->outputScriptHtml();
}
?>