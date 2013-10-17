<?php
namespace Lougis\utility;

require_once(PATH_SERVER.'abstracts/Utility.php');

class Group extends \Lougis\abstracts\Frontend {

    public function getGroupsWithUsers() {
        $groups = array();
        $Group = new \Lougis_group();
        $Group->orderBy("name");
        $Group->find();

        while($Group->fetch()) {
            $groups[] = $Group->getBasicInfoWithUsers();
        }
        return $groups;
	}
	
    public function saveGroup($groupData) {
    
        $id = ($groupData['id'] == 0)? null: $groupData['id'];
        unset($groupData['id']);
        $Group = new \Lougis_group($id);
        $Group->setFrom($groupData);
        
		if ( $Group->parent_id == 0 ) $Group->parent_id = null;
        if(empty($Group->date_created)) $Group->date_created = date(DATE_W3C);
        if(empty($Group->created_by)) $Group->created_by = 2;
        if ( !$Group->save() ) {
        	devlog($Group, 'pyry');
        	return false;
        }
        $users = json_decode($groupData['users'], true);
        $GUser = new \Lougis_group_user();
        $GUser->group_id = $Group->id;
        $GUser->delete();

        foreach($users as $user) {
            $GUser = new \Lougis_group_user();
            $GUser->group_id = $Group->id;
            $GUser->date_added = date(DATE_W3C);
            $GUser->user_id = $user['user_id'];
            $GUser->group_admin = $user['group_admin'];
            if ( !$GUser->save() ) {
	        	devlog($GUser, 'pyry');
	        }
        }

        return $Group->id;
    	
    	
        
    }

    public function deleteGroup($id) {
        $User = new \Lougis_group($id);
        $User->delete();
    }
}
?>