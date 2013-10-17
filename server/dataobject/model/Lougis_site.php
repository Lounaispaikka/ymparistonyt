<?php
/**
 * Table Definition for lougis.site
 */
require_once 'DB/DataObject.php';

class Lougis_site extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.site';                     // table name
    public $id;                              // varchar(-1)  not_null primary_key
    public $title;                           // varchar(-1)  not_null
    public $default_template;                // varchar(-1)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_site',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    public function getPageByName($UrlName, $LangId) {
    
    	$Pg = new \Lougis_cms_page();
    	$Pg->site_id = $this->id;
    	$Pg->lang_id = $LangId;
    	$Pg->url_name = $UrlName;
    	$Pg->limit(1);
    	$Pg->find();
    	$Pg->fetch();
    	if ( empty($Pg->published) ) return false;
    	return $Pg;
    
    }
    
    public function getFirstPage( $LangId ) {
    	
    	$Pg = new \Lougis_cms_page();
    	$Pg->site_id = $this->id;
    	$Pg->lang_id = $LangId;
    	$Pg->orderBy('seqnum ASC');
    	$Pg->limit(1);
    	$Pg->find();
    	$Pg->fetch();
    	if ( empty($Pg->published) ) return false;
    	return $Pg;
    	
    }
    //get published indicators 
    /* EI KÄYTÖSSÄ --> cmsPublic.php funktio
    public function getPubInds($Amount = null) {
    
    	$IndicatorArray = array();
        $Indicator = new \Lougis_chart();
    	$Indicator->orderBy('title ASC');
        $Indicator->published = true;
        if ( !empty($Amount) ) $Indicator->limit($Amount);
    	$Indicator->find();
    	while( $Indicator->fetch() ) $IndicatorArray[] = clone($Indicator);
    	
    	return $IndicatorArray;
    
    }*/
    
    public function getNews( $Amount = null ) {
    
    	$NewsArray = array();
    	$News = new \Lougis_news();
    	$News->site_id = $this->id;
    	$News->orderBy('created_date DESC');
    	if ( !empty($Amount) ) $News->limit($Amount);
    	$News->find();
    	while( $News->fetch() ) $NewsArray[] = clone($News);
    	
    	return $NewsArray;
    
    }
    //Uutiset
    public function getNewsTapahtumat( $Amount = null ) {

    	$NewsArray = array();
    	$News = new \Lougis_news();
    	//$News->site_id = $this->id;
        $News->news_type = 1; //0=uutinen, 1=tapahtuma
    	$News->orderBy('created_date DESC');
    	if ( !empty($Amount) ) $News->limit($Amount);
    	$News->find();
    	while( $News->fetch() ) $NewsArray[] = clone($News);
    	
    	return $NewsArray;
    
    }
    //Tapahtumat
    public function getNewsUutiset( $Amount = null ) {
    
    	$NewsArray = array();
    	$News = new \Lougis_news();
    	//$News->site_id = $this->id;
        $News->whereAdd('news_type = 0');
        $News->whereAdd('news_type IS NULL', 'OR'); //0=uutinen tai NULL, 1=tapahtuma
        $News->orderBy('created_date DESC');
    	if ( !empty($Amount) ) $News->limit($Amount);
    	$News->find();
    	while( $News->fetch() ) $NewsArray[] = clone($News);
    	
    	return $NewsArray;
    
    }
    public function getRecentCharts( $Amount = null ) {
    
    	$ChartArray = array();
        $Chart = new \Lougis_chart();
    	$Chart->published = true;
    	$Chart->orderBy('updated_date DESC');
    	if ( !empty($Amount) ) $Chart->limit($Amount);
    	$Chart->find();
    	while( $Chart->fetch() ) $ChartArray[] = clone($Chart);
    	
    	return $ChartArray;
    
    }
    
     public function getRecentComments( $Amount = null ) {
    
    	$CommentArray = array();
        $Comment = new \Lougis_cms_comment();
		$Pg = new \Lougis_cms_page();
		$Ch = new \Lougis_chart();
	    $query = "SELECT
                  cm.title AS title, cm.page_id AS page_id, cm.date_created AS date, cm.part_id AS part_id,
				  pg.title AS page_title, pg.url_name AS url_name, 
				  ch.title AS chart_title
                  FROM {$Comment->__table} AS cm
				  LEFT JOIN {$Pg->__table} AS pg ON cm.page_id = pg.id
				  LEFT JOIN {$Ch->__table} AS ch ON cm.part_id = ch.id 
				  ORDER BY date DESC
				  LIMIT {$Amount}
				  ;";
		/*$query = "SELECT
                  cm.title AS title, cm.page_id AS page_id, cm.date_created AS date, cm.part_id AS part_id,
				  pg.title AS page_title, pg.url_name AS url_name, 
				  ch.title AS chart_title
                  FROM Lougis.cms_comment AS cm
				  LEFT JOIN Lougis.cms_page AS pg ON cm.page_id = pg.id
				  LEFT JOIN Lougis.chart AS ch ON cm.part_id = ch.id 
				  ORDER BY date DESC
				  LIMIT {$Amount}
		;";*/
		$Comment->query($query);
    	while( $Comment->fetch() ) $CommentArray[] = clone($Comment);  	
    	return $CommentArray;
    }
	public function getCommentDetails($CommentId) {
		
		$Comment = new \Lougis_cms_comment();
	    $query = "SELECT
                  cm.title AS title, cm.page_id AS page_id, cm.date_created AS date, cm.part_id AS part_id,
				  pg.title AS page_title, pg.url_name AS url_name, 
				  ch.title AS chart_title
                  FROM {$Comment->__table} AS cm
				  WHERE cm.id = {$CommentId}
				  LEFT JOIN lougis.cms_page AS pg ON cm.page_id = pg.id
				  LEFT JOIN lougis.chart AS ch ON cm.part_id = ch.id 
				  ;";
		$Comment->query($query);
		return $Comment;
		
	}
	
    public function getChartIframe() {
    	
    	$Chart = new \Lougis_chart($_GET['id']);
    	return $Chart->getIframeCode();
    	
    	
    }
    public function getChartCsv() {
    	
    	$Chart = new \Lougis_chart($_GET['id']);
    	return $Chart->getDownloadCsvPath();
    	
    	
    }
    
}
