<?php
/**
 * Table Definition for lougis.news_page
 */
require_once 'DB/DataObject.php';

class Lougis_news_page extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.news_page';                // table name
    public $news_id;                         // int4(4)  not_null primary_key multiple_key
    public $page_id;                         // int4(4)  not_null primary_key multiple_key

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_news_page',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
