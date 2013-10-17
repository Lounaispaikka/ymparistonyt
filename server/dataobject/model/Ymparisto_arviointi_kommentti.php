<?php
/**
 * Table Definition for ymparisto.arviointi_kommentti
 */
require_once 'DB/DataObject.php';

class Ymparisto_arviointi_kommentti extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'ymparisto.arviointi_kommentti';    // table name
    public $id;                              // int4(4)  not_null default_nextval%28arviointi_kommentti_id_seq%29 primary_key
    public $vastaus_id;                      // int4(4)  not_null
    public $user_id;                         // int4(4)  
    public $sender_name;                     // varchar(-1)  
    public $sender_email;                    // varchar(-1)  
    public $sender_organization;             // varchar(-1)  
    public $sender_comment;                  // text(-1)  not_null
    public $sent_date;                       // timestamptz(8)  not_null
    public $visible;                         // bool(1)  default_true
    public $moderator_id;                    // int4(4)  
    public $moderator_notes;                 // text(-1)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Ymparisto_arviointi_kommentti',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
