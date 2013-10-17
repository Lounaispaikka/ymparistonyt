<?php
/**
 * Table Definition for ymparisto.arviointi_ryhma
 */
require_once 'DB/DataObject.php';

class Ymparisto_arviointi_ryhma extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'ymparisto.arviointi_ryhma';       // table name
    public $id;                              // int4(4)  not_null default_nextval%28arviointi_ryhma_id_seq%29 primary_key
    public $kysely_id;                       // int4(4)  not_null
    public $user_id;                         // int4(4)  not_null
    public $admin;                           // bool(1)  not_null default_false

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Ymparisto_arviointi_ryhma',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
