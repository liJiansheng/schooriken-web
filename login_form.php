<?php
require_once("users.php");
require_once("common.php");
if (isset($_POST['submit'])) {
	if (Users::validate($_POST['username'], $_POST['password'])) {
		if (isset($_SESSION['loginRedirect']) == false) $_SESSION['loginRedirect'] = "index.php";
		successMessage("Successfully logged in. ", $_SESSION['loginRedirect']);
	}
	else {
		errorMessage("Username and Password combination is invalid.", "error.php");
	}
}
if ($mydata) errorMessage("You are already logged in as ".htmlentities($mydata['fname']), 'viewUser.php?userID='.intval($mydata['userID']));
require_once("top.php");?>
<div class="buffer"></div>
	<div class="col-md-4 col-md-offset-4">
    <h1>Login</h1>
    <?php if (!$mydata) { ?>
   <form class="well form-horizontal" role="form" method='post' >
   <div class="form-group">
   	<label for="username" class="col-sm-4 control-label">Username</label>
    <div class="col-sm-8">
			<input type='text' class="form-control" name='username' placeholder='Username' autocorrect='off' autocapitalize='off' id='abc'>
      </div>
            </div>
		 <div class="form-group">	
         	<label for="password" class="col-sm-4 control-label">Password</label>
            <div class="col-sm-8">
            <input type='password' name='password' class="form-control" placeholder='Password'>
            </div>
            </div>
			<button type='submit' name='submit' class='btn btn-primary btn-block'>Sign in</button>
            
	</form>   
    <?php } ?>
    <a href="forgetpassword.php">Forget password? </a></div>
    <div class="content"></div>
  <?php require_once("newtail.php");?>
