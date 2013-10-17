<?php
/**
 * Table Definition for lougis.cms_menu_item
 */
require_once 'DB/DataObject.php';

class Lougis_cms_menu_item extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.cms_menu_item';            // table name
    public $id;                              // int4(4)  not_null default_nextval%28lougis.cms_menu_item_id_seq%29 primary_key
    public $menu_id;                         // int4(4)  not_null
    public $title;                           // varchar(-1)  not_null
    public $seqnum;                          // int4(4)  not_null default_nextval%28lougis.cms_menu_item_seqnum_seq%29
    public $page_id;                         // int4(4)  
    public $url_link;                        // varchar(-1)  
    public $target;                          // varchar(-1)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_cms_menu_item',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
