<?php
	require_once("session.php");
	require_once("common.php");	
	ob_start();
?>
<!DOCTYPE html>
<!-- saved from url=(0039)http://getbootstrap.com/examples/theme/ -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="img/favicon.ico">

    <title>Schooriken - Student Personal Assistant App</title>
  <link href='http://fonts.googleapis.com/css?family=Oswald:400,700|Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css'>  	
	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Economica:700,400italic">
     <link href="css/bootstrap.min.css" rel="stylesheet">     
  <link href="css/style.css" rel="stylesheet"> 
  <link rel="stylesheet" href="css/font-awesome.css">
  <link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
 <script src="js/bootstrap-datetimepicker.min.js"></script>
</head>
<body>
<div class="wrapper">
<header>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
        <div class="navbar-header">
            <a class="brand" href="index.php">           
                 <img src="img/logo.png" alt="SchooRiken Logo" height="45" width="45" />   
            </a>                  
             </div>
                <h2 class="col-md-2 col-sm-4 logo-text">SCHOORIKEN</h2>  
                 <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
    			<span class="icon-bar"></span>
    			<span class="icon-bar"></span>
    			<span class="icon-bar"></span>
    </button>           
             <nav class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
            	<li><a href='about.php'><span>About</span></a></li>
				<li><a href='support.php'><span>Support</span></a></li>		
            <?php 					
			if ($sess->isLogin()) {	
            if($mydata['role'] == 1 || $mydata['role'] == 4){				
				echo "<li class='dropdown'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>Manage<b class='caret'></b></a>";
                echo " <ul class='dropdown-menu' role='menu' aria-labelledby='dropdownMenu'>";
                echo "<li><a href='viewGroupList.php'>Groups</a></li>";
                echo "<li><a href='viewAssessList.php'>Assessments</a></li>";
                echo "<li><a href='viewEventList.php'>Events</a></li>";
                echo "<li><a href='viewAnnounceList.php'>Announcements</a></li>";
                echo "</ul></li>";
				echo "<li class ='dropdown'><a href='#' class='dropdown-toggle' data-toggle='dropdown'>".htmlentities($mydata['name'])." ".$mydata['lname']."<i class='caret'></i></a>";
				echo "<ul class='dropdown-menu'>";
							}						
							echo "<li><a href='changepassword.php'>Change password</a></li>";
							echo "<li><a href='logout.php'>Logout</a></li>";
							echo "</ul></li>";
							}		
							else{																																				
								echo "<li><a href='login_form.php' id='login'>Login</a></li>";									
								 }
								 ?>                                           
            </ul>          
        </nav>
    </div>
</div>
</header>
<div class="buffer"></div>
		<div class="col-md-3 visible-desktop"></div>
		<div class="col-md-6">
        <?php		
			if (isset($_SESSION['flash'])) {
				foreach ($_SESSION['flash'] as $k => $v) {
					$type = $v['type'];
					echo "<div class='alert $type' style='z-index:9999'><a class='close' data-dismiss='alert' href='#'>×</a>";
					echo $v['message'];
					echo "</div>";
				}
				session_start();
				unset($_SESSION['flash']);
				session_write_close();
			}
			ob_end_flush();
			?>
		</div>
<div class="body-content">