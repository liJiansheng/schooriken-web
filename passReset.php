<?
	$email = mysql_real_escape_string($_POST["email"]);
	echo json_encode(array("msg" => $email));
	/*echo json_encode(array("msg" => procResetPassword($email)));
	function procResetPassword($inEmail){ //Resets the password with a random string
		//DO SEND EMAIL STUFF HERE
		return $inEmail;
	}*/
?>