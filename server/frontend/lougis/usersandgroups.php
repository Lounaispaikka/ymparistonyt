<?php
namespace Lougis\frontend\lougis;

require_once(PATH_SERVER.'abstracts/Frontend.php');
require_once(PATH_SERVER.'utility/UsersAndGroups/User.php');
require_once(PATH_SERVER.'utility/UsersAndGroups/Group.php');

class Usersandgroups extends \Lougis\abstracts\Frontend {

    public function jsonListUsers() {
        $user = new \Lougis\utility\User();
        $response = array(
            "success" => true,
            "users" => $user->getPublicUsers()
        );
        $this->jsonOut($user->getPublicUsers());
	}

    public function jsonListGroupsWithUsers() {
        $group = new \Lougis\utility\Group();

        $response = array(
            "success" => true,
            "groups" => $group->getGroupsWithUsers()
        );

        $this->jsonOut($response);
    }

    public function editUser() {
        $data = file_get_contents("php://input");
        $userData = json_decode($data, true);
        
        $user = new \Lougis\utility\User();
        $id = $user->saveUser($userData);
        $response = array(
            "success" => true,
            "userId" => $id
        );
        $this->jsonOut($response);
    }

    public function editGroup() {
        $groupData = $_REQUEST;
        $group = new \Lougis\utility\Group();
        $group->saveGroup($groupData);
        $response = array(
            "success" => true
        );
        $this->jsonOut($response);
    }

    public function deleteUser() {
        $id = $_REQUEST['userId'];
        $user = new \Lougis\utility\User();
        $user->deleteUser($id);
        $response = array(
            "success" => true
        );
        $this->jsonOut($response);
    }

    public function deleteGroup() {
        $id = $_REQUEST['groupId'];
        $user = new \Lougis\utility\Group();
        $user->deleteGroup($id);
        $response = array(
            "success" => true
        );
        $this->jsonOut($response);
    }

    public function jsonLoggedUserInfo() {
        $user = new \Lougis\utility\User();
        $info = $user->getLoggedUserInfo();
        $this->jsonOut($info);
    }
    
    public function logoutUser() {
    
		session_destroy();
		unset($_SESSION['user_id']);
		unset($_SESSION['site_id']);
		unset($_SESSION['admin_login']);
		header('Location: /hallinta/?logout');
	
    }
    
}
?>