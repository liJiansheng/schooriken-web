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
    <link rel="shortcut icon" href="http://getbootstrap.com/docs-assets/ico/favicon.png">

    <title>Schooriken</title>
  <link href='http://fonts.googleapis.com/css?family=Oswald:400,700|Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css'>  	
	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Economica:700,400italic">
     <link href="css/bootstrap.css" rel="stylesheet">     
  <link href="css/style.css" rel="stylesheet"> 
  <link rel="stylesheet" href="css/font-awesome.css">
 <script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</head>
<body>

<header>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
        <div class="navbar-header">
            <a class="brand" href="index.php">           
                <img src="img/logo.png" alt="SchooRiken Logo" height="45" width="45" />                
            </a>                  
             </div>
              <h2 class="col-md-2 logo-text">SCHOORIKEN</h2>                 
             <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
             <li><button class="btn-primary btn-lg">Login</button></li>
            </ul>          
        </div>
    </div>
</div>
</header>
<div class="modal hide fade" id="schumodal" tabindex="-1" role="dialog" aria-labelledby="schumodallabel" aria-hidden="true">
	<div class="modal-header">
    	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Login</h3>
    </div>
    <form action="login.php" method='post' id='login'>
    <div class="modal-body">
    	Username: <input type="text" name="user"/><br/>
        Password: <input type="password" name="pass"/><br/>
        <?php
			if(isset($_GET['loginfailed'])){
				echo "<span class='redtext'>Username Or Password Is Incorrect</span>";
			}
		?>
    </div>
    <div class="modal-footer">
    	<a class="btn" href="#" data-dismiss="modal" aria-hidden="true">Close</a>
    	<button type="submit" class="btn btn-primary">Login</button>
    </div>
    </form>
</div>
</div>

	<div class="row-fluid" id="flashRow">
		<div class="col-md-3 visible-desktop"></div>
		<div class="col-md-6">
        <?php		
			if (isset($_SESSION['flash'])) {
				foreach ($_SESSION['flash'] as $k => $v) {
					$type = $v['type'];
					echo "<div class='alert $type'><a class='close' data-dismiss='alert' href='#'>×</a>";
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
	</div>