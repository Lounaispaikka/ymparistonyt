<?php
/**
 * Table Definition for ymparisto.arviointi_vastaus
 */
require_once 'DB/DataObject.php';

class Ymparisto_arviointi_vastaus extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'ymparisto.arviointi_vastaus';     // table name
    public $id;                              // int4(4)  not_null default_nextval%28arviointi_vastaus_id_seq%29 primary_key
    public $kysely_id;                       // int4(4)  not_null
    public $user_id;                         // int4(4)  not_null
    public $sent_date;                       // timestamptz(8)  not_null
    public $reminder1_date;                  // timestamptz(8)  
    public $reminder2_date;                  // timestamptz(8)  
    public $arvio_arvo;                      // numeric(-1)  
    public $arvio_perustelu;                 // text(-1)  
    public $arvio_date;                      // timestamptz(8)  
    public $closed;                          // bool(1)  default_false
    public $published;                       // bool(1)  default_false

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Ymparisto_arviointi_vastaus',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    public function getVastausUrl() {
    
    	$Host = ( strpos(PATH_SERVER, 'dev') !== false ) ? 'dev.ymparisto.lounaispaikka.fi' : 'ymparisto.lounaispaikka.fi';
    	return 'http://'.$Host.'/arviointi/'.$this->id.'/'.$this->genVastausHash().'/';
    
    }
    
    public function genVastausHash() {
    
    	return substr(md5($this->id.$this->user_id), 5, 5);
    
    }
    
    public function checkVastausHash( $hash ) {
    	
    	if ( $hash == $this->genVastausHash() ) return true;
    	
    	return false; 
    	
    }
    
    public static function getPlainPerustelu( $Teksti ) {
	    
	    $Txt = trim(strip_tags($Teksti));
	    $Txt = utf8_encode( html_entity_decode($Txt) );
	    return trim($Txt);
	    
    }
    
    public static function arvo2teksti( $IntArvo ) {
	    
	    if ( is_double($IntArvo) ) $IntArvo = intval($IntArvo);
	    $Arvotekstit = array( "ei toteudu", "heikosti", "kohtalaisesti", "hyvin", "toteutuu", "toteutuu" );
	    if ( isset($Arvotekstit[$IntArvo]) ) return $Arvotekstit[$IntArvo];
	    
	    return "virhe";
	    
    }
    
}
