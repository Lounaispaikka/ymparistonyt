<?php
/**
 * Table Definition for lougis.cms_menu
 */
require_once 'DB/DataObject.php';

class Lougis_cms_menu extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.cms_menu';                 // table name
    public $id;                              // int4(4)  not_null default_nextval%28lougis.cms_menu_id_seq%29 primary_key
    public $site_id;                         // varchar(-1)  not_null
    public $lang_id;                         // bpchar(-1)  not_null
    public $name;                            // varchar(-1)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_cms_menu',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
