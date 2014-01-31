<?php
/**
 * Table Definition for ymparisto.arviointi_kierros
 */
require_once 'DB/DataObject.php';

class Ymparisto_arviointi_kierros extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'ymparisto.arviointi_kierros';     // table name
    public $id;                              // int4(4)  not_null default_nextval%28arviointi_kierros_id_seq%29 primary_key
    public $created_date;                    // timestamptz(8)  not_null
    public $started_date;                    // timestamptz(8)  
    public $reminder1_date;                  // timestamptz(8)  not_null
    public $reminder2_date;                  // timestamptz(8)  not_null
    public $closing_date;                    // timestamptz(8)  not_null
    public $closed;                          // bool(1)  default_false
    public $published;                       // bool(1)  default_false
    public $title1;                          // varchar(-1)  not_null unique_key
    public $title2;                          // varchar(-1)  
    public $title3;                          // varchar(-1)  
    public $content1;                        // text(-1)  
    public $email_tpl;                       // text(-1)  
    public $in_process;                      // bool(1)  default_false
    public $notes;                           // text(-1)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Ymparisto_arviointi_kierros',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    public function getKyselyt( $AsArray = false ) {
	    
	    $Kyselyt = array();
	    
		$sql  = "SELECT yak.*, 
				ohjelma.title || ' - ' || lcp.title AS ohjelmatitle, 
				( SELECT COUNT(id) FROM ymparisto.arviointi_ryhma AS yar WHERE yar.kysely_id = yak.id ) AS asiantuntijoita
				FROM ymparisto.arviointi_kysely AS yak
					LEFT JOIN lougis.cms_page AS lcp ON lcp.id = yak.page_id
					LEFT JOIN lougis.cms_page AS ohjelma ON ohjelma.id = lcp.parent_id
				WHERE yak.kierros_id = {$this->id}
				ORDER BY ohjelmatitle;";
	    
	    $Kys = new \Ymparisto_arviointi_kysely();
	    /*
	    $Kys->kierros_id = $this->id;
	    $Kys->find();
	    */
	    $Kys->query($sql);
	    while( $Kys->fetch() ) {
	    	if ( $Kys->asiantuntijoita < 1 ) $Kys->ohjelmatitle .= ' <span style="color:red;">(tyhj&auml;)</span>';
	    	if ( $AsArray ) {
	    		$Kyselyt[] = $Kys->toArray();
	    	} else {
	    		$Kyselyt[] = clone($Kys);
	    	}
	    }
	    
	    return $Kyselyt;
	    
    }
    
    public function addKysely( $PageId ) {
	    
	    $Kys = new \Ymparisto_arviointi_kysely();
	    $Kys->kierros_id = $this->id;
	    $Kys->page_id = $PageId;
	    if ( $Kys->count() == 0 ) return $Kys->save();
	    return false;
	    
    }
    
}
