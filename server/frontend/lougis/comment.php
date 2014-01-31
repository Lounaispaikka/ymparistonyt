<?php
namespace Lougis\frontend\lougis;

require_once(PATH_SERVER.'abstracts/Frontend.php');

require_once(PATH_SERVER."utility/PHPMailer/class.phpmailer.php");

class Comment extends \Lougis\abstracts\Frontend {

	public function newComment() {
		
		global $Site, $Session;
		
		try {
			
			$CmData = $_REQUEST['comment'];
			
			$Required = array('nick', 'msg', 'check');
			foreach($Required as $ReqVal) {
				if ( empty($CmData[$ReqVal]) || strlen($CmData[$ReqVal]) < 2 ) throw new \Exception("Lomakkeessa tyhjä kenttä. Kaikki kentät ovat pakollisia");
			}
			if ( strlen($CmData['nick']) > 200 || strlen($CmData['title']) > 200 ) throw new \Exception("Liian pitkä otsikko tai nimi.");
			if ( empty($CmData['check']) || $CmData['check'] != date('Y') ) throw new \Exception("Virheellinen vastaus tarkistuskysymykseen! Kirjoita vastauskenttään luku ".date('Y'));
			if ( empty($_SESSION['comment_page_id']) ) throw new \Exception("Tekninen virhe. Ota yhteyttä sivuston ylläpitoon");
			
			
			$Cm = new \Lougis_cms_comment();
			$Cm->setFrom($CmData);
			$Cm->page_id = $_SESSION['comment_page_id'];
			if(isset($_SESSION['comment_part_id'])) $Cm->part_id = $_SESSION['comment_part_id'];
			$Cm->lang_id = $_SESSION['lang_id'];
			$Cm->msg = strip_tags($CmData['msg']);
			
			if ( !$Cm->save() ) throw new \Exception("Tekninen virhe. Ota yhteyttä sivuston ylläpitoon");
			
			$res = array(
				"success" => true,
				"comment" => $Cm->toArray()
			);
             //           $mail_msg = "Viestin otsikko: ".$CmData['title']."\n\n".$CmData['msg']."\n\n (Tämä on automaattinen viesti, älä vastaa).";
			mail('ville@lounaispaikka.fi, merja.haliseva-soila@ely-keskus.fi, nina.myllykoski@ely-keskus.fi, tapio.saario@ely-keskus.fi', 'Uusi kommentti sivustolla YmparistoNyt', $mail_msg, 'From: ymparisto@lounaispaikka.fi');
			//$cm_det = $Site->getCommentDetails($Cm->id);
			//devlog($cm_det);
			try {
				$mail = new \PHPMailer();
				//$mail->PluginDir = PATH_SERVER."utility/PHPMailer/";
				$mail->SetFrom('ymparisto@lounaispaikka.fi', 'Ymparisto NYT');
				$mail->Subject = "Uusi kommentti sivustolla YmparistoNyt";
				$mail->MsgHTML( "Viestin otsikko: ".$CmData['title']."\n\n".$CmData['msg']."\n\n (Tämä on automaattinen viesti, älä vastaa)." );
				$mail->FromName = "<Ymparisto Nyt ymparisto@lounaispaikka.fi>";
				$mail->AltBody = "Viestin otsikko: ".$CmData['title']."\n\n".$CmData['msg']."\n\n (Tämä on automaattinen viesti, älä vastaa).";
				$mail->AddAddress('ville@lounaispaikka.fi');
				$mail->AddAddress('merja.haliseva-soila@ely-keskus.fi');
				$mail->AddAddress('nina.myllykoski@ely-keskus.fi');
				$mail->AddAddress('tapio.saario@ely-keskus.fi');
				$mail->IsHTML(true);
				$mail->Send();;
			} catch (phpmailerException $e) {
				die("Sähköpostin lähetys epäonnistui: ".$e->errorMessage());
			}
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function likeMsg() {
		
		global $Site, $Session;
		
		try {
			
			if ( !isset($_SESSION['rated_comments']) ) $_SESSION['rated_comments'] = array();
			
			$MsgId = $_REQUEST['msgid'];
			if ( empty($MsgId) || in_array($MsgId, $_SESSION['rated_comments']) ) throw new \Exception("Tämä kommentti jo arvioitu.");
			
			$Cm = new \Lougis_cms_comment($_REQUEST['msgid']);
			if ( $_REQUEST['likeval'] > 0 ) {
				$Cm->likes = $Cm->likes+1;
			} else {
				$Cm->dislikes = $Cm->dislikes+1;
			}
			
			if ( !$Cm->save() ) throw new \Exception("Tekninen virhe. Ota yhteyttä sivuston ylläpitoon");
			array_push($_SESSION['rated_comments'], $MsgId);
			$res = array(
				"success" => true,
				"comment" => $Cm->toArray()
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function replyBoxHtml() {
		
		$MsgId = $_REQUEST['msgid'];
		if ( empty($MsgId) ) {
			echo "Tekninen virhe!";
			die;
		}
		$Rules = \Lougis_cms_comment::getRules();
		?>
<img class="closereplybox" src="/img/close.png" alt="" title="Sulje" onclick="closeReplyBox(<?=$MsgId?>);" />
<h2>Vastaa viestiin</h2>
<div id="replyform<?=$MsgId?>"></div>
		<?
		echo $Rules;
		
	}
	
	
}
?>
