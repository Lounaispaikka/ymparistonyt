<?php
/**
 * Table Definition for lougis.group
 */
require_once 'DB/DataObject.php';

class Lougis_group extends \Lougis\DB_DataObject_Wrapper
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.group';                    // table name
    public $id;                              // int4(4)  not_null default_nextval%28lougis.group_id_seq%29 primary_key
    public $name;                            // varchar(-1)  not_null
    public $date_created;                    // timestamptz(8)  not_null
    public $created_by;                      // int4(4)  not_null
    public $public_joining;                  // bool(1)  not_null default_false
    public $description;                     // text(-1)  
    public $parent_id;                       // int4(4)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_group',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    public function getBasicInfoWithUsers() {
        $info = array(
            "id" => intval($this->id),
            "name" => $this->name,
            "date_created" => date("Y-m-d", strtotime($this->date_created)),
            "public_joining" => ($this->public_joining == 't'),
            "description" => $this->description,
            "parent_id" => intval($this->parent_id),
            "users" => array()
        );
        $Users = new \Lougis_group_user();
        $Users->group_id = intval($this->id);
        $Users->find();
        while($Users->fetch()) {
            $info['users'][] = array(
                "id" => $Users->user_id,
                "isAdminOfAGroup" => $Users->group_admin == 't'
            );
        }
        return $info;
    }

}
