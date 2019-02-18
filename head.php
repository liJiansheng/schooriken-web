<?php
	//Checks if the user is logged in, if he isn't, to the dungeon!
	session_start();
	if(!isset($_SESSION['uid'])){
		header('Location: /index.php');
	}
	require_once('methods.php');
	$uid = $_SESSION['uid'];
	echo '<center><img src="logo.png" alt="SchooRiken Admin" height="500" width="500" align="center"/></center>';
?>