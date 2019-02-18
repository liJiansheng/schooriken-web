<?php
	//Checks if the user is logged in, if he isn't, to the dungeon!
	session_start();
	if(!isset($_SESSION['uid'])){
		header('Location: /index.php');
	}
	require_once('methods.php');
	$uid = $_SESSION['uid'];
	//echo '<center><img src="logo.png" alt="SchooRiken Admin" height="500" width="500" align="center"/></center>';
?>
<!DOCTYPE html>
<!-- saved from url=(0039)http://getbootstrap.com/examples/theme/ -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="http://getbootstrap.com/docs-assets/ico/favicon.png">

    <title>Schooriken</title>
 <link href="css/bootstrap-theme.min.css" rel="stylesheet">
 <link href="css/bootstrap.css" rel="stylesheet">
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<div class="wrapper">
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
        <div class="navbar-header">
            <a class="brand" href="newhome.php">
                <img src="logo_new.png" alt="SchooRiken Admin" height="40" width="40" />
            </a>
            <ul class="nav">
            	<li><a><?php echo "Welcome,".$_SESSION['user'];?></a></li>
                <li><a href="newhome.php">Home</a></li>
            	<li class="dropdown">
                	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    	Manage
                        <b class="caret"></b>
                	</a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
                        <li><a tabindex="-1" href="managegroup_view.php">Groups</a></li>
                        <li><a tabindex="-1" href="manageevent_view.php">Events</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav pull-right">
            	<li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="body-content">