<?php
require_once("session.php");
require_once("common.php");

$sess->authPage(4);

require_once("top.php");

function do_mail($name,$email,$id) {
    $msg =
        "Dear $name,\r\n".
        "\r\n Welcome to Schooriken!\r\n".
        "Your school is adopting Schooriken as their student mobile communication channel. For students, Schooriken acts as their mobile personal personal assistant app. For schools and teachers, Schooriken is the digital channel to reach your students.\r\n". 
		"\r\n To get started, please go to http://schooriken.com/register.php?id=$id.\r\n".			
		"\r\n Click on the link to create your account. For students, once you have created your account you can go to http://schooriken.com to download the mobile apps. Teachers can create groups and update them using http://schooriken.com. ".
		"\r\n For more details on how to use Schooriken, go to http://schooriken.com/support.php".				
        "\r\n Kind regards,\r\n".
        "The Schooriken Team\r\n";
    $headers = "From: Schooriken <no-reply@schooriken.com>\r\n";
    return mail($email,"Schooriken Account Registration", $msg, $headers);
}

if (isset($_POST["btnSubmit"])) {
    if (isset($_POST["school"]) && isset($_FILES["inputFile"])) {
        if (($handle = fopen($_FILES["inputFile"]["tmp_name"],"r")) !== FALSE) {
            $ctr = 0;
            if ($_POST["school"]=="other") {
                $schoolID = -1;
                $schoolName = mysql_real_escape_string($_POST["inputSchool"]);
            } else {
                $schoolID = intval($_POST["school"]);
                $schoolName = "";
            }
            $password = md5("");
            
            //Skip first row
            $data = fgetcsv($handle,10000,",");
            $students = array();
            $s = 0;
            $teachers = array();
            $t = 0;
          	$class = array();
            while (($data = fgetcsv($handle,10000,",")) !== FALSE) {
				
                if (!isset($data[0])||!isset($data[1])||!isset($data[2]) ||!isset($data[3])) continue;                 
			  //  if ($data[0]==""||$data[1]==""||$data[2]==""||$data[3]=="") continue;
				//echo $data[2][0];
              
				$name = $data[0];
                $email = $data[1];
				$class = $data[3];
				
				
                if ($data[2][0]=='s'||$data[2][0]=='S') {
                    //Inserting new student

                    
                    /*mysql_query(
                        "INSERT INTO students".
                        "(username,name,email,password,schoolID)".
                        "VALUES ($username,$name,$email,$password,$schoolID)"
                        );*/
                    $students[$s] = array(
                        "name" => $name,
                        "email" => $email,
						"class" => $class
                    );
                    $s++;
                } else if ($data[2][0]=='t' ||$data[2][0]=='T') {
                    //Inserting new teacher
					
                    $teachers[$t] = array(
                        "name" => $name,
                        "email" => $email,
						"class" => $class
                    );
                    $t++;
                } else {
                    continue;
                }
                $ctr++;
            }
            echo "<div class='container'><div class='row'>";
            echo "<form name='addSchoolInfo' class='form-horizontal' method='POST' id='formAddSchoolInfo' action=''><fieldset>";
            echo "<div class='form-group'>";
            
            echo "<table class='table table-bordered'><thead>";
            echo "<tr><td colspan=3><strong>Students</strong></td></tr>";
            echo "<tr><td>Name</td><td>Email</td><td>Class</td></tr></thead>";
            echo "<tbody>";
            foreach ($students as $k => $v) {
                echo "<tr>";
                echo "<td>".htmlentities($v['name'])."</td><td>".htmlentities($v['email'])."</td><td>".htmlentities($v['class'])."</td>";
                echo "</tr>";
                echo "<input type='hidden' name='students[$k][name]' value='".htmlentities($v['name'],ENT_QUOTES)."' />";
                echo "<input type='hidden' name='students[$k][email]' value='".htmlentities($v['email'],ENT_QUOTES)."' />";
				echo "<input type='hidden' name='students[$k][class]' value='".htmlentities($v['class'],ENT_QUOTES)."' />";
            }
            echo "</tbody></table>";
            
            echo "<table class='table table-bordered'><thead>";
            echo "<tr><td colspan=2><strong>Teachers</strong></td></tr>";
            echo "<tr><td>Name</td><td>Email</td></tr></thead>";
            echo "<tbody>";
            foreach ($teachers as $k => $v) {
                echo "<tr>";
                echo "<td>".htmlentities($v['name'])."</td><td>".htmlentities($v['email'])."</td>";
                echo "</tr>";
                echo "<input type='hidden' name='teachers[$k][name]' value='".htmlentities($v['name'],ENT_QUOTES)."' />";
                echo "<input type='hidden' name='teachers[$k][email]' value='".htmlentities($v['email'],ENT_QUOTES)."' />";
				//echo "<input type='hidden' name='teachers[$k][class]' value='".htmlentities($v['class'],ENT_QUOTES)."' />";
            }
            echo "</tbody></table>";
            
            echo "</div>";
            echo "<div class='form-group'>";
            echo "<button type='submit' class='btn btn-primary' name='btnSubmit' id='btnSubmit'>Confirm</button>";
            echo "</div>";
            echo "<input type='hidden' name='schoolID' value='$schoolID' >";
            echo "<input type='hidden' name='schoolName' value='$schoolName' >";
            echo "</fieldset></form>";
            echo "</div></div>";
        }
    } else if (isset($_POST['schoolID'])) {
        $schoolID = intval($_POST['schoolID']);
        $schoolName = mysql_real_escape_string($_POST['schoolName']);
        if ($schoolID==-1) {
            mysql_query("INSERT INTO school(schoolName) VALUES ('$schoolName')");
            $schoolID = mysql_insert_id();
        }
        $ctr = 0;
        $key = md5(uniqid(rand(),true));
        if (isset($_POST['students'])) {
            foreach ($_POST['students'] as $k => $v) {
                $name = mysql_real_escape_string(html_entity_decode($v['name']));
                $email = mysql_real_escape_string(html_entity_decode($v['email']));
				 $class = mysql_real_escape_string(html_entity_decode($v['class']));
                mysql_query(
                    "INSERT INTO registrationkeys".
                    "(name,email,class,register_key,teacher,school_id) ".
                    "VALUES ('$name','$email','$class','$key',0,$schoolID)"
                );
                do_mail($name,$email,$key);
                $key = md5(uniqid(rand(),true));
                $ctr++;
            }
        }
        if (isset($_POST['teachers'])) {
            foreach ($_POST['teachers'] as $k => $v) {
                $name = mysql_real_escape_string(html_entity_decode($v['name']));
                $email = mysql_real_escape_string(html_entity_decode($v['email']));
                mysql_query(
                    "INSERT INTO registrationkeys".
                    "(name,email,register_key,teacher,school_id) ".
                    "VALUES ('$name','$email','$key',1,$schoolID)"
                );
                do_mail($name,$email,$key);
                $key = md5(uniqid(rand(),true));
                $ctr++;
            }
        }
        successMessage("Added $ctr users!","index.php");
        die(0);
    }
} else {

    echo "<div class='container'><div class='row'>";
    echo "<form name='addSchoolInfo' class='form-horizontal' enctype='multipart/form-data' method='POST' id='formAddSchoolInfo' action=''>";
    echo "<fieldset><div class='form-group'>";
    
    echo "<label>School:</label>";
    echo "<select name='school' id='selectSchool'>";
    $schools = mysql_query("SELECT * FROM school");
    while (($v = mysql_fetch_assoc($schools)) != FALSE) {
        echo "<option value=".$v['schoolID'].">".$v['schoolName']."</option>";
    }
    echo "<option value='other'>Add School...</option>";
    echo "</select>";
    echo "<input type='text' id='inputSchool' name='inputSchool'>";
    echo "</div>";
    
    echo "<div class='form-group'>";
    echo "<label>New accounts CSV file:</label>";
    echo "<input type='file' size='32' name='inputFile' value='' id='inputFile' />";
    echo "</div>";
    
    echo "<div class='form-group'>";
    echo "<button type='submit' class='btn btn-primary' name='btnSubmit' id='btnSubmit'>Add Accounts</button>";
    echo "<button type='reset' class='btn clear'>Clear</button>";
    echo "</div></fieldset>";
    echo "</form></div></div>";
    echo
        "<script>".
        "$(function(){".
            "$('#inputSchool').hide();".
            "$('#selectSchool').change(function(){".
                "if ($('#selectSchool').val()=='other') {".
                    "$('#inputSchool').show();".
                "} else {".
                    "$('#inputSchool').hide();".
                "}".
            "});".
        "});".
        "</script>";
    
}
    require_once("newtail.php");
?>