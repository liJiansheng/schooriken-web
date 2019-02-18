<?php
require_once("connect.php");
class Users {
	static function userExists ($userID) {
		$userID = intval($userID);
		if ($userID == null) return false;
		else return mysql_num_rows(mysql_query("SELECT userID FROM users WHERE userID =$userID;"));
	}
	static function getUser ($userID) {
		$userID = intval($userID);
		//echo $userID;
		//$role = mysql_fetch_assoc(mysql_query("SELECT role as r FROM users where userID=$userID;"));			
		//errorMessage("Get user ".$role, "error.php");	
		//errorMessage("Get user student ".$role , "error.php");	
	//	if($role['r'] == 1){			
		return mysql_fetch_assoc(mysql_query("SELECT* from users WHERE userID = $userID;"));

		//}
	}
	static function getUserList () {
		$q = mysql_query("SELECT * FROM users;");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
static function getAdminList () {
		$q = mysql_query("SELECT * FROM users u INNER JOIN Administrator a ON u.userID=a.userID AND u.role=1;");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	
	static function getTeacherList () {
		$q = mysql_query("SELECT * FROM users u INNER JOIN teacher t ON u.userID=t.userID AND u.role=2;");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}

	static function getClassStd ($c) {
		$q = mysql_query("SELECT * FROM users u INNER JOIN student s ON u.userID=s.userID AND u.role=3 AND s.classid=$c;");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}

	static function getStudentList () {
		$q = mysql_query("SELECT * FROM users u INNER JOIN student s ON u.userID=s.userID AND u.role=3;");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	
		static function getEventStudent ($eid) {
		$q = mysql_query("SELECT u.name, u.email, s.class FROM users u INNER JOIN students s ON u.userID =s.student_id INNER JOIN studentevents se ON u.userID =se.student_id AND se.event_id=$eid WHERE flag=1;");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	
	static function countUsers() {
		$courseID = intval($courseID);
		$q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM users;"));
		return intval($q['num']);
	}
	
	/*static function updateClass ($userID) {
		$userID = intval($userID);
		$data = mysql_fetch_assoc(mysql_query("SELECT classes.class, classes.registerNum, classes.year FROM classes WHERE classes.userID=$userID ORDER BY classes.year DESC LIMIT 1"));
		$data['class'] = mysql_escape_string($data['class']);
		$data['registerNum'] = intval($data['registerNum']);
		$data['year'] = intval($data['year']);
		mysql_query("UPDATE users SET class='$data[class]', year=$data[year], registerNum=$data[registerNum] WHERE userID=$userID");
	}*/
	 static function validate ($username, $password) {	
		$username = mysql_real_escape_string($username);
		$password = md5($password);
		$data = mysql_fetch_assoc(mysql_query("SELECT userID FROM users WHERE username='$username' AND password='$password'"));		
		if (!$data) return false;
		else {
			session_start();
			$_SESSION['userID'] = intval($data['userID']);
			session_write_close();
			return true;
		}
	}
	
static function validateUser ($data,$userID=-1) {
	$data['dob'] = $data['dob_day']."-".$data['dob_month']."-".$data['dob_year'];
		$fieldsToCheck = array('fname', 'lname', 'password', 'repeatpassword','address','sex', 'dob_day' , 'dob_month', 'dob_year',  'email', 'telno', 'icno','join_day', 'join_month','join_year');
		foreach ($fieldsToCheck as $k => $field) {
			if (!isset($data[$field]) || $data[$field] == "") {
				errorMessage2("Field '$field' cannot be left blank!");
				return false;
			}
		}
			
		$data['email'] = mysql_real_escape_string($data['email']);
		$sameUser = mysql_query("SELECT userID FROM users WHERE email = '$data[email]';");
		if (mysql_num_rows($sameUser) != 0) {
			$q = mysql_fetch_assoc($sameUser);
			if ($q['userID'] != $userID) {
				errorMessage2("Email is not unique, it is similar to user with userID $q[email]");
				return false;
			}
		}				
		/*if(!valid_date($data['dob'])){
			errorMessage("Please enter the correct date of birth format!");
			return false;
		}
		if(!valid_date($data['date_employed'])){
			errorMessage("Please enter the correct date employed format!");
			return false;
		}*/
		if ($data['password'] != $data['repeatpassword']) {
			errorMessage2("Passwords do not match!");
			return false;
		}
		/////Check date format if ddmmyy///////
		
		if ($data['password'] != $data['repeatpassword']) {
			errorMessage2("Passwords do not match!");
			return false;
		}
		/*$data['batchID'] = intval($data['batchID']);
		$batch = mysql_fetch_assoc(mysql_query("SELECT * FROM batches WHERE batchID = $data[batchID];"));
		if (!$batch) {
			errorMessage("Specified batch does not exist. Are you sure you didn't hack the system?");
			return false;
		}*/
		return true;
	}
	static function addUser ($data) {
			if($data['role'] == 1){
		$data['fname'] = mysql_real_escape_string($data['fname']);
		$data['lname'] = mysql_real_escape_string($data['lname']);
		$data['address'] = mysql_real_escape_string($data['address']);
		$data['sex'] = mysql_real_escape_string($data['sex']);
		$data['class'] = mysql_real_escape_string($data['class']);
		$data['age'] = mysql_real_escape_string($data['age']);
		$data['dob'] = $data['dob_day']."-".$data['dob_month']."-".$data['dob_year'];
		//$data['dob'] = $data['dob_day']."-".$data['dob_month']."-".$data['dob_year'];
		//$data['dob'] = mysql_real_escape_string($data['dob']);
		$data['icno'] = mysql_real_escape_string($data['icno']);
		$data['date_employed'] = $data['join_day']."-".$data['join_month']."-".$data['join_year'];
		//$data['date_employed'] = mysql_real_escape_string($data['date_employed']);
		$data['mobileno'] = mysql_real_escape_string($data['mobileno']);
		$data['telno'] = mysql_real_escape_string($data['telno']);		
		$data['password'] = md5($data['password']);
		$data['email'] = mysql_real_escape_string($data['email']);
		$data['position'] = mysql_real_escape_string($data['position']);
		$data['status'] = mysql_real_escape_string($data['status']);
		$data['income'] = mysql_real_escape_string($data['income']);
		//$data['class'] = mysql_real_escape_string($data['class']);		
		$data['teachpic'] = mysql_real_escape_string($data['teachpic']);
		$data['role'] = mysql_real_escape_string($data['role']);
		$data['role'] = mysql_real_escape_string($data['role']);
		/*$div = array();
		foreach ($data['division'] as $k => $v) {
			$div[] = mysql_real_escape_string($v);
		}
		$divStr = implode(", ", $div);*/
		
		mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		
		$s1 = mysql_query("INSERT INTO users (email, password, role, role)
		VALUES ('$data[email]', '$data[password]', '$data[role]', '$data[role]')");
		$lastid = mysql_insert_id();
		$s2 = mysql_query("INSERT INTO administrator (userID,fname, lname, address, sex, age, dob,icno, date_employed, mobileno, telno,position, status, income) VALUES ('$lastid', '$data[fname]','$data[lname]', '$data[address]','$data[sex]','$data[age]','$data[dob]','$data[icno]','$data[date_employed]','$data[mobileno]','$data[telno]','$data[position]','$data[status]','$data[income]')");				
		if ($s1 and $s2) {
    		mysql_query("COMMIT");
} else {        
    mysql_query("ROLLBACK");
}
		}else if($data['role'] == 2){
		$data['fname'] = mysql_real_escape_string($data['fname']);
		$data['lname'] = mysql_real_escape_string($data['lname']);
		$data['address'] = mysql_real_escape_string($data['address']);
		$data['sex'] = mysql_real_escape_string($data['sex']);
		$data['class'] = mysql_real_escape_string($data['class']);
		$data['age'] = mysql_real_escape_string($data['age']);
		$data['dob'] = $data['dob_day']."-".$data['dob_month']."-".$data['dob_year'];
		//$data['dob'] = $data['dob_day']."-".$data['dob_month']."-".$data['dob_year'];
		//$data['dob'] = mysql_real_escape_string($data['dob']);
		$data['icno'] = mysql_real_escape_string($data['icno']);
		$data['date_employed'] = $data['join_day']."-".$data['join_month']."-".$data['join_year'];
		//$data['date_employed'] = mysql_real_escape_string($data['date_employed']);
		$data['mobileno'] = mysql_real_escape_string($data['mobileno']);
		$data['telno'] = mysql_real_escape_string($data['telno']);		
		$data['password'] = md5($data['password']);
		$data['email'] = mysql_real_escape_string($data['email']);
		$data['position'] = mysql_real_escape_string($data['position']);
		$data['status'] = mysql_real_escape_string($data['status']);
		$data['income'] = mysql_real_escape_string($data['income']);
		//$data['class'] = mysql_real_escape_string($data['class']);		
		$data['teachpic'] = mysql_real_escape_string($data['teachpic']);
		$data['role'] = mysql_real_escape_string($data['role']);
		$data['role'] = mysql_real_escape_string($data['role']);
		/*$div = array();
		foreach ($data['division'] as $k => $v) {
			$div[] = mysql_real_escape_string($v);
		}
		$divStr = implode(", ", $div);*/
		
		mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		
		$s1 = mysql_query("INSERT INTO users (email, password, role, role)
		VALUES ('$data[email]', '$data[password]', '$data[role]', '$data[role]')");
		$lastid = mysql_insert_id();
		$s2 = mysql_query("INSERT INTO teacher (userID,fname, lname, address, sex, age, dob,icno, date_employed, mobileno, telno,position, status, income) VALUES ('$lastid', '$data[fname]','$data[lname]', '$data[address]','$data[sex]','$data[age]','$data[dob]','$data[icno]','$data[date_employed]','$data[mobileno]','$data[telno]','$data[position]','$data[status]','$data[income]')");				
		if ($s1 and $s2) {
    		mysql_query("COMMIT");
} else {        
    mysql_query("ROLLBACK");
}
		}else if($data['role'] == 3){
		$data['fname'] = mysql_real_escape_string($data['fname']);
		$data['lname'] = mysql_real_escape_string($data['lname']);
		$data['address'] = mysql_real_escape_string($data['address']);
		$data['sex'] = mysql_real_escape_string($data['sex']);
		$class_arr = explode(",",$data['class']);
		$data['class'] = mysql_real_escape_string($class_arr[0])." ".mysql_real_escape_string($class_arr[1]);	
		$data['classid'] = intval($class_arr[2]);
		$data['age'] = mysql_real_escape_string($data['age']);
		$data['dob'] = $data['dob_day']."-".$data['dob_month']."-".$data['dob_year'];
		//$data['dob'] = $data['dob_day']."-".$data['dob_month']."-".$data['dob_year'];
		//$data['dob'] = mysql_real_escape_string($data['dob']);
		$data['icno'] = mysql_real_escape_string($data['icno']);
		$data['date_enrol'] = $data['join_day']."-".$data['join_month']."-".$data['join_year'];
		//$data['date_employed'] = mysql_real_escape_string($data['date_employed']);
		$data['mobileno'] = mysql_real_escape_string($data['mobileno']);
		$data['telno'] = mysql_real_escape_string($data['telno']);		
		$data['password'] = md5($data['password']);
		$data['email'] = mysql_real_escape_string($data['email']);
		//$data['class'] = mysql_real_escape_string($data['class']);		
		$data['studpic'] = mysql_real_escape_string($data['studpic']);
		$data['role'] = mysql_real_escape_string($data['role']);
		$data['role'] = mysql_real_escape_string($data['role']);
		/*$div = array();
		foreach ($data['division'] as $k => $v) {
			$div[] = mysql_real_escape_string($v);
		}
		$divStr = implode(", ", $div);*/
		
		mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		
		$s1 = mysql_query("INSERT INTO users (email, password, role, role)
		VALUES ('$data[email]', '$data[password]', '$data[role]', '$data[role]')");
		$lastid = mysql_insert_id();
		$s2 = mysql_query("INSERT INTO student (userID,fname, lname, address, sex, age, dob,icno, date_enrol, mobileno, telno,classid,class) VALUES ('$lastid', '$data[fname]','$data[lname]', '$data[address]','$data[sex]','$data[age]','$data[dob]','$data[icno]','$data[date_enrol]','$data[mobileno]','$data[telno]','$data[classid]','$data[class]')");				
		if ($s1 and $s2) {
    		mysql_query("COMMIT");
} else {        
    mysql_query("ROLLBACK");
}
			
		}
		return $lastid;		
	}
	
	static function editUser ($data, $userID) {
		$userID = intval($userID);
		if($data['role'] == 1){
		$data['fname'] = mysql_real_escape_string($data['fname']);
		$data['lname'] = mysql_real_escape_string($data['lname']);
		$data['address'] = mysql_real_escape_string($data['address']);
		$data['sex'] = mysql_real_escape_string($data['sex']);
		$data['class'] = mysql_real_escape_string($data['class']);
		$data['age'] = mysql_real_escape_string($data['age']);
		$data['dob'] = $data['dob_day']."-".$data['dob_month']."-".$data['dob_year'];
		$data['icno'] = mysql_real_escape_string($data['icno']);
		$data['date_employed'] = $data['join_day']."-".$data['join_month']."-".$data['join_year'];
		$data['mobileno'] = mysql_real_escape_string($data['mobileno']);
		$data['telno'] = mysql_real_escape_string($data['telno']);		
		if ($data['password'] != "") $data['password'] = md5($data['password']);
		$data['email'] = mysql_real_escape_string($data['email']);
		$data['position'] = mysql_real_escape_string($data['position']);
		$data['status'] = mysql_real_escape_string($data['status']);
		$data['income'] = mysql_real_escape_string($data['income']);
		//$data['class'] = mysql_real_escape_string($data['class']);		
		$data['teachpic'] = mysql_real_escape_string($data['teachpic']);
		$data['role'] = mysql_real_escape_string($data['role']);
		$data['role'] = mysql_real_escape_string($data['role']);
		
		mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		
		if ($data['password'] == "") {
		$s1 = mysql_query("UPDATE users SET email='$data[email]', role='$data[role]', role='$data[role]' WHERE userID = $userID;");
		$s2 = mysql_query("UPDATE administrator fname='$data[fname]', lname='$data[lname]', address='$data[address]', sex='$data[sex]', age='$data[age]', dob='$data[dob]', icno='$data[icno]', date_employed='$data[date_employed]', mobileno='$data[mobileno]', telno='$data[telno]', position='$data[position]' , status='$data[status]', income='$data[income]' WHERE userID = $userID;");
	}
		else {
		$s1 = mysql_query("UPDATE users SET email='$data[email]', role='$data[role]', role='$data[role]' WHERE userID = $userID;");
		$s2 = mysql_query("UPDATE administrator fname='$data[fname]', lname='$data[lname]', address='$data[address]', sex='$data[sex]', age='$data[age]', dob='$data[dob]', icno='$data[icno]', date_employed='$data[date_employed]', mobileno='$data[mobileno]', telno='$data[telno]', position='$data[position]' , password='$data[password]', status='$data[status]', income='$data[income]' WHERE userID = $userID;");
		}
	}else if($data['role'] == 2){
		$data['fname'] = mysql_real_escape_string($data['fname']);
		$data['lname'] = mysql_real_escape_string($data['lname']);
		$data['address'] = mysql_real_escape_string($data['address']);
		$data['sex'] = mysql_real_escape_string($data['sex']);
		$data['class'] = mysql_real_escape_string($data['class']);
		$data['age'] = mysql_real_escape_string($data['age']);
		$data['dob'] = $data['dob_day']."-".$data['dob_month']."-".$data['dob_year'];
		$data['icno'] = mysql_real_escape_string($data['icno']);
		$data['date_employed'] = $data['join_day']."-".$data['join_month']."-".$data['join_year'];
		$data['mobileno'] = mysql_real_escape_string($data['mobileno']);
		$data['telno'] = mysql_real_escape_string($data['telno']);		
		if ($data['password'] != "") $data['password'] = md5($data['password']);
		$data['email'] = mysql_real_escape_string($data['email']);
		$data['position'] = mysql_real_escape_string($data['position']);
		$data['status'] = mysql_real_escape_string($data['status']);
		$data['income'] = mysql_real_escape_string($data['income']);
		//$data['class'] = mysql_real_escape_string($data['class']);		
		$data['teachpic'] = mysql_real_escape_string($data['teachpic']);
		$data['role'] = mysql_real_escape_string($data['role']);
		$data['role'] = mysql_real_escape_string($data['role']);
		
		mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		
		if ($data['password'] == "") {
		$s1 = mysql_query("UPDATE users SET email='$data[email]', role='$data[role]', role='$data[role]' WHERE userID = $userID;");
		$s2 = mysql_query("UPDATE teacher fname='$data[fname]', lname='$data[lname]', address='$data[address]', sex='$data[sex]', age='$data[age]', dob='$data[dob]', icno='$data[icno]', date_employed='$data[date_employed]', mobileno='$data[mobileno]', telno='$data[telno]', position='$data[position]' , status='$data[status]', income='$data[income]' WHERE userID = $userID;");
	}
		else {
		$s1 = mysql_query("UPDATE users SET email='$data[email]', role='$data[role]', role='$data[role]' WHERE userID = $userID;");
		$s2 = mysql_query("UPDATE teacher fname='$data[fname]', lname='$data[lname]', address='$data[address]', sex='$data[sex]', age='$data[age]', dob='$data[dob]', icno='$data[icno]', date_employed='$data[date_employed]', mobileno='$data[mobileno]', telno='$data[telno]', position='$data[position]' , password='$data[password]', status='$data[status]', income='$data[income]' WHERE userID = $userID;");
		}
	}
	else if($data['role'] == 3){
		$data['fname'] = mysql_real_escape_string($data['fname']);
		$data['lname'] = mysql_real_escape_string($data['lname']);
		$data['address'] = mysql_real_escape_string($data['address']);
		$data['sex'] = mysql_real_escape_string($data['sex']);
		$class_arr = explode(",",$data['class']);
		$data['class'] = mysql_real_escape_string($class_arr[0])." ".mysql_real_escape_string($class_arr[1]);	
		$data['classid'] = intval($class_arr[2]);
		$data['age'] = mysql_real_escape_string($data['age']);
		$data['dob'] = $data['dob_day']."-".$data['dob_month']."-".$data['dob_year'];
		$data['icno'] = mysql_real_escape_string($data['icno']);
		$data['date_enrol'] = $data['join_day']."-".$data['join_month']."-".$data['join_year'];
		$data['mobileno'] = mysql_real_escape_string($data['mobileno']);
		$data['telno'] = mysql_real_escape_string($data['telno']);		
		if ($data['password'] != "") $data['password'] = md5($data['password']);
		$data['email'] = mysql_real_escape_string($data['email']);
		$data['position'] = mysql_real_escape_string($data['position']);
		$data['status'] = mysql_real_escape_string($data['status']);
		$data['income'] = mysql_real_escape_string($data['income']);
		//$data['class'] = mysql_real_escape_string($data['class']);		
		$data['teachpic'] = mysql_real_escape_string($data['teachpic']);
		$data['role'] = mysql_real_escape_string($data['role']);
		$data['role'] = mysql_real_escape_string($data['role']);
		
		mysql_query("SET AUTOCOMMIT=0");
		mysql_query("START TRANSACTION");
		
		if ($data['password'] == "") {
		$s1 = mysql_query("UPDATE users SET email='$data[email]', role='$data[role]', role='$data[role]' WHERE userID = $userID;");
		$s2 = mysql_query("UPDATE student fname='$data[fname]', lname='$data[lname]', address='$data[address]', sex='$data[sex]', age='$data[age]', dob='$data[dob]', icno='$data[icno]', date_employed='$data[date_enrol]', mobileno='$data[mobileno]', telno='$data[telno]',classid='$data[classid]',class='$data[class]' WHERE userID = $userID;");
	}
		else {
		$s1 = mysql_query("UPDATE users SET email='$data[email]', role='$data[role]', role='$data[role]' WHERE userID = $userID;");
		$s2 = mysql_query("UPDATE student fname='$data[fname]', lname='$data[lname]', address='$data[address]', sex='$data[sex]', age='$data[age]', dob='$data[dob]', icno='$data[icno]', date_employed='$data[date_employed]', mobileno='$data[mobileno]', telno='$data[telno]',,classid='$data[classid]',class='$data[class]', password='$data[password]' WHERE userID = $userID;");
		}
	}
	if ($s1 and $s2) {
    		mysql_query("COMMIT");
} else {        
    mysql_query("ROLLBACK");
}
		
	}
	static function searchName ($query) {
		$query = mysql_real_escape_string($query);
		$result = array();
		$q = mysql_query("SELECT name, userID FROM users WHERE MATCH(name) AGAINST('$query');");
		while ($i = mysql_fetch_assoc($q)) {
			$result[] = $i;
		}
		return $result;
	}
	static function selectList ($checked = array()) {
		$batches = Users::getBatchList();
		$string = "";
		foreach ($batches as $k => $batch) {
			$batchStr = htmlentities("$batch[startyear] - $batch[endyear] ($batch[level])");
			$batch['startyear'] = intval($batch['startyear']);
			$query = mysql_query("SELECT name, userID FROM users WHERE startyear=$batch[startyear] ORDER BY name ASC");
			if (mysql_num_rows($query) == 0) continue;
			$string .= "<optgroup label='$batchStr'>";
			while ($r = mysql_fetch_assoc($query)) {
				$str = "";
				if (in_array(intval($r['userID']), $checked)) $str = "selected checked";
				$string .= "<option value='$r[userID]' $str >".htmlentities($r['name'])."</option>";
			}
			$string .= "</optgroup>";
		}
		return $string;
		/*
		$query = mysql_query("SELECT name, userID FROM users ORDER BY name ASC");
		$string = "";
		while ($r = mysql_fetch_assoc($query)) {
			$str = "";
			if (in_array(intval($r['userID']), $checked)) $str = "selected checked";
			$string .= "<option value='$r[userID]' $str >".htmlentities($r['name'])."</option>";
		}
		return $string;
		*/
	}
	
	static function getDivisionMemberList ($division) {
		$division = mysql_real_escape_string($division);
		$query = mysql_query("SELECT users.*, batches.endyear, batches.level FROM users INNER JOIN batches ON batches.startyear = users.startyear WHERE division LIKE '%$division%' ORDER BY startyear DESC, class ASC, name ASC");
		$data = array();
		while ($r = mysql_fetch_assoc($query)) {
			$r['batch'] = "$r[startyear] - $r[endyear] ($r[level])";
			$data[] = $r;
		}
		return $data;
	}
	static function levelSelectList ($checked = array()) {
		$result = mysql_query("SHOW COLUMNS FROM batches LIKE 'level'");
		$row = mysql_fetch_array( $result , MYSQL_NUM );
		$regex = "/'(.*?)'/";
		preg_match_all( $regex , $row[1], $enum_array );
		$enum_fields = $enum_array[1];
		$string = "";
		foreach ($enum_fields as $k => $v) {
			$str = "";
			if (in_array($v, $checked)) $str = "selected checked";
			$string .= "<option value='".htmlentities($v)."' $str>".htmlentities($v)."</option>";
		}
		return $string;
	}
static function getTeachingClass($uid){		
	$data = array();
		//$q = mysql_query("SELECT * FROM subjects ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, subjectsID DESC");
		$q = mysql_query("SELECT s.subjname FROM subject s INNER JOIN subjTeachers st ON s.subjectsID=st.subjectsID WHERE st.userID = $uid;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;			
	}

	static function classExists ($classID) {
		$classid = intval($classID);
		if ($classid == null) return false;
		else return mysql_num_rows(mysql_query("SELECT classid FROM classgrp WHERE classid =$classid;"));
	}
	static function getClass ($classID) {
		$classid = intval($classID);
		//echo $userID;		
		return mysql_fetch_assoc(mysql_query("SELECT * FROM classgrp WHERE classid = $classid;"));
	}

	static function getClassList() {
		$data = array();
		$q = mysql_query("SELECT * FROM classgrp ORDER BY year DESC");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	
	
	static function batchSelectList($checked = array()) {
		$query = mysql_query("SELECT * FROM batches ORDER BY startyear DESC");
		$string = "";
		while ($r = mysql_fetch_assoc($query)) {
			$str = "";
			if (in_array(intval($r['batchID']), $checked)) $str = "selected";
			$string .= "<option value='$r[batchID]' $str >".htmlentities("$r[startyear] - $r[endyear] ($r[level])")."</option>";
		}
		return $string;
	
	}
	static function countBatches() {
		$q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM batches;"));
		return intval($q['num']);
	}
	
	static function validateClass($data) {
		$fieldsToCheck = array('level', 'classname', 'year');
		foreach ($fieldsToCheck as $k => $field) {
			if (!isset($data[$field]) || $data[$field] == "") {
				errorMessage2("Field '$field' cannot be left blank!");
				return false;
			}
		}
		$data['level'] = intval($data['level']);
		$data['classname'] = mysql_real_escape_string($data['classname']);
		$data['year'] = intval($data['year']);
		
		return true;
	}
	static function addClass ($data) {
		$data['level'] = intval($data['level']);
		$data['classname'] = mysql_real_escape_string($data['classname']);
		$data['year'] = intval($data['year']);
		mysql_query("INSERT INTO classgrp (level, classname, year) VALUES ($data[level], '$data[classname]', $data[year])");
		return mysql_insert_id();
	}
	static function editClass ($data, $classID) {
		$classID = intval($classID);
		$data['level'] = intval($data['startyear']);
		$data['classname'] = mysql_real_escape_string($data['classname']);
		$data['year'] = intval($data['year']);
		mysql_query("UPDATE batches SET level=$data[level], classname='$data[classname]', year=$data[year] WHERE classid=$classID");
	}
	
	

	static function addBatch ($data) {
		$data['startyear'] = intval($data['startyear']);
		$data['endyear'] = intval($data['endyear']);
		$data['level'] = mysql_real_escape_string($data['level']);
		mysql_query("INSERT INTO batches (startyear, endyear, level) VALUES ($data[startyear], $data[endyear], '$data[level]')");
		return mysql_insert_id();
	}
	static function editBatch ($data, $batchID) {
		$batchID = intval($batchID);
		$data['startyear'] = intval($data['startyear']);
		$data['endyear'] = intval($data['endyear']);
		$data['level'] = mysql_real_escape_string($data['level']);
		mysql_query("UPDATE batches SET startyear=$data[startyear], endyear=$data[endyear], level='$data[level]' WHERE batchID=$batchID");
	}
	static function deleteBatch ($batchID) {
		$batchID = intval($batchID);
		mysql_query("DELETE FROM batches WHERE batchID=$batchID");
	}

	static function navBar($userID) {
		$userID = intval($userID);
		$userData = Users::getUser($userID);
		//$batchList = Users::getBatchList();
		//if(strcmp($userData['role'],'admin')){
		if($userData['role'] == 1){	
		$adminView =  1;
		}
		$roleView = $userData['role'];
		$output = "<div class='well'><ul class='nav nav-list'> ";
		$output .= "<li class='nav-header'>Manage User Accounts</li>";
		$output .= "<li><a href='allAdmin.php'><i class='icon-user'></i>View Administrators</a></li>";
		$output .= "<li><a href='allteachers.php'><i class='icon-user'></i>View Teachers</a></li>";
		$output .= "<li><a href='allStudents.php'><i class='icon-user'></i>View Students</a></li>";
		$output .= "<li><a href='viewParents.php'><i class='icon-user'></i>View Parents</a></li>";
		$output .= "<li><a href='allclass.php'><i class='icon-user'></i>View Class</a></li>";		
		/*$output .= "<ul class='dropdown-menu'>";
		foreach($batchList as $k => $v) {
			$output .= "<li><a href='batchList.php#$v[startyear]'>$v[startyear] - $v[endyear] ($v[level])</a></li>";
		}
		$output .= "</ul></li>";*/	
		
		//$output .= "<li class='dropdown'><a class='dropdown-toggle' data-toggle='dropdown' href='#'><i class='icon-list-alt'></i>My Courses<b class='caret'></b><span class='badge badge-warning pull-right'>".Courses::countCourses($userID)."</span></a>";
		//$output .= "<ul class='dropdown-menu'><li><a href='myCourses.php#attending'>Attending</a></li><li><a href='myCourses.php#registered'>Registered</a></li><li><a href='myCourses.php#completed'>Completed</a></li><li><a href='myCourses.php#facilitating'>Facilitating</a></li></ul></li>";
		//$output .= "<li><a href='courseList.php'><i class='icon-list-alt'></i>My Courses<span class='badge badge-warning pull-right'>".Courses::countCourses()."</span></a></li>";
		if ($adminView) $output .= "<li><a href='addAdmin.php'><i class='icon-plus'></i>Add New Administrator</a></li>";
		if ($adminView) $output .= "<li><a href='addTeacher.php'><i class='icon-plus'></i>Add New Teacher</a></li>";
		if ($adminView) $output .= "<li><a href='addParent.php'><i class='icon-plus'></i>Add New Parent</a></li>";
		if ($adminView) $output .= "<li><a href='addStudent.php'><i class='icon-plus'></i>Add New Student</a></li>";
		if ($adminView) $output .= "<li><a href='addClass.php'><i class='icon-plus'></i>Add New Class</a></li>";
		//Add subjects Add assessments Add classes Add Marks Add Hall of Fame
		$output .= "</ul></div>";
		return $output;
	}
	///
	static function userNavBar($userID, $targetID) {
		$targetID = intval($targetID);
		$userID = intval($userID);
		$user = Users::getUser($userID);
		$userData = Users::getUser($targetID);
		$roleView = $userData['role'];
		if($userData['role'] == 1){
		$adminView =  1;
		}
		
		$output = "<div class='well'><ul class='nav nav-list'> ";
		$output .= "<li class='nav-header'>".htmlentities($user['fname'])."</li>";
		if ($adminView) {						
			if($user['role'] == 1){
		$output .= "<li><a href='editAdmin.php?userID=$userID'><i class='icon-edit'></i>Edit Admin</a></li>";	
		} else if($user['role'] == 2){
		$output .= "<li><a href='editTeacher.php?userID=$userID'><i class='icon-edit'></i>Edit Teacher</a></li>";	
		} else if($user['role'] == 3){
				$output .= "<li><a href='editStudent.php?userID=$userID'><i class='icon-edit'></i>Edit Student</a></li>";	
		}
		else if($$user['role'] == 4){
			$output .= "<li><a href='editParent.php?userID=$userID'><i class='icon-user'></i>Edit Parent</a></li>";
		}

		}
		/*
		*/
		if ($targetID == $userID){
			switch ($roleView) {    	
    		case 2:
        	$output .= "<li><a href='editTeacher.php?userID=$userID'><i class='icon-refresh'></i>Update Particulars</a></li>";
        	break;
    		case 3:
        	$output .= "<li><a href='editStudent.php?userID=$userID'><i class='icon-refresh'></i>Update Particulars</a></li>";
        	break;
			case 4:
        	$output .= "<li><a href='editParent.php?userID=$userID'><i class='icon-refresh'></i>Update Particulars</a></li>";
        	break;
			}
      }			
		
		$output .= "</ul></div>";
		return $output;
	}
}
?>
