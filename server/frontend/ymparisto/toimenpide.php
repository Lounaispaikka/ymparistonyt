<?php
namespace Lougis\frontend\ymparisto;

require_once(PATH_SERVER.'abstracts/Frontend.php');
require_once(PATH_SERVER.'utility/Ymparisto/Kysely.php');

class Toimenpide extends \Lougis\abstracts\Frontend {

	public function __construct() {
	
		global $Site, $User;
		
		if ( $Site->id != 'ymparisto' ) throw new \Exception('Site error');
		if ( !$User->isLogged() ) throw new \Exception('Auth error');
	
	}
	
	public function testTitle() {
		
		
		$Kys = new \Ymparisto_arviointi_kysely($_REQUEST['kysely_id']);
		$title = $Kys->getKyselyTitle();
		print_r_html($title);
		print_r_html($Kys);
		
	}
	
	public function arviointikierrosTree() {
		
		global $Site;
		
		try {
			
			$Kierrokset = array();
			$Kierros = new \Ymparisto_arviointi_kierros();
			$Kierros->orderBy("id");
			$Kierros->find();
			while($Kierros->fetch()) {
				$Kyselyt = $Kierros->getKyselyt();
				$Leaf = true;
				if ( count($Kyselyt) > 0 ) {
					$Leaf = false;
					$Kya = array();
					foreach($Kyselyt as $Kys) {
						$Kya[] = array(
							"text" => $Kys->ohjelmatitle,
							"leaf" => true,
							"expanded" => true,
							"kysely_id" => intval($Kys->id),
							"type" => 'kysely'	
						);
					}
				}
				
				$Kia = array(
					"text" => $Kierros->title1,
					"leaf" => $Leaf,
					"expanded" => false,
					"kierros_id" => intval($Kierros->id),
					"type" => 'kierros'	
				);
				if ( !$Leaf ) $Kia['children'] = $Kya;
				$Kierrokset[] = $Kia;
			}
			
			$res = $Kierrokset;
			
			//$res = \Lougis\utility\ymparisto\Kysely::loadToimenpideData();
			
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function saveKierros( $Kierros = null, $Return = false ) {
		
		global $Site;
		
		try {
			
			$Kd = $_REQUEST['kierros'];
			if ( empty($Kd['title1']) ) throw new \Exception("Kierroksen nimi on pakollinen");
			if ( empty($Kd['closing_date']) ) throw new \Exception("Kierroksen päättymispäivämäärä on pakollinen");
			if ( trim(strtolower($Kd['title1'])) == 'arviointikierros -' ) throw new \Exception("Arviointikierros tarvitsee kuvaamamman nimen kuin '{$Kd['title1']}'");
			if ( strpos($Kd['email_tpl'], '[LINKKI_ARVIOINTIKYSELYYN]') === false  ) throw new \Exception("Sähköpostipohjan pitää sisältää [LINKKI_ARVIOINTIKYSELYYN] tekstin jossain kohtaa.");
			
			switch(true) {
				case !empty($Kierros) && !empty($Kierros->created_date) :
				break;
				case empty($Kd['id']):
					$Kierros = new \Ymparisto_arviointi_kierros();
					$Kierros->created_date = date(DATE_W3C);
					$Msg = "Uusi arviointikierros on talletettu";
				break;
				default: 
					$Kierros = new \Ymparisto_arviointi_kierros($Kd['id']);
					if ( empty($Kierros->created_date) ) throw new \Exception("Tekninen virhe! Ota yhteyttä ylläpitoon.");
					$Msg = "Arviointikierroksen tiedot päivitetty";
				break;
			}
			$Kierros->setFrom($Kd);
			$Kierros->reminder1_date = ( !empty($Kd['reminder1_date']) ) ? date(DATE_W3C, strtotime($Kd['reminder1_date'])) : null;
			$Kierros->reminder2_date = ( !empty($Kd['reminder2_date']) ) ? date(DATE_W3C, strtotime($Kd['reminder2_date'])) : null;
			$Kierros->closing_date = date(DATE_W3C, strtotime($Kd['closing_date']));
			
			if ( !empty($Kierros->reminder1_date) && !empty($Kierros->closing_date) && strtotime($Kierros->reminder1_date) > strtotime($Kierros->closing_date) ) throw new \Exception("1. muistutus ei voi olla kyselyn päättymisen jälkeen");
			if ( !empty($Kierros->reminder2_date) && !empty($Kierros->closing_date) && strtotime($Kierros->reminder2_date) > strtotime($Kierros->closing_date) ) throw new \Exception("2. muistutus ei voi olla kyselyn päättymisen jälkeen");
			if ( !empty($Kierros->reminder1_date) && !empty($Kierros->reminder2_date) && strtotime($Kierros->reminder1_date) > strtotime($Kierros->reminder2_date) ) throw new \Exception("1. muistutus ei voi olla 2. muistutuksen jälkeen");
			
			if ( !$Kierros->save() ) throw new \Exception("Tekninen virhe! Ota yhteyttä ylläpitoon. Virhe: <br/>".$Kierros->_lastError->userinfo);
			
			$Tids = explode(',', $_REQUEST['toimenpiteet']);
			foreach($Tids as $PageId) {
				$Kierros->addKysely( $PageId );
			}
			
			$res = array(
				"success" => true,
				"msg" => $Msg,
				"kierros_id" => $Kierros->id
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		if ( $Return ) return $res;
		
		$this->jsonOut($res);
		
	}
	
	public function getKierrosDetails() {
		
		
		try {
			
			$Kierros = new \Ymparisto_arviointi_kierros($_REQUEST['kierros_id']);
			if ( empty($Kierros->created_date) ) throw new \Exception("Tekninen virhe! Kierrosta ei voitu ladata. Ota yhteyttä ylläpitoon.");
			
			$Ka = $Kierros->toArray();
			$Kyselyt = $Kierros->getKyselyt( true );
			$Ka["kyselyt"] = $Kyselyt;
			$Kyids = array();
			foreach($Kyselyt as $Kysely) {
				$Kyids[] = $Kysely['page_id'];
			}
			$Ka["kysely_idt"] = $Kyids;
			
			$Ta = \Lougis\utility\ymparisto\Kysely::getAllToimenpiteet();
			
			$res = array(
				"success" => true,
				"kierros" => $Ka
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function aloitaKierros() {
		
		
		try {
			
			$Kierros = new \Ymparisto_arviointi_kierros($_REQUEST['kierros_id']);
			if ( empty($Kierros->created_date) ) throw new \Exception("Tekninen virhe! Kierrosta ei voitu ladata. Ota yhteyttä ylläpitoon.");
			/*
			$res = $this->saveKierros($Kierros, true);
			if ( $res['success'] !== true )  throw new \Exception($res['msg']);
			*/
			if ( !empty($Kierros->reminder1_date) && strtotime($Kierros->reminder1_date) < time() ) throw new \Exception("1. muistutuksen päivämäärä on historiassa.");
			if ( !empty($Kierros->reminder1_date) && !empty($Kierros->closing_date) && strtotime($Kierros->reminder1_date) > strtotime($Kierros->closing_date) ) throw new \Exception("1. muistutus ei voi olla kyselyn päättymisen jälkeen");
			if ( !empty($Kierros->reminder2_date) && !empty($Kierros->closing_date) && strtotime($Kierros->reminder2_date) > strtotime($Kierros->closing_date) ) throw new \Exception("2. muistutus ei voi olla kyselyn päättymisen jälkeen");
			if ( !empty($Kierros->reminder1_date) && !empty($Kierros->reminder2_date) && strtotime($Kierros->reminder1_date) > strtotime($Kierros->reminder2_date) ) throw new \Exception("1. muistutus ei voi olla 2. muistutuksen jälkeen");
			
			$Kyselyt = $Kierros->getKyselyt();
			foreach($Kyselyt as $Kys) {
				if ( $Kys->asiantuntijoita < 1 ) throw new \Exception("Kierroksessa on mukana toimenpiteitä, joissa ei ole lainkaaan asiantuntijoita. Lisää asiantuntijat kaikkiin toimenpiteisiin ennen kierroksen aloittamista.");
			}
			$cmd = "nohup nice ". PATH_SCRIPTS. "ymparisto/ymparisto_send_arviointi_viestit.php >/tmp/ymparisto.log &";
			shell_exec($cmd);
			$Kierros->published = true;
			$Kierros->in_process = true;
			$Kierros->notes = "Viestien lähetys kesken. Kierros alkanut ".date("d.m.Y H:i:s");
			if ( !$Kierros->save() ) throw new \Exception("Tekninen virhe! Ota yhteyttä ylläpitoon. Virhe: <br/>".$Kierros->_lastError->userinfo);
			
			
			$res = array(
				"success" => true,
				"msg" => "Kierros aloitettu. Kierroksen tietoja ei voi muokata ennen kuin kierroksen viestit ovat lähetetty."
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function suljeKierros() {
		
		
		try {
			
			$Kierros = new \Ymparisto_arviointi_kierros($_REQUEST['kierros_id']);
			if ( empty($Kierros->created_date) ) throw new \Exception("Tekninen virhe! Kierrosta ei voitu ladata. Ota yhteyttä ylläpitoon.");
			
			$Kierros->closed = true;
			$Kierros->notes = "Kierros suljettu ".date("d.m.Y H:i:s");
			if ( !$Kierros->save() ) throw new \Exception("Tekninen virhe! Ota yhteyttä ylläpitoon. Virhe: <br/>".$Kierros->_lastError->userinfo);
			
			
			$res = array(
				"success" => true,
				"msg" => "Kierros suljettu. Toimenpiteiden arviointeja ei voida enää muokata."
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function poistaKierros() {
		
		
		try {
			
			$Kierros = new \Ymparisto_arviointi_kierros($_REQUEST['kierros_id']);
			if ( empty($Kierros->created_date) ) throw new \Exception("Tekninen virhe! Kierrosta ei voitu ladata. Ota yhteyttä ylläpitoon.");
			
			$Kyselyt = $Kierros->getKyselyt();
			if ( count($Kyselyt) > 0 ) throw new \Exception("Kierrosta ei voida poistaa jos se sisältää kyselyjä. Poista kaikki kyselyt ennen kuin poistat kierroksen.");
			
			if ( !$Kierros->delete() ) throw new \Exception("Tekninen virhe! Kierrosta ei voitu poistaa. Ota yhteyttä ylläpitoon.");
			
			$res = array(
				"success" => true,
				"msg" => 'Kierros "'.$Kierros->title1.'" poistettu.'
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function getToimenpiteetStoreData() {
		
		$res = array();
			
		$sql  = "SELECT ohjelma.title || ' - ' || lcp.title AS title, lcp.id AS page_id
			FROM lougis.cms_page AS lcp
			LEFT JOIN lougis.cms_page AS ohjelma ON ohjelma.id = lcp.parent_id
			WHERE lcp.site_id = 'ymparisto' AND lcp.page_type = 'toimenpide'
			ORDER BY title;";
					
		$Pg = new \Lougis_cms_page();
		$Pg->query($sql);
		while( $Pg->fetch() ) {
			$res[] = array(
				'title' => $Pg->title,
				'page_id' => intval($Pg->page_id),
			);
		}
		
		$this->jsonOut($res);
		
	}
	
	public function listUsers() {
	
		$Users = array();
		$Usr = new \Lougis_user();
		$Usr->orderBy('email');
		$Usr->find();
		while( $Usr->fetch() ) {
			$Usa = array(
				"firstname" => $Usr->firstname,
				"lastname" => $Usr->lastname,
				"email" => $Usr->email,
				"organization" => $Usr->organization,
				"admin" => false
			);
			$Users[] = $Usa;
		}
	
		$this->jsonOut($Users);
	
	}
	
	public function listOrganizations() {
	
		$Users = array();
		$Usr = new \Lougis_user();
		$Usr->query("SELECT DISTINCT organization FROM lougis.user GROUP BY organization");
		/*
		$Usr->orderBy('organization');
		$Usr->find();
		*/
		while( $Usr->fetch() ) {
			$Usa = array(
				"organization" => $Usr->organization
			);
			$Users[] = $Usa;
		}
	
		$this->jsonOut($Users);
	
	}
	
	
	public function getKyselyDetails(  ) {
	
		$Kysely = new \Ymparisto_arviointi_kysely($_REQUEST['kysely_id']);
		$Asiantuntijat = $Kysely->getAsiantuntijatArray();
		$Arvioinnit = $Kysely->getArvioinnitArray();
		
		$res = array(
			"kysely" => $Kysely->toArray(),
			"asiantuntijat" => $Asiantuntijat,
			"arvioinnit" => $Arvioinnit
		);
		
		$this->jsonOut($res);
	
	}
	
	public function getVastausData() {
		
		$Vastaus = new \Ymparisto_arviointi_vastaus($_REQUEST['vastaus_id']);
		$User = new \Lougis_user($Vastaus->user_id);
		$Vaa = $Vastaus->toArray();
		$Vaa['user'] = $User->toArray();
		$Vaa['url_private'] = $Vastaus->getVastausUrl();
		$res = array(
			"success" => true,
			"vastaus" => $Vaa
		);
		
		$this->jsonOut($res);
		
	}
	
	public function saveKysely() {
		
		global $Site, $User;
		
		try {
		
			
			if ( !isset($_REQUEST['kysely']['kierros_id']) || empty($_REQUEST['kysely']['kierros_id']) ) throw new \Exception("Tekninen virhe!");
			if ( !isset($_REQUEST['kysely']['page_id']) || empty($_REQUEST['kysely']['page_id']) ) throw new \Exception("Tekninen virhe!");
			
			$Kysely = new \Ymparisto_arviointi_kysely($_REQUEST['kysely']['id']);
			
			if ( !$Kysely->save() ) throw new \Exception("Järjestelmävirhe! Kyselyä ei voitu tallentaa. Ota yhteyttä ylläpitoon.");
			
			$Kysely->saveAsiantuntijat( json_decode($_REQUEST['asiantuntijat']) );
			
			$res = array(
				"success" => true,
				"msg" => "Kysely tallennettu onnistuneesti!",
				"kysely_id" => $Kysely->id,
				"kierros_id" => $Kysely->kierros_id,
				"page_id" => $Kysely->page_id
			);
		
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function poistaKysely() {
		
		global $Site, $User;
		
		try {
			
			$Kysely = new \Ymparisto_arviointi_kysely($_REQUEST['kysely_id']);
			if ( empty($Kysely->page_id) ) throw new \Exception("Tekninen virhe! Kyselyä ei voitu ladata. Ota yhteyttä ylläpitoon.");
			$Vastaus = new \Ymparisto_arviointi_vastaus();
			$Vastaus->kysely_id = $Kysely->id;
			$Vastaus->whereAdd('arvio_arvo IS NOT NULL');
			if ( $Vastaus->count() > 0 ) throw new \Exception("Kyselyyn liittyy jo asiantuntijoiden vastauksia. Vain tekninen ylläpito voi poistaa tämän kyselyn");
			
			if ( !$Kysely->delete() ) throw new \Exception("Järjestelmävirhe! Kyselyä ei voitu poistaa. Ota yhteyttä ylläpitoon.");
			
			$res = array(
				"success" => true,
				"msg" => "Kysely poistettu onnistuneesti!"
			);
		
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function saveArvio() {
		
		global $Site;
		
		try {
			
			$Vas = new \Ymparisto_arviointi_vastaus($_REQUEST['vastaus_id']);
			if ( empty($Vas->user_id) ) throw new \Exception("Vastausta ei voitu ladata tai tallentaa!");
			if ( $Vas->user_id != $_SESSION['user_id'] ) throw new \Exception("Käyttöoikeusvirhe!");
			$Kysely = new \Ymparisto_arviointi_kysely($Vas->kysely_id);
			$Kierros = new \Ymparisto_arviointi_kierros($Kysely->kierros_id);
			if ( empty($Kierros->created_date) ) throw new \Exception("Kyselyä ei voitu ladata tai tallentaa!");
			//if ( time() > strtotime($Kierros->closing_date) ) throw new \Exception("Kyselyn vastausaika on päättynyt.");
			
			$arvo = doubleval(intval($_REQUEST['arvio_arvo'])/10);
			$Vas->arvio_arvo = $arvo;
			$Vas->arvio_perustelu = $_REQUEST['arvio_perustelu'];
			$Vas->arvio_date = date(DATE_W3C);
			if ( !$Vas->save() ) throw new \Exception("Vastausta ei voitu tallentaa!");
			
			$res = array(
				"success" => true,
				"msg" => "Toimenpiteen arviointi tallennettu onnistuneesti!"
			);
		
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function loadKyselyForPage( $PageId ) {
	
		$Pg = new \Lougis_cms_page($PageId);
		if ( empty($Pg->page_type) || $Pg->page_type != 'toimenpide' ) throw new \Exception("Järjestelmävirhe! Toimenpidettä ei voitu ladata. Ota yhteyttä ylläpitoon.");
		$Kysely = new \Ymparisto_arviointi_kysely();
		$Kysely->get('page_id', $Pg->id);
		return $Kysely;
		
	}
	
	public function sendKyselyMessages(  ) {
		
		global $Site, $User;
		
		try {
			
			$Kysely = new \Ymparisto_arviointi_kysely($_REQUEST['kysely_id']);
			$Asiantuntijat = $Kysely->getAsiantuntijatArray();
			if ( count($Asiantuntijat) < 1 )  throw new \Exception("Kyselyssä ei ole yhtään tallennettua asiantuntijaa. Tallenna ensin asiantuntijat.");
			
			if ( !\Lougis\utility\ymparisto\Kysely::sendKyselyMessages($_REQUEST['kysely_id']) ) throw new \Exception("Kyselyä ei voitu lähettää");
			
			$res = array(
				"success" => true,
				"msg" => "Kysely lähetetty asiantuntijoille.",
				"kysely_id" => $_REQUEST['kysely_id']
			);
		
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
}
?>