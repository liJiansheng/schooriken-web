<?php
require_once("session.php");
require_once("common.php");

if (isset($_GET['id'])) {
    
    $key = $_GET['id'];
    $query = mysql_query("SELECT * FROM registrationkeys WHERE register_key='$key'");
    if (($user = mysql_fetch_array($query)) == NULL) {
        errorMessage("Invalid key!","index.php");
        die(0);
    }
} else {
    errorMessage("Invalid key!","index.php");
    die(0);
}
if (isset($_POST['id'])) {
    if (!isset($_POST['username']) || !isset($_POST['password1'])
    || $_POST['username']=="" || $_POST['password1']=="") {
        errorMessage2("Cannot leave field blank!");
    } else if (!isset($_POST['password2'])||$_POST['password1']!=$_POST['password2']) {
        errorMessage2("Passwords do not match!");
    } else {
        $username = mysql_real_escape_string($_POST['username']);
        if (
            mysql_num_rows(mysql_query("SELECT * FROM students WHERE username='$username'"))!=0 ||
            mysql_num_rows(mysql_query("SELECT * FROM teachers WHERE username='$username'"))!=0
        ) {
            errorMessage2("Username already in use!");
        } else {
            $name = mysql_real_escape_string($user['name']);
            $email = mysql_real_escape_string($user['email']);
			$class = mysql_real_escape_string($user['class']);
            $password = md5($_POST['password1']);
            $schoolID = $user['school_id'];
            if ($user['teacher']==0) {
            	mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
			$s1 = mysql_query("INSERT INTO users (username, name,email, password, role, schoolID)
		VALUES ('$username','$name','$email','$password',2,$schoolID)");   
		$lastid = mysql_insert_id();
			   $s2 = mysql_query(
                    "INSERT INTO students".
                    "(student_id, class) ".
                    "VALUES ('$lastid','$class')"
                );				
			if ($s1 and $s2) {
    				mysql_query("COMMIT");
				} else {        
    				mysql_query("ROLLBACK");
				}		
            } else {
				mysql_query("INSERT INTO users (username, name,email, password, role, schoolID)
		VALUES ('$username','$name','$email','$password',1,$schoolID)");   
		/*$lastid = mysql_insert_id();
			   $s2 = mysql_query(
                    "INSERT INTO students".
                    "(student_id, class) ".
                    "VALUES ('$lastid','$class')"
                );				
			if ($s1 and $s2) {
    				mysql_query("COMMIT");
				} else {        
    				mysql_query("ROLLBACK");
				}		
                mysql_query(
                    "INSERT INTO teachers".
                    "(username,password,schoolID) ".
                    "VALUES ('$username','$password',$schoolID)"
                );*/
            }
            mysql_query("DELETE FROM registrationkeys WHERE register_key='$key'");
            successMessage("Registration successful!","index.php");
            die(0);
        }
    }
}
require_once("top.php");
?>
<div class="buffer"></div>
<div class="container"><div class="row col-md-6">
<form name="registration" class="form-horizontal" method="POST" id="formRegstration" action=""><fieldset>
    <div class="form-group">
        <label class='col-md-3'>Name:</label>
        <div class='col-md-3'><?php echo $user['name']; ?></div>
    </div>
    <div class="form-group">
        <label class='col-md-3'>Email:</label>
        <div class='col-md-3'><?php echo $user['email']; ?></div>
    </div>
    <div class="form-group">
        <label class='col-md-3'>Account Type:</label>
        <div class='col-md-3'>
        <?php
            if($user['teacher']==1) {
                echo "Teacher";
            } else {
                echo "Student";			
            }
        ?>
        </div>
    </div>
    <div class="form-group">
        <label class='col-md-3'>Username:</label>
        <input class='col-md-3' type="text" name="username" id="username" size=32>
    </div>
    <div class="form-group">
        <label class='col-md-3'>Password:</label>
        <input class='col-md-3' type="password" name="password1" id="password1" size=32>
    </div>
    <div class="form-group">
        <label class='col-md-3'>Re-enter password:</label>
        <input class='col-md-3' type="password" name="password2" id="password2" size=32>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary" name="btnSubmit" id="btnSubmit">Register</button>
    </div>
    <input type='hidden' name='id' id='id' value='<?php echo $_GET["id"]; ?>'>
</fieldset></form>
</div></div>

<?php
require_once("newtail.php");
?>