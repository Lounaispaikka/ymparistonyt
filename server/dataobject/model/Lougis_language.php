<?php
/**
 * Table Definition for lougis.language
 */
require_once 'DB/DataObject.php';

class Lougis_language extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.language';                 // table name
    public $id;                              // bpchar(-1)  not_null primary_key
    public $name_local;                      // varchar(-1)  not_null
    public $name_english;                    // varchar(-1)  
    public $locale;                          // varchar(-1)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_language',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
