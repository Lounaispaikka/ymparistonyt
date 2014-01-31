<?php
/**
 * Table Definition for lougis.cms_content
 */
require_once 'DB/DataObject.php';

class Lougis_cms_content extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.cms_content';              // table name
    public $id;                              // int4(4)  not_null default_nextval%28lougis.cms_content_id_seq%29 primary_key
    public $page_id;                         // int4(4)  not_null
    public $lang_id;                         // bpchar(-1)  not_null
    public $content;                         // text(-1)  not_null
    public $date_created;                    // timestamptz(8)  not_null
    public $published;                       // bool(1)  not_null
    public $created_by;                      // int4(4)  
    public $content_column;                  // text(-1)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_cms_content',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
