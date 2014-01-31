<?php
/**
 * Table Definition for lougis.news
 */
require_once 'DB/DataObject.php';

class Lougis_news extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.news';                     // table name
    public $id;                              // int4(4)  not_null default_nextval%28lougis.news_id_seq%29 primary_key
    public $site_id;                         // varchar(-1)  not_null
    public $lang_id;                         // bpchar(-1)  not_null
    public $title;                           // varchar(-1)  not_null
    public $description;                     // varchar(-1)  
    public $content;                         // text(-1)  
    public $seqnum;                          // int4(4)  
    public $created_date;                    // timestamptz(8)  not_null
    public $created_by;                      // int4(4)  
    public $published;                       // bool(1)  default_true
    public $source;                          // varchar(-1)  
    public $source_url;                      // varchar(-1)  
    public $news_type;                       // int2(2)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_news',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    //Array of Lougis_cms_page objects related to this news article, load the contents of the array with $this->loadPages()
    public $pageObjects = array();
    public $pagesLoaded = false;
    
    public function loadPages() {
    
    	$Np = new \Lougis_news_page();
    	$Np->news_id = $this->id;
    	$Np->orderBy('page_id');
    	$Np->find();
    	while( $Np->fetch() ) { 
    		$Page = new \Lougis_cms_page($Np->page_id);
    		$this->pageObjects[] = $Page;
    	}
    	$this->pagesLoaded = true;
    	return $this->pageObjects;
    
    }
    
    public function setPages( $pagesArray ) {
    
    	foreach( $pagesArray as $pageId ) {
	    	$Np = new \Lougis_news_page();
	    	$Np->news_id = $this->id;
    		$Np->page_id = $pageId;
    		$Np->save();
    	}
    	
    	return true;
    
    }
    
    public function removePages() {
    
    	$Np = new \Lougis_news_page();
    	$Np->news_id = $this->id;
    	return $Np->delete();
    
    }
    
    public function getPagesIdArray() {
    
    	if ( !$this->pagesLoaded ) $this->loadPages();
    	$CleanData = array();
    	foreach($this->pageObjects as $Page) {
    		$CleanData[] = intval($Page->id);
    	}
    	return $CleanData;
    
    }
    
    public function getPagesDataArray() {
    
    	if ( !$this->pagesLoaded ) $this->loadPages();
    	$CleanData = array();
    	foreach($this->pageObjects as $Page) {
    		$CleanData[] = $Page->toArray();
    	}
    	return $CleanData;
    
    }
    
    public function getTitleAsUrl() {
    
    	$url = strtolower(trim($this->title));
    	$url = str_replace(array('ä','ö','å',' '), array('a','o','a','-'), $url);
		$url = preg_replace('/[^(\x20-\x7F)]*/','', $url);
		return $url;
    	
    
    }
    
    public function getContentHtml() {
    
    	return $this->content;
    
    }
    
    public static function growSeqnums( $Amount = 1 ) {
    
    	$sql = "UPDATE lougis.news SET seqnum = (seqnum+".intval($Amount).")";
    	
    	$News = new \Lougis_news();
    	$News->query($sql);
    	
    	return true;
    
    }
    
}
