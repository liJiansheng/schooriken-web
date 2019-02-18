<?php
require_once("connect.php");
require_once("common.php");
require_once("users.php");
class Session {
	public $user;
	
	function __construct() {
	session_start();
		$this->user = 0;
		if (isset($_SESSION['userID'])) {
			$this->user = Users::getUser($_SESSION['userID']);
			/*$uri = substr($_SERVER['REQUEST_URI'], 1);
			$uri = mysql_real_escape_string($uri);*/
			$userID = intval($_SESSION['userID']);
			//mysql_query("UPDATE users SET lastPage='$uri', lastActive=CURRENT_TIMESTAMP WHERE userID=$userID");
		}
session_write_close();
	}
	function isLogin() {
		//errorMessage("test session login ".$this->user['roleid'], "error.php");
		//$test = "login";
		return !($this->user == 0);
		//return true;
	}
	function isAdmin() {		
		if ($this->isLogin()) {
			if($this->user['role'] == 4)
			return true;
		}
		else return false;
	}
	function isTeacher() {		
		if ($this->isLogin()) {
			if($this->user['role'] == 1)
			return true;
		}
		else return false;
	}
	function hasAccess($level = 10) {		
		if ($level == 0) return true;
		else if ($level == 2) return $this->isLogin();
		else if ($level == 1) return $this->isTeacher();
		else if ($level == 4) return $this->isAdmin();
		else return false;
	}
	function authPage ($level = 10) {
		if ($this->hasAccess($level)) return;	
			errorMessage("Page not found!", "error.php");
		if (!$this->isLogin()) {
			session_start();
			$_SESSION['loginRedirect'] = $_SERVER["REQUEST_URI"];
			session_write_close();
			errorMessage("Please login to view this page. ", "error.php");
		}
		else if (!$this->isAdmin()) {
			errorMessage("You are not an adminstrator!", "error.php");
		}
		else errorMessage("You are not allowed to use this resource!", "error.php");
	}
}
$sess = new Session();
$mydata = ($sess->user);
?>
