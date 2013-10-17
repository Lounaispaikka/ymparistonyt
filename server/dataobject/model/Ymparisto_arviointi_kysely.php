<?php
/**
 * Table Definition for ymparisto.arviointi_kysely
 */
require_once 'DB/DataObject.php';

class Ymparisto_arviointi_kysely extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'ymparisto.arviointi_kysely';      // table name
    public $id;                              // int4(4)  not_null default_nextval%28arviointi_kysely_id_seq%29 primary_key
    public $kierros_id;                      // int4(4)  not_null unique_key multiple_key
    public $page_id;                         // int4(4)  not_null unique_key multiple_key
    public $sent_date;                       // timestamptz(8)  
    public $closed;                          // bool(1)  default_false
    public $published;                       // bool(1)  default_false

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Ymparisto_arviointi_kysely',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    public function getKyselyTitle() {
	    
		$sql  = "SELECT ohjelma.title || ' - ' || lcp.title AS ohjelmatitle
				FROM ymparisto.arviointi_kysely AS yak
					LEFT JOIN lougis.cms_page AS lcp ON lcp.id = yak.page_id
					LEFT JOIN lougis.cms_page AS ohjelma ON ohjelma.id = lcp.parent_id
				WHERE yak.id = {$this->id}
				ORDER BY ohjelmatitle;";
	    $Do = new \DB_DataObject();
	    $Do->query($sql);
	    $Do->fetch();
	    return $Do->ohjelmatitle;
	    
    }
    
    public function addAsiantuntija( $asId, $admin = false ) {
	    
    	$Grp = new \Ymparisto_arviointi_ryhma();
    	$Grp->kysely_id = $this->id;
    	$Grp->user_id = $asId;
    	$Count = $Grp->count();
    	if ( $Count != 0 ) return false;
    	$Grp->admin = $admin;
		return $Grp->save();
		
    }
    
    public function saveAsiantuntijat( $asiantuntijaArray ) {
    	
    	$this->clearAsiantuntijat();
    	
    	foreach($asiantuntijaArray as $usrObj) {
    		
    		if ( empty($usrObj->firstname) || empty($usrObj->lastname) || empty($usrObj->email) ) throw new \Exception("Asiantuntijoiden tiedossa puutteita. Tarkista asiantuntijoiden tiedot!");
    		
    		$Usr = \Lougis_user::getByEmail($usrObj->email);
    		if ( empty($Usr->id) ) {
    			$Usr->setFrom($usrObj);
    			$Usr->date_created = date(DATE_W3C);
    			if ( !$Usr->save() ) throw new \Exception("Järjestelmävirhe! Uutta käyttäjää ei voitu tallentaa. Ota yhteyttä ylläpitoon.");
    		}
	    	$Grp = new \Ymparisto_arviointi_ryhma();
	    	$Grp->kysely_id = $this->id;
	    	$Grp->user_id = $Usr->id;
	    	$Grp->admin = ( $usrObj->admin ) ? true : false;
    		if ( !$Grp->save() ) throw new \Exception("Järjestelmävirhe! Käyttäjää ei voitu tallentaa asiantuntijaksi. Ota yhteyttä ylläpitoon.");
    		
    	}
    	
    	
    }
    
    public function clearAsiantuntijat() {
    
    	$Grp = new \Ymparisto_arviointi_ryhma();
    	$Grp->kysely_id = $this->id;
    	if ( $Grp->count() > 0 ) return $Grp->delete();
    	return true;
    
    }
    
    public function getAdmins() {
    	
    	if ( empty($this->page_id) ) return array();
    	
    	$Admins = array();    	
    	$Grp = new \Ymparisto_arviointi_ryhma();
    	$Grp->kysely_id = $this->id;
    	$Grp->admin = true;
    	$Grp->orderBy('id');
    	$Grp->find();
    	while( $Grp->fetch() ) {
    		$Usr = new \Lougis_user($Grp->user_id);
    		$Usc = $Usr->toArray();
    		unset($Usc['password']);
    		$Admins[] = $Usc;
    	}
    	
    	return $Admins;
    
    }
    
    public function getAsiantuntijatArray() {
    	
    	$Asiantuntijat = array();    	
    	$Grp = new \Ymparisto_arviointi_ryhma();
    	$Grp->kysely_id = $this->id;
    	$Grp->orderBy('id');
    	$Grp->find();
    	while( $Grp->fetch() ) {
    		$Usr = new \Lougis_user($Grp->user_id);
    		$Usc = $Usr->toArray();
    		unset($Usc['password']);
    		$Usc['admin'] = ( $Grp->admin == 't' ) ? true : false;
    		$Asiantuntijat[] = $Usc;
    	}
    	
    	return $Asiantuntijat;
    
    }
    
    public function getArvioinnitArray() {
    	
    	$Arvioinnit = array();    	
    	$Vas = new \Ymparisto_arviointi_vastaus();
    	$Vas->kysely_id = $this->id;
    	$Vas->orderBy('sent_date, arvio_date');
    	$Vas->find();
    	while( $Vas->fetch() ) {
    		$Usr = new \Lougis_user($Vas->user_id);
    		$Vaa = array();
    		/*
    		$Vaa['id'] = intval($Vas->id);
    		$Vaa['user_id'] = $Usr->id;
    		$Vaa['arvio_arvo'] = $Vas->arvio_arvo;
    		*/
    		$Vaa = $Vas->toArray();
    		$Vaa['user_name'] = $Usr->getFullname().' - '.$Usr->organization;
    		$Vaa['reminders'] = 0;
    		if ( !empty($Vas->reminder1_date) ) $Vaa['reminders']++;
    		if ( !empty($Vas->reminder2_date) ) $Vaa['reminders']++;
    		$Vaa['sent_date'] = strtotime($Vas->sent_date);
    		$Vaa['arvio_date'] = ( !empty($Vas->arvio_date) ) ? strtotime($Vas->arvio_date) : null;
    		
    		$Arvioinnit[] = $Vaa;
    	}
    	return $Arvioinnit;
    
    }

    public static function hasPublishedArviointi( $PageId ) {
	    
	    $sql = "SELECT kysely.*, COUNT(vastaus.id) AS vastauksia_yht
					FROM ymparisto.arviointi_kysely AS kysely
					JOIN ymparisto.arviointi_kierros AS kierros ON kierros.id = kysely.kierros_id
					JOIN ymparisto.arviointi_vastaus AS vastaus ON vastaus.kysely_id = kysely.id
				WHERE kysely.page_id = {$PageId} 
				AND vastaus.arvio_arvo IS NOT NULL
				AND kierros.published = TRUE
				AND kierros.closed = TRUE
				GROUP BY kysely.id, kysely.kierros_id;";
	    $Kysely = new \Ymparisto_arviointi_kysely();
	    $Kysely->query($sql);
	    $Kysely->fetch();
	    if ( $Kysely->page_id == $PageId && intval($Kysely->vastauksia_yht) > 0 ) return $Kysely;
	    
	    return false;
	    
    }
    
}
