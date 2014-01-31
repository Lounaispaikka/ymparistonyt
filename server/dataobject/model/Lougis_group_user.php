<?php
/**
 * Table Definition for lougis.group_user
 */
require_once 'DB/DataObject.php';

class Lougis_group_user extends \Lougis\DB_DataObject_Wrapper
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.group_user';               // table name
    public $group_id;                        // int4(4)  not_null primary_key multiple_key
    public $user_id;                         // int4(4)  not_null primary_key multiple_key
    public $group_admin;                     // bool(1)  not_null default_false
    public $date_added;                      // timestamptz(8)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_group_user',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
