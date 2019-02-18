<?php
require_once("session.php");
require_once("common.php");


if (isset($_POST['email'])) {
  $e = mysql_real_escape_string($_POST['email']);
      
		  	$q = mysql_query("SELECT userID, username FROM users WHERE email='$e' LIMIT 1");
			$numrows = mysql_num_rows($q);
			if($numrows>0){
			while($row = mysql_fetch_array($q, MYSQL_ASSOC)){
			$id = $row["userID"];
			$u = $row["username"];
		}
		$emailcut = substr($e, 0, 4);
		$randNum = rand(10000,99999);
		$tempPass = "$emailcut$randNum";
		$hashTempPass = md5($tempPass);
		if(mysql_num_rows(mysql_query("SELECT * FROM useroptions WHERE userID='$id' LIMIT 1"))>0){
		$sql = "UPDATE useroptions SET tempass='$hashTempPass' WHERE userID='$id' LIMIT 1";
		}else{
			$sql = "INSERT INTO useroptions (userID,tempass) VALUES ('$id','$hashTempPass');";
		}
	    $query = mysql_query($sql);
		$to = "$e";
		$from = "auto_responder@schooriken.com";
		 $headers = "From: Schooriken <no-reply@schooriken.com>\r\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
		$subject ="Schooriken Reset Password";
		$msg = '<h2>Hello '.$u.'</h2><p>This is an automated message from schooriken.com. If you did not recently initiate the Forgot Password process, please disregard this email.</p><p>You indicated that you forgot your login password. A temporary password is generated for you to log in with, then once logged in, please change your password immediately.</p><p>After you click the link below your password to login will be:<br /><b>'.$tempPass.'</b></p><p><a href="http:/schooriken.com/forgetpassword.php?uid='.$id.'&p='.$hashTempPass.'">Click here now to apply the temporary password shown below to your account</a></p><p>If you do not click the link in this email, no changes will be made to your account. In order to set your login password to the temporary password you must click the link above.</p>';
		if(mail($to,$subject,$msg,$headers)) {
			successMessage("Email Sent!","index.php");	  
			exit();
		} else {
			errorMessage("Email sent failed!","forgetpassword.php");	  
			exit();
		}
    } else {
      errorMessage("No such email found!","forgetpassword.php");	  
    }
    exit();
}
?><?php
// EMAIL LINK CLICK CALLS THIS CODE TO EXECUTE
if(isset($_GET['uid']) && isset($_GET['p'])){
	$uid = preg_replace('#[^a-z0-9]#i', '', $_GET['uid']);
	$temppasshash = preg_replace('#[^a-z0-9]#i', '', $_GET['p']);
	if(strlen($temppasshash) < 10){
		exit();
	}
	$sql = "SELECT userID FROM useroptions WHERE userID='$uid' AND tempass='$temppasshash' LIMIT 1";
	$query = mysql_query($sql);
	$numrows = mysql_num_rows($query);
	if($numrows == 0){
		errorMessage("There is no such username!","index.php");
    	exit();
	} else {
		$row = mysql_fetch_row($query);
		$id = $row[0];
		$sql = "UPDATE users SET password='$temppasshash' WHERE userID='$uid' LIMIT 1";
	    $query = mysql_query($sql);
		$sql = "UPDATE useroptions SET tempass='' WHERE userID='$uid' LIMIT 1";
	    $query = mysql_query($sql);
	 	successMessage("Password reset!","login_form.php");	  
        exit();
    }
}
require_once("top.php");
?>
<div class="buffer"></div>
<div class="container"><div class="col-md-6 col-md-offset-2">
   <h1>Forget Password</h1>
   <div class="gap"></div>
<form name="forgetpw" class="form-horizontal" method="POST" id="formForget" action=""><fieldset>

    <div class="form-group">
        <label class='col-md-3'>Enter your email:</label>
        <input class='col-md-9' type="text" name="email" id="email" size=100>
    </div>
  
    <div class="form-group">
        <button type="submit" class="btn btn-primary" name="btnSubmit" id="btnSubmit">Send</button>
    </div>

</fieldset></form>
</div></div>

<?php
require_once("newtail.php");
?>