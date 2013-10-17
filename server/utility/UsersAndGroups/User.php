<?php
namespace Lougis\utility;

require_once(PATH_SERVER.'abstracts/Utility.php');

class User extends \Lougis\abstracts\Utility {

    private static $LoggedUser;

    public function getPublicUsers() {
        $users = array();
        $User = new \Lougis_user();
        $User->orderBy("lastname, firstname");
        $User->find();

        while($User->fetch()) {
            $users[] = $User->getBasicInfoArray();
        }
        return $users;
	}

    public function saveUser($userData) {
        $id = ($userData['id'] == 0)? null: $userData['id'];
        unset($userData['id']);
        $User = new \Lougis_user($id);
        $User->setFrom($userData);
        if(empty($User->date_created)) $User->date_created = date(DATE_W3C);
        if(!empty($userData['password']) && $userData['password'] == $userData['password_again']) {
           	$User->setPassword($userData['password']);
        } else {
       		unset($userData['password']);
        }

        $User->save();
        return $User->id;
    }

    public function deleteUser($id) {
        $User = new \Lougis_user($id);
        $User->delete();
    }

    public function getLoggedUserInfo() {
        if(empty(self::$LoggedUser)) self::$LoggedUser = new \Lougis_user($_SESSION['user_id']);
        return self::$LoggedUser->getBasicInfoArray();
    }

    public function login($email, $clearPassword) {
    
    	global $Session;
	if(!empty($email) and !empty($clearPassword)){
	  $User = new \Lougis_user();
	  $User->email = $email;
	  $User->password = \Lougis_user::hashPasswd($clearPassword);
	  $User->find(true);
	  if(!empty($User->id)) {
		  if ( !isset($Session->lifetime) || empty($Session->lifetime) ) throw new \Exception("Unable to login. Session data missing");
		  $Session->loginUser( $User->id );
	      return true;
	  }
        }
        return false;
    }
}
?>