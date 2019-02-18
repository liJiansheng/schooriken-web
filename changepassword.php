<?php

require_once("session.php");
require_once("common.php");

if (isset($_POST['submit'])) {
    if (!isset($_POST['username']) || !isset($_POST['password1'])
    || $_POST['username']=="" || $_POST['password1']=="") {
        errorMessage2("Cannot leave field blank!");
    } else if (!isset($_POST['password2'])||$_POST['password1']!=$_POST['password2']) {
        errorMessage2("Passwords do not match!");
    } else {
            $userID = $mydata['userID'];          
            $password = md5($_POST['password1']);
            $username = mysql_real_escape_string($_POST['username']);
            
			mysql_query("UPDATE users SET username = '$username', password='$password' WHERE userID = '$userID';");                    
            successMessage("Password change successful!".$password,"index.php");
            die(0);
       
    }
}
require_once("top.php");
?>

<div class="container">

<h2>Change Password</h2>
   <form class='form-horizontal' method='post'><fieldset>
    <div class="form-group">
        <label class='col-md-3'>Username:</label>
        <input class='col-md-3' type="text" name="username" id="username" value =<?php echo $mydata['username'] ?>  size=32>
    </div>
    <div class="form-group">
        <label class='col-md-3'>Enter new password:</label>
        <input class='col-md-3' type="password" name="password1" id="password1" size=32>
    </div>
    <div class="form-group">
        <label class='col-md-3'>Re-enter password:</label>
        <input class='col-md-3' type="password" name="password2" id="password2" size=32>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary" name="submit" id="Submit">Edit</button>
    </div>
</fieldset></form>
</div></div>

<?php
require_once("newtail.php");
?>