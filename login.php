<?php
	$user = addslashes($_POST["user"]);
	$pass = md5($_POST["pass"]);
	require_once("methods.php");	
	session_start();
	$query = mysql_query("SELECT * FROM `teachers` WHERE `username` = '$user' AND `password` = '$pass'");
	if (mysql_num_rows($query) == 0)
	{
		header("Location: /index.php?loginfailed=1&user=$user");
		die();
	}
	else
	{
		while ($row = mysql_fetch_array($query))
		{
			$_SESSION['uid'] = $row['teacher_id'];
			$uid = $_SESSION['uid'];
		}
		$_SESSION['user']=$user;
	}
	mysql_close($con);
	header("Location: /newhome.php");
?>