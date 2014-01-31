<?php
namespace Lougis;
/**
 * Wrapper for session classes.
 *
 * @author 		Pyry Liukas
 */
abstract class DB_Session_Wrapper extends \Lougis\DB_DataObject_Wrapper {

    public function __construct($SessionId = null) {

        ob_start();
        ini_set("session.use_cookies", 1);
        ini_set("session.name", $this->_cookie_name);
        ini_set("session.cookie_lifetime", $this->_session_max_lifetime);
        //ini_set("session.cookie_host", $_SERVER['HTTP_HOST']);

        session_set_save_handler(
                array(&$this, '_open'),
                array(&$this, '_close'),
                array(&$this, '_read'),
                array(&$this, '_write'),
                array(&$this, '_destroy'),
                array(&$this, '_clean')
        );

        if ( !isset($_SESSION['session_id']) ) {
            if ( session_start() ) {
                $_SESSION["start"] = true;
                $_SESSION["session_id"] = session_id();
            } else {
                $_SESSION["start"] = false;
            } //end if-else
            $this->get($_SESSION["session_id"]);
        } else {
            $this->get($_SESSION['session_id']);
        }
    }

    public function getId() {
        return $this->session_id;
    }

    private function setSessionId($sid) {
        if ($this->session_id != $sid) {
            $this->session_id = $sid;
            return true;
        }
        return false;
    }

    public function getSessionId() {
        return $this->session_id;
    }

    private function setClientData() {
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
        return true;
    }

    public function getCookieName() {
        return $this->_cookie_name;
    }

    public function setLifetime() {
        $this->lifetime = date(DATE_W3C, time() + $this->_session_max_lifetime);
        return true;
    }

    public function isExpired() {
        if (!empty($this->ended)) return true;
        if (strtotime($this->lifetime) < strtotime(date(DATE_W3C))) return true;
        return false;
    }

    private function session_load($sid) {
    }

    public function session_end() {
        session_regenerate_id(true);
        $sid = session_id(md5(time() . $this->_cookie_name));
        setcookie($this->_cookie_name, $sid, $this->_session_max_lifetime, '/');
        $_SESSION["session_id"] = $sid;
        return true;
    }

    private function check_session_lifetime($sid) {
        $this->whereAdd("session_id = '" . $sid . "'");
        $this->whereAdd('lifetime > NOW()');
        $this->whereAdd('ended IS NULL');
        $total = $this->count(DB_DATAOBJECT_WHEREADD_ONLY);
        if ($total > 0) return true;
        return false;
    }

    public function sessionExists($sid) {
        return $this->exists($sid, 'session_id');
    }

    public function _open() {
        return true;
    }

    public function _close() {
    	return true;
    	//$this->save();
    }

    public function _read($sid) {
        $this->get('session_id', $sid);
        return $this->getSessionData();
    }

    public function _write($sid, $data) {
        if ($this->session_data != $data) {
	        $this->session_data = $data;
            if ($this->N == false) {
	            $this->session_id = $sid;
	            $this->updated = date(DATE_W3C);
                $this->created = date(DATE_W3C);
                $this->setClientData();
                $this->setLifetime();
                $this->insert();
                $this->N = 1;
            } else {
            	$this->update();
            }
        }
        return false;
    }

    public function _destroy($sid = null) {
        $this->ended = date(DATE_W3C);
        $this->update();
        session_unset();
        setcookie($this->_cookie_name, '', -3600, '/');
        @session_write_close();
    }

    public function _clean() {
       // $query = "UPDATE ".self::$ObjectSchema.".".self::$ObjectTable." SET ended = NOW() WHERE lifetime < NOW()";
        $query = "UPDATE {$this->__table} SET ended = NOW() WHERE lifetime < NOW()";
        $this->query($query);
        return true;
    }

    public function getSessionData() {
        return !empty($this->session_data)? $this->session_data: '';
    }

}

?>