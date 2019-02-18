<?php
require_once("connect.php");
require_once('class.upload.php');
//require_once("users.php");
class Riken {

	static function rikensExists ($riken_id) {
		$riken_id = intval($riken_id);
		if ($riken_id == null) return false;
		else return (mysql_num_rows(mysql_query("SELECT riken_id FROM rikens WHERE riken_id=$riken_id;")) == 1);
	}

	static function getRikenPhoto ($riken_id) {
		$riken_id = intval($riken_id);
		if(mysql_num_rows(mysql_query("SELECT image FROM rikenextras WHERE riken_id=$riken_id;"))>0){
			return true;
		}else{
			return false;
		}
	}	
	static function getRikens ($riken_id, $isImg) {
		$riken_id = intval($riken_id);
		if($isImg){
		return mysql_fetch_assoc(mysql_query("SELECT e.riken_id, e.type, e.title, e.description, e.evtDate, e.userID, e.group_id, e.schoolID, ex.image FROM rikens e INNER JOIN rikenextras ex ON e.riken_id=ex.riken_id WHERE e.riken_id=$riken_id"));
		}else{
			return mysql_fetch_assoc(mysql_query("SELECT * FROM rikens WHERE riken_id=$riken_id;"));
		}
	}
	static function getRikenList ($user_id) {
		$data = array();
		//$q = mysql_query("SELECT * FROM rikens ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, riken_id DESC");
		$q = mysql_query("SELECT e.riken_id,e.title, e.description,e.evtDate,g.group_name from rikens e INNER JOIN groups g ON e.group_id=g.group_id WHERE e.userID=$user_id;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}

	static function countRikens ($userID = -1) {
		$userID = intval($userID);
		$q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM riken;"));
		/*if ($userID == -1) $q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM riken;"));
		else $q = mysql_fetch_assoc(mysql_query("SELECT COUNT(pre.riken_id) as num FROM (SELECT riken_id FROM signups WHERE userID=$userID UNION SELECT riken_id FROM facilitators WHERE userID=$userID) as pre"));*/
		return intval($q['num']);
	}
	
	static function validateRikens ($data) {
		$fieldsToCheck = array('title', 'group_id','evtDate','description');
		foreach ($fieldsToCheck as $k => $field) {
			if (!isset($data[$field]) || $data[$field] == "") {
				errorMessage("Field '$field' cannot be left blank!");
				return false;
			}
		}	
		return true;
	}

	static function validateImage($evtID,$file){
			
			$picInfo = getimagesize($file['picture']['tmp_name']);
			if (!$picInfo) {
				errorMessage("The file upload is not a picture.");
				return false;	
				}
			else {
				$ext = substr($file['picture']['name'],strpos($file['picture']['name'],'.')+1,strlen($file['picture']['name'])-1);
				$mime = array('image/gif' => 'gif',
		                  'image/jpeg' => 'jpeg',
		                  'image/png' => 'png',
		                  'application/x-shockwave-flash' => 'swf',
		                  'image/psd' => 'psd',
		                  'image/bmp' => 'bmp',
		                  'image/tiff' => 'tiff',
		                  'image/tiff' => 'tiff',
		                  'image/jp2' => 'jp2',
		                  'image/iff' => 'iff',
		                  'image/vnd.wap.wbmp' => 'bmp',
		                  'image/xbm' => 'xbm',
		                  'image/vnd.microsoft.icon' => 'ico');
				$allowed = array('jpeg', 'png', 'gif', 'bmp');
				$fileMime = $picInfo['mime'];
				$newExt = $mime[$fileMime];
				if (!in_array($newExt, $allowed)) {
					return false;	
				}
				else {
					$file['picture']['name'] .= ".$newExt";
					//$picLocation = "riken_imgs/$itemID/".$file['picture']['name'];
					//echo $itemID."<br><img src='$picLocation'><br>";
				}	
			}
				return true;	
	}

	static function addRikens ($edata) {

		$edate = new DateTime($edata['evtDate']); // format: MM/DD/YYYY
		$edata['title'] = mysql_real_escape_string($edata['title']);		
		$edata['type'] = mysql_real_escape_string($edata['type']);		
		$edata['evtDate'] = $edate->format('U'); 
		//$data['posted'] = $data['post_day']."-".$data['post_month']."-".$data['post_year'];
		$edata['description'] = mysql_real_escape_string($edata['description']);
		$edata['userID'] = mysql_real_escape_string($edata['userID']);
		$edata['group_id'] = mysql_real_escape_string($edata['group_id']);
		$edata['schoolID'] = mysql_real_escape_string($edata['schoolID']);
		
            // everything was fine !
		mysql_query("INSERT INTO rikens (title, type, evtDate, userID,group_id,schoolID,description) VALUES ('$edata[title]','$edata[type]','$edata[evtDate]','$edata[userID]','$edata[group_id]', '$edata[schoolID]', '$edata[description]')");
			
		$riken_id = mysql_insert_id();
		return $riken_id;               
	}

	static function editRikens ($data, $riken_id) {
		$riken_id = intval($riken_id);
		$edate = new DateTime($data['evtDate']); // format: MM/DD/YYYY
		$data['title'] = mysql_real_escape_string($data['title']);		
		$data['evtDate'] = $edate->format('U');
		$data['type'] = mysql_real_escape_string($data['type']);		
		//$data['posted'] = $data['post_day']."-".$data['post_month']."-".$data['post_year'];
		$data['description'] = mysql_real_escape_string($data['description']);
		$data['userID'] = mysql_real_escape_string($data['userID']);
		$data['group_id'] = mysql_real_escape_string($data['group_id']);
		$data['schoolID'] = mysql_real_escape_string($data['schoolID']);
		
		mysql_query("UPDATE rikens SET title='$data[title]',  type='$data[type]', evtDate='$data[evtDate]', userID='$data[userID]', group_id='$data[group_id]', schoolID='$data[schoolID]', description='$data[description]' WHERE riken_id=$riken_id;");
		
	}
	static function deleteRikens ($riken_id) {
		$riken_id = intval($riken_id);
		mysql_query("DELETE FROM riken WHERE riken_id=$riken_id");
	/*	mysql_query("DELETE FROM attendance WHERE lessonID IN (SELECT lessonID FROM lessons WHERE riken_id=$riken_id)");
		mysql_query("DELETE FROM lessons WHERE riken_id=$riken_id");
		mysql_query("DELETE FROM signups WHERE riken_id=$riken_id");
		mysql_query("DELETE FROM facilitators WHERE riken_id=$riken_id");*/
	}
	
static function attachPhoto ($iid, $uploadedfile) {
	$PIC_FOLDER = "riken_img/";
	$iid = intval($iid);
	$targetDir = $PIC_FOLDER.$iid;
	 ini_set("max_execution_time",0);

    // we don't upload, we just send a local filename (image)
    $handle = new Upload($uploadedfile);
	$picName = $uploadedfile['name'];
    if ($handle->uploaded) {
        // now, we start a serie of processes, with different parameters
        // we use a little function TestProcess() to avoid repeting the same code too many times
        if (!file_exists($targetDir)) {
        	mkdir($targetDir,0777,true);
       	   }
	
		}
		$handle->image_resize          = true;
        $handle->image_x               = 120;
        $handle->image_y               = 40;
		  $handle->Process($targetDir);
            if ($handle->processed) {
            mysql_query("INSERT INTO rikenextras (riken_id, image) VALUES ($iid, '$picName');");
             return 1;
            } else {
              return 0;
            }
	
}
static function deletePhoto ($iid){
	$PIC_FOLDER = "riken_img/";
	$iid = intval($iid);
	$targetDir = $PIC_FOLDER.$iid;
	$picName = $uploadedfile['name'];

 if (file_exists($targetDir)) {
 	 $objects = scandir($targetDir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         unlink($targetDir."/".$object); 
       } 
     } 
     reset($objects); 
     rmdir($targetDir); 
      mysql_query("DELETE FROM rikenextras WHERE riken_id='$iid';");
 }
}
static function editPhoto ($iid, $uploadedfile) {
	$PIC_FOLDER = "riken_img/";
	$iid = intval($iid);
	$targetDir = $PIC_FOLDER.$iid;
	 ini_set("max_execution_time",0);

    // we don't upload, we just send a local filename (image)
    $handle = new Upload($uploadedfile);
	$picName = $uploadedfile['name'];
    if ($handle->uploaded) {
        // now, we start a serie of processes, with different parameters
        // we use a little function TestProcess() to avoid repeting the same code too many times
        if (!file_exists($targetDir)) {
        	mkdir($targetDir,0777,true);
       	   }
	
		}
		$handle->image_resize          = true;
        $handle->image_x               = 120;
        $handle->image_y               = 40;
		  $handle->Process($targetDir);
            if ($handle->processed) {
            mysql_query("UPDATE rikenextras SET image = '$picName' WHERE riken_id='$iid';");
          
             return 1;
            } else {
              return 0;
            }
	
}

	static function getSubjClass($riken_id,$classid){					
		if(mysql_num_rows(mysql_query("SELECT * FROM subjClass WHERE riken_id=$riken_id AND classid=$classid;"))>0){
			return true;
		}else{
			return false;
		}
	}
		static function getSubTeachers($riken_id,$userid){					
		if(mysql_num_rows(mysql_query("SELECT * FROM subjTeachers WHERE riken_id=$riken_id AND userid=$userid;"))>0){
			return true;
		}else{
			return false;
		}
	}
	static function getSubjClassList($riken_id,$classid){		
	$data = array();
		//$q = mysql_query("SELECT * FROM rikens ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, riken_id DESC");
		$q = mysql_query("SELECT * FROM classgrp WHERE riken_id=$riken_id;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;			
		
	}

	static function getGroupList(){	
		$data = array();
		$q = mysql_query("SELECT * FROM groups");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	
	static function getRikenTeachers($riken_id){					
		$q = mysql_query("SELECT t.userid,t.fname,t.lname FROM teacher t INNER JOIN subjTeachers st ON t.userid=st.userid WHERE riken_id=$riken_id;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;	
	}
	
	static function getRikenClass($riken_id){		
	$data = array();
		//$q = mysql_query("SELECT * FROM rikens ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, riken_id DESC");
		$q = mysql_query("SELECT * FROM classgrp INNER JOIN subjClass ON classgrp.classid=subjClass.classid WHERE riken_id=$riken_id;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;					
	}
	
	static function getResClassList($riken_id){		
	$data = array();
		//$q = mysql_query("SELECT * FROM rikens ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, riken_id DESC");
		$q = mysql_query("SELECT * FROM classgrp INNER JOIN subjClass ON classgrp.classid=subjClass.classid AND subjClass.riken_id=$riken_id;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;			
	}
	
	static function getStuSubjInfo($cid){		
	$data = array();
		//$q = mysql_query("SELECT * FROM rikens ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, riken_id DESC");
		$q = mysql_query("SELECT s.subjname, t.fname, t.lname FROM riken s INNER JOIN classTeachers ct ON s.riken_id=ct.riken_id INNER JOIN teacher t ON t.userid=ct.userid WHERE ct.classid = $cid;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;			
	}
	
	static function viewClassResults($data){		
	$d = array();
	$data['assessID'] = mysql_real_escape_string($data['aname']);
	$data['cid'] = mysql_real_escape_string($data['classid']);
	
		//$q = mysql_query("SELECT * FROM rikens ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, riken_id DESC");
		$q = mysql_query("SELECT s.classid, s.studid, s.fname, s.lname, cg.level, cg.classname, sc.riken_id, a.assessID, a.totalmarks, sm.marks FROM student s INNER JOIN subjClass sc ON s.classid = sc.classid INNER JOIN riken sub ON sc.riken_id=sub.riken_id INNER JOIN assessment a ON a.riken_id = sc.riken_id INNER JOIN classgrp cg ON s.classid = cg.classid INNER JOIN studmarks sm ON sc.riken_id=sm.riken_id AND sm.studid=s.studid WHERE a.assessID='$data[assessID]' AND sc.classid = '$data[cid]' AND sc.riken_id='$data[riken_id]';");
		while ($r = mysql_fetch_assoc($q)) {
			$d[] = $r;
		}
		return $d;			
	}
	
	static function viewClassResultsEntry($data){		
	$d = array();
	$data['assessID'] = mysql_real_escape_string($data['aname']);
	$data['cid'] = mysql_real_escape_string($data['className']);
	$data['riken_id'] = mysql_real_escape_string($data['riken_id']);
	
		//$q = mysql_query("SELECT * FROM rikens ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, riken_id DESC");
		$q = mysql_query("SELECT s.classid, s.studid, s.fname, s.lname, cg.level, cg.classname, sc.riken_id, a.assessID, a.totalmarks, sm.marks FROM student s INNER JOIN subjClass sc ON s.classid = sc.classid INNER JOIN riken sub ON sc.riken_id=sub.riken_id INNER JOIN assessment a ON a.riken_id = sc.riken_id INNER JOIN classgrp cg ON s.classid = cg.classid LEFT OUTER JOIN studmarks sm ON sc.riken_id=sm.riken_id AND sm.studid=s.studid WHERE a.assessID='$data[assessID]' AND sc.classid = '$data[cid]' AND sc.riken_id='$data[riken_id]';");
		while ($r = mysql_fetch_assoc($q)) {
			$d[] = $r;
		}
		return $d;			
	}
	
	static function addClassResults($data){		
	$d = array();	
	$count = sizeof($data['marks']);	
	
	$st = "DELETE FROM studmarks WHERE classid=".$data['classid'].";";
	mysql_query($st);
	$s1 = "INSERT INTO studmarks (studid, classid, riken_id, assessID, marks) VALUES ";		
		for ($i=0;$i<$count-1;$i++){			
			$s1 .="('".$data['studid'][$i]."','".$data['classid']."','".$data['riken_id']."','".$data['assessID']."','".$data['marks'][$i]."'),"; 
				
		}
		$s1 .="('".$data['studid'][$i]."','".$data['classid']."','".$data['riken_id']."','".$data['assessID']."','".$data['marks'][$i]."');"; 
		mysql_query($s1);
	//	mysql_query("INSERT INTO subjClass (riken_id, classid) VALUES ('1','2');");				
	/*while ($r = mysql_fetch_assoc($q)) {
			$d[] = $r;
		}
		return $d;	*/
		return $data;
		//$q = mysql_query("SELECT * FROM rikens ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, riken_id DESC");
					
	}
	
	static function assignClassSub($data, $riken_id) {
				
		$st = mysql_query("DELETE FROM subjClass WHERE riken_id=$riken_id;");
		mysql_query($st);
		$count = sizeof($data['classyid']);
		$s1 = "INSERT INTO subjClass (riken_id, classid) VALUES ";		
		for ($i=0;$i<$count-1;$i++){			
			$s1 .="('".$riken_id."','".$data['classyid'][$i]."'),"; 				
		}
		$s1 .="('".$riken_id."','".$data['classyid'][$count-1]."');"; 
		mysql_query($s1);
	//	mysql_query("INSERT INTO subjClass (riken_id, classid) VALUES ('1','2');");				
		//return $data;
	}

	static function assignSubTeacher($data, $riken_id) {
		
	$st = mysql_query("DELETE FROM subjTeachers WHERE riken_id=$riken_id;");
		mysql_query($st);
		$count = sizeof($data['useid']);
		$s1 = "INSERT INTO subjTeachers (riken_id, userid) VALUES ";		
		for ($i=0;$i<$count-1;$i++){			
			$s1 .="('".$riken_id."','".$data['useid'][$i]."'),"; 
				
		}
		$s1 .="('".$riken_id."','".$data['useid'][$count-1]."');"; 
		mysql_query($s1);
	//	mysql_query("INSERT INTO subjClass (riken_id, classid) VALUES ('1','2');");				
		//return $data;
	}
	
		static function assignClassTeachers($data, $riken_id) {
		
	$st = mysql_query("DELETE FROM classTeachers WHERE riken_id=$riken_id;");
		mysql_query($st);
		$count = sizeof($data['classid']);
		$s1 = "INSERT INTO classTeachers (classid,userid,riken_id) VALUES ";		
		for ($i=0;$i<$count-1;$i++){			
			$s1 .="('".$data['classid'][$i]."','".$data['teacher'][$i]."','".$riken_id."'),"; 
				
		}
		$s1 .="('".$data['classid'][$count-1]."','".$data['teacher'][$count-1]."','".$riken_id."');"; 
		mysql_query($s1);
	//	mysql_query("INSERT INTO subjClass (riken_id, classid) VALUES ('1','2');");				
		//return $data;
	}
	
	/////////////////////////////////////////////////////////////////////////////
	static function assessmentExists ($assessID) {
		$assessID = intval($assessID);
		if ($assessID == null) return false;
		else return (mysql_num_rows(mysql_query("SELECT assessID FROM assessment WHERE assessID=$assessID;")) == 1);
	}
		static function getAssessment ($assessID) {
		$assessID = intval($assessID);
		return mysql_fetch_assoc(mysql_query("SELECT * FROM assessment WHERE assessID=$assessID"));
	}
	static function getAssessmentList ($riken_id) {
		$data = array();
		//$q = mysql_query("SELECT * FROM rikens ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, riken_id DESC");
		$q = mysql_query("SELECT * from assessment WHERE riken_id=$riken_id");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	
	static function validateassessment ($data) {
		$fieldsToCheck = array('aname', 'weightage', 'totalmarks','description');
		foreach ($fieldsToCheck as $k => $field) {
			if (!isset($data[$field]) || $data[$field] == "") {
				errorMessage("Field '$field' cannot be left blank!");
				return false;
			}
		}
		return true;
	}
	
		static function addassessment ($data) {
		$data['riken_id'] = mysql_real_escape_string($data['riken_id']);
		$data['weightage'] = mysql_real_escape_string($data['weightage']);
		$data['aname'] = mysql_real_escape_string($data['aname']);
		$data['marks'] = mysql_real_escape_string($data['marks']);
		$data['totalmarks'] = mysql_real_escape_string($data['totalmarks']);
		$data['description'] = mysql_real_escape_string($data['description']);
		
		mysql_query("INSERT INTO assessment (riken_id, weightage, aname, totalmarks, description) VALUES ('$data[riken_id]', '$data[weightage]','$data[aname]','$data[totalmarks]', '$data[description]')");
		
		}
	static function editassessment ($data, $riken_id) {
		
		$data['riken_id'] = mysql_real_escape_string($data['riken_id']);
		$data['weightage'] = mysql_real_escape_string($data['weightage']);
		$data['aname'] = mysql_real_escape_string($data['aname']);
		$data['marks'] = mysql_real_escape_string($data['marks']);
		$data['totalmarks'] = mysql_real_escape_string($data['totalmarks']);
		$data['description'] = mysql_real_escape_string($data['description']);
		
		mysql_query("UPDATE assessment SET riken_id='$data[riken_id]',  weightage='$data[weightage]', aname='$data[aname]',totalmarks='$data[totalmarks]', description='$data[description]' WHERE aid=$data[aid];");
		
	}
	static function deleteassessment ($aid) {
		$aid = intval($aid);
		mysql_query("DELETE FROM assessment WHERE assessID=$aid");
	/*	mysql_query("DELETE FROM attendance WHERE lessonID IN (SELECT lessonID FROM lessons WHERE riken_id=$riken_id)");
		mysql_query("DELETE FROM lessons WHERE riken_id=$riken_id");
		mysql_query("DELETE FROM signups WHERE riken_id=$riken_id");
		mysql_query("DELETE FROM facilitators WHERE riken_id=$riken_id");*/
	}
	
	
	function getAttending ($userID) {
		$userID = intval($userID);
		$q = mysql_query("SELECT rikenss.* FROM signups INNER JOIN rikenss ON signups.riken_id = rikenss.riken_id WHERE signups.userID = $userID AND rikenss.status='Ongoing' ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, rikenss.riken_id DESC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	function getRegistered ($userID) {
		$userID = intval($userID);
		$q = mysql_query("SELECT rikenss.* FROM signups INNER JOIN rikenss ON signups.riken_id = rikenss.riken_id WHERE signups.userID = $userID AND rikenss.status='Upcoming' ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, rikenss.riken_id DESC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	function getCompleted ($userID) {
		$userID = intval($userID);
		$q = mysql_query("SELECT rikenss.* FROM signups INNER JOIN rikenss ON signups.riken_id = rikenss.riken_id WHERE signups.userID = $userID AND rikenss.status='Over' ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, rikenss.riken_id DESC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	function getFacilitating ($userID) {
		$userID = intval($userID);
		$q = mysql_query("SELECT rikenss.* FROM facilitators INNER JOIN rikenss ON facilitators.riken_id = rikenss.riken_id WHERE facilitators.userID = $userID ORDER BY (CASE status WHEN 'Upcoming' THEN 2 WHEN 'Ongoing' THEN 1 WHEN 'Over' THEN 3 END) ASC, signup DESC, rikenss.riken_id DESC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	
	
	function getLesson ($lessonID) {
		$lessonID = intval($lessonID);
		return mysql_fetch_assoc(mysql_query("SELECT * FROM lessons WHERE lessonID=$lessonID"));
	}
	function getLessonList ($riken_id) {
		$riken_id = intval($riken_id);
		$q = mysql_query("SELECT * FROM lessons WHERE lessons.riken_id=$riken_id  ORDER BY time DESC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	function getLessonID ($riken_id) {
		$riken_id = intval($riken_id);
		$q = mysql_query("SELECT lessons.lessonID FROM lessons WHERE lessons.riken_id=$riken_id  ORDER BY time DESC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = intval($r['lessonID']);
		}
		return $data;
	}
	function countLessons ($riken_id) {
		$riken_id = intval($riken_id);
		$q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM lessons WHERE riken_id=$riken_id;"));
		return intval($q['num']);
	}
	function getAttendedUID ($lessonID) {
		$data = array();
		$lessonID = intval($lessonID);
		$q = mysql_query("SELECT userID FROM attendance WHERE lessonID=$lessonID");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = intval($r['userID']);
		}
		return $data;
	}
	function getUserAttendance ($riken_id, $userID) {
		$riken_id = intval($riken_id);
		$userID = intval($userID);
		$lessons = rikenss::getLessonID($riken_id);
		$weightage = array();
		$q = mysql_query("SELECT lessons.weightage, lessonID FROM lessons WHERE riken_id=$riken_id");
		$total = 0;
		$userVal = 0;
		while ($r = mysql_fetch_assoc($q)) {
			$weightage[intval($r['lessonID'])] = floatval($r['weightage']);
			$total += floatval($r['weightage']);
		}
		
		$q = mysql_query("SELECT lessonID FROM attendance WHERE lessonID IN (SELECT lessonID FROM lessons WHERE riken_id=$riken_id) AND userID=$userID");
		while ($r = mysql_fetch_assoc($q)) {
			$lessonID = intval($r['lessonID']);
			$userVal += floatval($weightage[$lessonID]);
		}
		return floatval($userVal*100/$total);
	}
	function getAttendanceMatrix ($riken_id) {
		$riken_id = intval($riken_id);
		$people = rikenss::getSignupUID($riken_id);
		$lessons = rikenss::getLessonID($riken_id);
		$weightage = array();
		$q = mysql_query("SELECT lessons.weightage, lessonID FROM lessons WHERE riken_id=$riken_id");
		$total = 0;
		while ($r = mysql_fetch_assoc($q)) {
			$weightage[intval($r['lessonID'])] = floatval($r['weightage']);
			$total += floatval($r['weightage']);
		}
		$data = array();
		$data['sum'] = array();
		$data['percentage'] = array();
		$data['sum']['total'] = $total;
		$pre = array();
		foreach ($people as $k => $v) {
			$pre[intval($v)] = 0;
		}
		$pre['total'] = 0;
		foreach ($lessons as $k => $v) {
			$data[$v] = $pre;
		}
		$q = mysql_query("SELECT userID, lessonID FROM attendance WHERE lessonID IN (SELECT lessonID FROM lessons WHERE riken_id=$riken_id)");
		while ($r = mysql_fetch_assoc($q)) {
			$lessonID = intval($r['lessonID']);
			$userID = intval($r['userID']);
			$data[$lessonID][$userID] = $weightage[$lessonID];
			$data[$lessonID]['total']++;
			$data['sum'][$userID] += $weightage[$lessonID];
		}
		$data['percentage']['average'] = 0.0;
		foreach ($people as $k => $v) {
			$data['percentage'][$v] = floatval($data['sum'][$v]*100/$total);
			$data['percentage']['average']+=floatval($data['percentage'][$v]);
		}
		$data['percentage']['average'] /= count($people);
		return $data;
	}
	
	function getFacilitatorList ($riken_id) {
		$riken_id = intval($riken_id);
		$data = array();
		$q = mysql_query("SELECT facilitators.role, users.* FROM facilitators INNER JOIN users ON users.userID = facilitators.userID WHERE facilitators.riken_id=$riken_id;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	function getFacilitatorUID ($riken_id) {
		$riken_id = intval($riken_id);
		$data = array();
		$q = mysql_query("SELECT facilitators.userID FROM facilitators WHERE facilitators.riken_id=$riken_id;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = intval($r['userID']);
		}
		return $data;
	}
	function countFacilitators ($riken_id) {
		$riken_id = intval($riken_id);
		$q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM facilitators WHERE riken_id=$riken_id;"));
		return intval($q['num']);
	}
	function isFacilitator ($riken_id, $userID) {
		$riken_id = intval($riken_id);
		$userID = intval($userID);
		return mysql_num_rows(mysql_query("SELECT facilID FROM facilitators WHERE riken_id=$riken_id AND userID=$userID;"));
	}
	
	
	/*function getAttendeeList ($riken_id) {
		$riken_id = intval($riken_id);
		$q = mysql_query("SELECT users.*, signups.time, signups.notes FROM signups INNER JOIN users ON signups.userID = users.userID WHERE signups.riken_id = $riken_id ORDER BY name ASC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}*/
	function getSignupUID ($riken_id) {
		$riken_id = intval($riken_id);
		$q = mysql_query("SELECT users.*, signups.time, signups.notes FROM signups INNER JOIN users ON signups.userID = users.userID WHERE signups.riken_id = $riken_id ORDER BY name ASC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = intval($r['userID']);
		}
		return $data;
	}
	function getSignupList ($riken_id) {
		$riken_id = intval($riken_id);
		$data = array();
		$q = mysql_query("SELECT users.*, signups.time, signups.notes, batches.endyear, batches.level FROM signups INNER JOIN users ON signups.userID = users.userID INNER JOIN batches ON batches.startyear = users.startyear WHERE signups.riken_id = $riken_id ORDER BY name ASC");
		while ($r = mysql_fetch_assoc($q)) {
			$r['batch'] = "$r[startyear] - $r[endyear] ($r[level])";
			$data[] = $r;
		}

		return $data;
	}
	function getSignup ($riken_id, $userID) {
		$riken_id = intval($riken_id);
		$userID = intval($userID);
		return mysql_fetch_assoc(mysql_query("SELECT * FROM signups WHERE riken_id=$riken_id AND userID=$userID"));
	}
	function countSignups ($riken_id) {
		$riken_id = intval($riken_id);
		$q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM signups WHERE riken_id=$riken_id;"));
		return intval($q['num']);
	}
	function addSignup ($riken_id, $userID) {
		$riken_id = intval($riken_id);
		$userID = intval($userID);
		if (!rikenss::getSignup($riken_id, $userID))
		mysql_query("INSERT INTO signups (userID, riken_id) VALUES ($userID, $riken_id)");
	}
	function editSignup($data, $signupID) {
		$signupID = intval($signupID);
		$data['notes'] = mysql_real_escape_string($data['notes']);
		mysql_query("UPDATE signups SET notes='$data[notes]' WHERE signupID=$signupID");
	}
	function deleteSignup($signupID) {
		$signupID = intval($signupID);
		mysql_query("DELETE FROM signups WHERE signupID=$signupID");
	}
	
	
	
	
	/* Add, Edit and Delete Lessons */
	function addLesson ($data, $riken_id) {
		$riken_id = intval($riken_id);
		$data['weightage'] = floatval($data['weightage']);
		mysql_query("INSERT INTO lessons (riken_id, weightage) VALUES ($riken_id, $data[weightage])");
		$lessonID = mysql_insert_id();
		$attendees = rikenss::getSignupUID($riken_id);
		$chain = array();
		foreach ($data['attendees'] as $k => $v) {
			$v = intval($v);
			if (!in_array(intval($v), $attendees)) continue;
			$chain[] = "($lessonID, $v)";
		}
		if (count($chain) > 0) 
		mysql_query("INSERT INTO attendance (lessonID, userID) VALUES ".implode(", ", $chain).";");
		return $lessonID;
	}
	function editLesson ($data, $lessonID) {
		$lessonID = intval($lessonID);
		$data['weightage'] = floatval($data['weightage']);
		mysql_query("UPDATE lessons SET weightage=$data[weightage] WHERE lessonID=$lessonID");
		$lesson = mysql_fetch_array(mysql_query("SELECT * FROM lessons WHERE lessonID=$lessonID"));
		$attendees = rikenss::getSignupUID($lesson['riken_id']);
		$current = rikenss::getAttendedUID($lessonID);
		$toInsert = array();
		$toDelete = array();
		foreach ($data['attendees'] as $k => $v) {
			if (!in_array(intval($v), $current)) $toInsert[] = intval($v);
			$data['attendees'][$k] = intval($data['attendees'][$k]);
		}
		foreach ($current as $k => $v) {
			if (!in_array(intval($v), $data['attendees'])) $toDelete[] = intval($v);
		}
		if (count($toDelete) > 0) mysql_query("DELETE FROM attendance WHERE lessonID=$lessonID AND userID IN (".implode(", ", $toDelete).");");
		$chain = array();
		foreach ($toInsert as $k => $v) {
			$v = intval($v);
			if (!in_array(intval($v), $attendees)) continue;
			$chain[] = "($lessonID, $v)";
		}
		if (count($chain) > 0) 
		mysql_query("INSERT INTO attendance (lessonID, userID) VALUES ".implode(", ", $chain).";");
	}
	function deleteLesson ($lessonID) {
		$lessonID = intval($lessonID);
		mysql_query("DELETE FROM attendance WHERE lessonID=$lessonID");
		mysql_query("DELETE FROM lessons WHERE lessonID=$lessonID");
	}
	function validateLink ($data) {
		$fieldsToCheck = array('name', 'url', 'ranking');
		foreach ($fieldsToCheck as $k => $field) {
			if (!isset($data[$field]) || $data[$field] == "") {
				errorMessage("Field '$field' cannot be left blank!");
				return false;
			}
		}
		return true;
	}
	function addLink ($data, $riken_id) {
		$data['name'] = mysql_real_escape_string($data['name']);
		$data['url'] = mysql_real_escape_string($data['url']);
		$data['ranking'] = intval($data['ranking']);
		$riken_id = intval($riken_id);
		mysql_query("INSERT INTO rikensLinks (riken_id, name, url, ranking) VALUES ($riken_id, '$data[name]', '$data[url]', $data[ranking])");
		return mysql_insert_id();
	}
	function editLink($data, $linkID) {
		$data['name'] = mysql_real_escape_string($data['name']);
		$data['url'] = mysql_real_escape_string($data['url']);
		$data['ranking'] = intval($data['ranking']);
		$linkID = intval($linkID);
		mysql_query("UPDATE rikensLinks SET name='$data[name]', url='$data[url]', ranking=$data[ranking] WHERE linkID=$linkID;");
		return;
	}
	function deleteLink ($linkID) {
		mysql_query("DELETE FROM rikensLinks WHERE linkID=$linkID;");
		return;
	}
	function getLinkList ($riken_id) {
		$riken_id = intval($riken_id);
		$q = mysql_query("SELECT * FROM rikensLinks WHERE riken_id=$riken_id ORDER BY ranking DESC, time ASC");
		
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	function countrikensLink ($riken_id) {
		$riken_id = intval($riken_id);
		$q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) AS num FROM rikensLinks WHERE riken_id=$riken_id;"));
		return intval($q['num']);
	}
	function getLink ($linkID) {
		$linkID = intval($linkID);
		return mysql_fetch_assoc(mysql_query("SELECT * FROM rikensLinks WHERE linkID=$linkID;"));
	}
	function linkExists($riken_id, $linkID) {
		$riken_id = intval($riken_id);
		$linkID = intval($linkID);
		return (mysql_num_rows(mysql_query("SELECT linkID FROM rikensLinks WHERE linkID=$linkID and riken_id=$riken_id;")) == 1);
	}
	
	/* Generates the generic nav bar for rikens related actions */
	static function navBar ($userID) {
		$userID = intval($userID);
		$userData = Users::getUser($userID);
		if(strcmp($userData['role'],'admin')){
		$adminView =  1;
		}
	//View change according to user
		$output = "<div class='sidenav'><div class='list-group'>";
		$output .= "<div class='list-group-item nav-header'>Manage Rikens</div>";
		$output .= "<a href='viewRikenList.php' class='list-group-item'><i class='icon-plus'></i>View All Rikens</a>";
		$output .= "<a href='addRiken.php' class='list-group-item'><i class='icon-plus'></i>Add New Rikens</a>";		
		$output .= "</div></div>";
		return $output;
	}
	
	/* Generates the rikens-specific nav bar for rikens-specific actions*/
	static function rikensNavBar ($riken_id, $userID) {
		$userID = intval($userID);
		$userData = Users::getUser($userID);
		$riken_id = intval($riken_id);	
		$data = rikens::getRikens($riken_id);
		if($userData['role'] == 1){
		$adminView =  1;
		}
		
		$output = "<div class='sidenav'><div class='list-group'>";

		if ($adminView) $output .= "<div class='list-group-item nav-header'>".htmlentities($data['title'])."</div>";		
		if ($adminView) $output .= "<a class='list-group-item' href='editRiken.php?riken_id=$riken_id'><i class='icon-edit'></i>Edit/Delete rikens</a>";
		if ($adminView) $output .= "<a class='list-group-item' href='rikenStudents.php?riken_id=$riken_id'><i class='icon-edit'></i>View Students</a>";
		$output .= "</div></div>";
		return $output;
	}
}
?>
