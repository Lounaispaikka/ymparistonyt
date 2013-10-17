<?php
/**
 * Table Definition for lougis.cms_comment
 */
require_once 'DB/DataObject.php';

class Lougis_cms_comment extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.cms_comment';              // table name
    public $id;                              // int4(4)  not_null default_nextval%28cms_comment_id_seq%29 primary_key
    public $page_id;                         // int4(4)  not_null
    public $lang_id;                         // bpchar(-1)  not_null
    public $nick;                            // varchar(-1)  not_null
    public $title;                           // varchar(-1)  
    public $msg;                             // text(-1)  not_null
    public $parent_id;                       // int4(4)  
    public $date_created;                    // timestamptz(8)  not_null default_now%28%29
    public $hidden;                          // bool(1)  not_null default_false
    public $hidden_by;                       // int4(4)  
    public $likes;                           // int4(4)  default_0
    public $dislikes;                        // int4(4)  default_0
    public $part_id;                         // int4(4)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_cms_comment',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    public static function getAllForPage( $PageId, $PartId ) {
	    
	    $Comments = array();
	    $Cm = new \Lougis_cms_comment();
		$Cm->page_id = $PageId;
		//vain part_id-kohtaiset kommentit
		if ( isset($PartId) ) {
			$Cm->part_id = $PartId;
		}
		//vain kommentit, joissa ei ole part_id:tä
		else {
			$Cm->whereAdd('part_id IS NULL');
		}
	    $Cm->whereAdd('parent_id IS NULL');
	    $Cm->orderBy('date_created ASC');
	    $Cm->find();
	    while( $Cm->fetch() ) {
		    $Cm->loadReplys();
		    $Comments[] = clone($Cm);
	    }
	    return $Comments;
	    
    }
    
    public function loadReplys() {
	    
	    $this->replys = array();
	    $Rep = new \Lougis_cms_comment();
	    $Rep->parent_id = $this->id;
	    $Rep->orderBy('date_created ASC');
	    $Rep->find();
	    while( $Rep->fetch() ) {
		    $this->replys[] = clone($Rep);
	    }
	    return true;
	    
    }
	/*
	public function getParent( $ParentId ) {
		return new \Lougis_cms_comment($ParentId);
	}*/
	
	
    
    public static function getRules() {
	    
	    $Rules = "<h2>Ympäristö Nyt verkkokeskustelun säännöt</h2>
<p>Verkkokeskusteluun tulevat viestit tarkastetaan. Ylläpito voi lyhentää ja muokata kirjoituksia.</p>
<p>Kirjoittaja on juridisessa vastuussa viestinsä sisällöstä. Sisällöltään sopimattomat viestit tai mainokset poistetaan keskusteluista. Muista hyvät tavat, älä huuda!/HUUDA tai kiroile.</p>";

            return $Rules;
	    
    }
    
}
