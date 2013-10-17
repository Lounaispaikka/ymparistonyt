<?php
namespace Lougis\utility\ymparisto;

require_once(PATH_SERVER.'abstracts/Utility.php');
require_once(PATH_SERVER.'utility/PHPMailer/class.phpmailer.php');

class Kysely extends \Lougis\abstracts\Utility {
	
	public static function getAllToimenpiteet( ) {
		
		$res = array();
			
		$sql  = "SELECT ohjelma.title AS ohjelmatavoite, lcp.*
					FROM lougis.cms_page AS lcp
					LEFT OUTER JOIN lougis.cms_page AS ohjelma ON ohjelma.id = lcp.parent_id
					WHERE lcp.site_id = 'ymparisto' AND lcp.page_type = 'toimenpide'
					ORDER BY ohjelma.title, lcp.title;";
					
		$Pg = new \Lougis_cms_page();
		$Pg->query($sql);
		while( $Pg->fetch() ) $res[] = $Pg->toArray();
		
		return $res;
		
	}
	
	public static function loadToimenpideData( $kyselyId = null ) {
	
		$res = array();
			
		$sql  = "SELECT ohjelma.title AS ohjelmatavoite, lcp.*, ytk.id AS kysely_id, ytk.sending_date, ytk.closing_date, ytk.reminder_interval_days 
					FROM lougis.cms_page AS lcp
						LEFT OUTER JOIN ymparisto.arviointi_kysely AS atk ON atk.page_id = lcp.id
						LEFT OUTER JOIN lougis.cms_page AS ohjelma ON ohjelma.id = lcp.parent_id
					WHERE lcp.site_id = 'ymparisto' AND lcp.page_type = 'toimenpide'";
		if ( !empty($kyselyId) ) $sql .= " AND ytk.id = {$kyselyId} ";
		$sql .=	"ORDER BY ohjelma.title, lcp.title;";
		
		$Pg = new \Lougis_cms_page();
		$Pg->query($sql);
		while ( $Pg->fetch() ) {
			$Data = array();
			$Data["text"] = $Pg->ohjelmatavoite.' - '.$Pg->title;
			if ( empty($Pg->kysely_id) ) {
				$Data["text"] .= " <span style='color:red'>(kysely puuttuu)</span>";
				$Data["sending_date"] = date("Y/m/d", time()+(60*60*24*60));
				$Data["closing_date"] = date("Y/m/d", time()+(60*60*24*90));
				$Data["reminder_interval_days"] = 7;
			} else {
				$Data["sending_date"] = date("Y/m/d", strtotime($Pg->sending_date));
				$Data["closing_date"] = date("Y/m/d", strtotime($Pg->closing_date));
				$Data["reminder_interval_days"] = intval($Pg->reminder_interval_days);
			}
			$Data["leaf"] = true;
			$Data["title"] = $Pg->title;
			$Data["page_id"] = (int) $Pg->id;
			$Data["kysely_id"] = $Pg->kysely_id;
			$Data["ohjelmatavoite"] = $Pg->ohjelmatavoite;
			if ( !empty($kyselyId) ) return $Data;
			$res[] = $Data;
		}
		
		return $res;
	
	}
	
	
    public static function sendKyselyMessages( $kyselyId ) {

		$Kysely = new \Ymparisto_arviointi_kysely( $kyselyId );
		$Kierros = new \Ymparisto_arviointi_kierros( $Kysely->kierros_id );
		
		$Asiantuntijat = $Kysely->getAsiantuntijatArray();
		
		$title = $Kysely->getKyselyTitle();
		$msgTitle = "Ympäristöohjelman \"{$title}\"-toimenpiteen arviointikysely";
		foreach($Asiantuntijat as $Asa) {
		
			$Vas = new \Ymparisto_arviointi_vastaus();
			$Vas->kysely_id = $Kysely->id;
			$Vas->user_id = $Asa['id'];
			$Vas->find(true);
			
			if ( $Vas->save() ) {
				
				$url = $Vas->getVastausUrl();
				$aurl = '<a href="'.$url.'">'.$url.'</a>';
				$tplReplace = array(
					"[TOIMENPITEEN_NIMI]" => $title,
					"[ARVIOINTI_SULKEUTUU_PVM]" => date('d.m.Y', strtotime($Kierros->closing_date)),
					"[LINKKI_ARVIOINTIKYSELYYN]" => $aurl
				);
				$msgText = self::processTpl($Kierros->email_tpl, $tplReplace);
				
				$mail = new \PHPMailer();
				$mail->PluginDir = PATH_SERVER."utility/PHPMailer/";
				$mail->CharSet = 'utf-8';
				$mail->SetFrom('ymparisto@lounaispaikka.fi', 'Ympäristö NYT');
				$mail->Subject = $msgTitle;
				//$mail->AltBody = $msgText;
				//$msgText = str_replace($url, $aurl, $msgText);
				
				$mail->MsgHTML( $msgText );
				$mail->AddAddress($Asa['email'], $Asa['firstname'].' '.$Asa['lastname']);
				$mail->AddBCC('pyry@lounaispaikka.fi');
				
				if( !$mail->Send() ) {
					$Vas->delete();
					throw new \Exception("Sähköpostin lähetys epäonnistui: " . $mail->ErrorInfo);
				}
				$Vas->sent_date = date(DATE_W3C);
				$Vas->save();
				
			} else {
				throw new \Exception("Kyselykutsun tallennus epäonnistui: " . $Vas->_lastError);
			}
			
			
		}
		/* */
		
		return true;
		
	}
	
    public static function sendKyselyMuistutukset( $kyselyId ) {

		$Kysely = new \Ymparisto_arviointi_kysely( $kyselyId );
		$Kierros = new \Ymparisto_arviointi_kierros( $Kysely->kierros_id );
		
		$Asiantuntijat = $Kysely->getAsiantuntijatArray();
		
		$title = $Kysely->getKyselyTitle();
		$msgTitle = "Muistutus: Ympäristöohjelman \"{$title}\"-toimenpiteen arviointikysely";
		foreach($Asiantuntijat as $Asa) {
		
			$Vas = new \Ymparisto_arviointi_vastaus();
			$Vas->kysely_id = $Kysely->id;
			$Vas->user_id = $Asa['id'];
			$Vas->find(true);
			
			if ( empty($Vas->arvio_arvo) && empty($Vas->reminder2_date) ) {
				
				$url = $Vas->getVastausUrl();
				$aurl = '<a href="'.$url.'">'.$url.'</a>';
				$tplReplace = array(
					"[TOIMENPITEEN_NIMI]" => $title,
					"[ARVIOINTI_SULKEUTUU_PVM]" => date('d.m.Y', strtotime($Kierros->closing_date)),
					"[LINKKI_ARVIOINTIKYSELYYN]" => $aurl
				);
				$msgText = self::processTpl($Kierros->email_tpl, $tplReplace);
				
				$mail = new \PHPMailer();
				$mail->PluginDir = PATH_SERVER."utility/PHPMailer/";
				$mail->CharSet = 'utf-8';
				$mail->SetFrom('ymparisto@lounaispaikka.fi', 'Ympäristö NYT');
				$mail->Subject = $msgTitle;
				//$mail->AltBody = $msgText;
				//$msgText = str_replace($url, $aurl, $msgText);
				
				$mail->MsgHTML( $msgText );
				$mail->AddAddress($Asa['email'], $Asa['firstname'].' '.$Asa['lastname']);
				$mail->AddBCC('pyry@lounaispaikka.fi');
				
				if( !$mail->Send() ) {
					throw new \Exception("Sähköpostin lähetys epäonnistui: " . $mail->ErrorInfo);
				}
				if ( empty($Vas->reminder1_date) ) {
					$Vas->reminder1_date = date(DATE_W3C);
				} else {
					$Vas->reminder2_date = date(DATE_W3C);
				}
				$Vas->save();
				sleep(1);
			}
			
			
		}
		/* */
		
		return true;
		
	}
	
	private static function processTpl( $Tpl, $ReplaceValues ) {
		
		$String = $Tpl;
		foreach($ReplaceValues as $Key => $Val) {
			$String = str_replace($Key, $Val, $String);
		}
		return $String;
		
	}
	
    public static function getTitleTpl() {
    	
		$titleTpl = "Ympäristöohjelman \"{0}\"-toimenpiteen arviointikysely";
    	
    	if ( func_num_args() > 0 ) {
    		$args = func_get_args();
    		foreach($args as $key => $val) {
    			$titleTpl = str_replace('{'.$key.'}', $val, $titleTpl);
    		}
    	}
    	
    	return $titleTpl;
    
    }
    
    public static function getMsgTpl() {
    	/*
		$msgTpl =    "Hei,\r\n\r\n"
					."Pyydämme osallistuminaan Ympäristöohjelman \"{0}\"-toimenpiteen etenemisen arviointikyselyyn {1} mennessä.\r\n\r\n"
					."Voit arvioida toimenpiteen etenemistä klikkaamalla seuraavaa linkkiä:\r\n\r\n"
					."[linkki arviointikyselyyn]";
		*/
		$msgTpl =    "Hei,\r\n\r\n"
					."Olet ystävällisesti lupautunut asiantuntijaksi ympäristöohjelman seurantaan.\r\n\r\n"
					."Vuoden 2012 seurantakierros Ympäristö Nyt –palvelussa on käynnistynyt. Ole hyvä ja arvioi toimenpiteen [TOIMENPITEEN_NIMI] etenemistä [ARVIOINTI_SULKEUTUU_PVM] mennessä.\r\n\r\n"
					."Siirry arviointilomakkeeseen tästä linkistä: [LINKKI_ARVIOINTIKYSELYYN]";
		
    	if ( func_num_args() > 0 ) {
    		$args = func_get_args();
    		foreach($args as $key => $val) {
    			$msgTpl = str_replace('{'.$key.'}', $val, $msgTpl);
    		}
    	}
					
		return $msgTpl;
    
    }

}
?>