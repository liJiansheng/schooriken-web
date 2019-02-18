<?php
require_once("connect.php");
require_once('class.upload.php');
require_once("users.php");
class Events {

	static function EventsExists ($event_id) {
		$event_id = intval($event_id);
		if ($event_id == null) return false;
		else return (mysql_num_rows(mysql_query("SELECT event_id FROM events WHERE event_id=$event_id;")) == 1);
	}

	static function getEventPhoto ($event_id) {
		$event_id = intval($event_id);
		if(mysql_num_rows(mysql_query("SELECT image FROM eventextras WHERE event_id=$event_id;"))>0){
			return true;
		}else{
			return false;
		}
	}	
	static function getEvents ($event_id, $isImg) {
		$event_id = intval($event_id);
		if($isImg){
		return mysql_fetch_assoc(mysql_query("SELECT e.event_id, e.type, e.title, e.description, e.evtDate, e.userID, e.group_id, e.schoolID, ex.image FROM events e INNER JOIN eventextras ex ON e.event_id=ex.event_id WHERE e.event_id=$event_id"));
		}else{
			return mysql_fetch_assoc(mysql_query("SELECT * FROM events WHERE event_id=$event_id;"));
		}
	}

	static function getAssessList ($user_id, $schoolID) {
		$data = array();
		//$q = mysql_query("SELECT * FROM Events ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, event_id DESC");
		$q = mysql_query("SELECT e.event_id,e.title, e.description,e.evtDate,g.group_name from events e INNER JOIN groups g ON e.group_id=g.group_id WHERE e.userID=$user_id AND e.schoolID=$schoolID AND e.type='Assignment';");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	
	static function getAnnounceList ($user_id, $schoolID) {
		$data = array();
		//$q = mysql_query("SELECT * FROM Events ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, event_id DESC");
		$q = mysql_query("SELECT e.event_id,e.title, e.description,e.evtDate,g.group_name from events e INNER JOIN groups g ON e.group_id=g.group_id WHERE e.userID=$user_id AND e.schoolID=$schoolID AND e.type='Announcement';");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}

	static function getEventList ($user_id, $schoolID) {
		$data = array();
		//$q = mysql_query("SELECT * FROM Events ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, event_id DESC");
		$q = mysql_query("SELECT e.event_id,e.title, e.description,e.evtDate,g.group_name from events e INNER JOIN groups g ON e.group_id=g.group_id WHERE e.userID=$user_id AND e.schoolID=$schoolID AND e.type='Event';");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}

	static function countEvents ($userID = -1) {
		$userID = intval($userID);
		$q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM event;"));
		/*if ($userID == -1) $q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM event;"));
		else $q = mysql_fetch_assoc(mysql_query("SELECT COUNT(pre.event_id) as num FROM (SELECT event_id FROM signups WHERE userID=$userID UNION SELECT event_id FROM facilitators WHERE userID=$userID) as pre"));*/
		return intval($q['num']);
	}
	
	static function validateEvents ($data) {
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
					//$picLocation = "event_imgs/$itemID/".$file['picture']['name'];
					//echo $itemID."<br><img src='$picLocation'><br>";
				}	
			}
				return true;	
	}

	static function addEvents ($edata) {

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
		mysql_query("INSERT INTO events (title, type, evtDate, userID,group_id,schoolID,description) VALUES ('$edata[title]','$edata[type]','$edata[evtDate]','$edata[userID]','$edata[group_id]', '$edata[schoolID]', '$edata[description]')");		
		
		$event_id = mysql_insert_id();
		$count=0;
        $gid = $edata['group_id'];
        $res = mysql_query("SELECT * FROM studentgroups WHERE group_id=$gid");
		//mysql_query("INSERT INTO studentevents (event_id,student_id,group_id,flag) VALUES ($eid,$sid,$gid, 1)");
        while ($row=mysql_fetch_assoc($res)) {
            $sid = $row['student_id'];
            mysql_query("INSERT INTO studentevents (event_id,student_id,group_id,flag,isShow) VALUES ($event_id,$sid,$gid,0,1)");			
			$count++;
        }
					
		return $event_id;               
	}

	static function editEvents ($data, $event_id) {
		$event_id = intval($event_id);
		$edate = new DateTime($data['evtDate']); // format: MM/DD/YYYY
		$data['title'] = mysql_real_escape_string($data['title']);		
		$data['evtDate'] = $edate->format('U');
		$data['type'] = mysql_real_escape_string($data['type']);		
		//$data['posted'] = $data['post_day']."-".$data['post_month']."-".$data['post_year'];
		$data['description'] = mysql_real_escape_string($data['description']);
		$data['userID'] = mysql_real_escape_string($data['userID']);
		$data['group_id'] = mysql_real_escape_string($data['group_id']);
		$data['schoolID'] = mysql_real_escape_string($data['schoolID']);
		
		mysql_query("UPDATE events SET title='$data[title]',  type='$data[type]', evtDate='$data[evtDate]', userID='$data[userID]', group_id='$data[group_id]', schoolID='$data[schoolID]', description='$data[description]' WHERE event_id=$event_id;");
				          
	}
	static function deleteEvents ($event_id) {
		$event_id = intval($event_id);
		mysql_query("DELETE FROM events WHERE event_id=$event_id");
		mysql_query("DELETE FROM eventextras WHERE event_id=$event_id");
		mysql_query("DELETE FROM studentevents WHERE event_id=$event_id");
		
	/*	mysql_query("DELETE FROM attendance WHERE lessonID IN (SELECT lessonID FROM lessons WHERE event_id=$event_id)");
		mysql_query("DELETE FROM lessons WHERE event_id=$event_id");
		mysql_query("DELETE FROM signups WHERE event_id=$event_id");
		mysql_query("DELETE FROM facilitators WHERE event_id=$event_id");*/
	}
	
static function attachPhoto ($iid, $uploadedfile) {
	$PIC_FOLDER = "event_img/";
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
            mysql_query("INSERT INTO eventextras (event_id, image) VALUES ($iid, '$picName');");
             return 1;
            } else {
              return 0;
            }
	
}

//TODO: Add some verification code
static function attachPhotoUrl ($iid, $url) {
    mysql_query("INSERT INTO eventextras (event_id, image) VALUES ($iid,'$url');");
    return 1;
}
static function editPhotoUrl ($iid, $url) {
   mysql_query("UPDATE eventextras SET image = '$url' WHERE event_id='$iid';");    
    return 1;
}


static function deletePhoto ($iid){
	$PIC_FOLDER = "files/";
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
 }
  mysql_query("DELETE FROM eventextras WHERE event_id='$iid';");	

}
static function editPhoto ($iid, $uploadedfile) {
	$PIC_FOLDER = "files/";
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
            mysql_query("UPDATE eventextras SET image = '$picName' WHERE event_id='$iid';");
          
             return 1;
            } else {
              return 0;
            }
	
}

	static function getSubjClass($event_id,$classid){					
		if(mysql_num_rows(mysql_query("SELECT * FROM subjClass WHERE event_id=$event_id AND classid=$classid;"))>0){
			return true;
		}else{
			return false;
		}
	}
		static function getSubTeachers($event_id,$userid){					
		if(mysql_num_rows(mysql_query("SELECT * FROM subjTeachers WHERE event_id=$event_id AND userid=$userid;"))>0){
			return true;
		}else{
			return false;
		}
	}
	static function getSubjClassList($event_id,$classid){		
	$data = array();
		//$q = mysql_query("SELECT * FROM Events ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, event_id DESC");
		$q = mysql_query("SELECT * FROM classgrp WHERE event_id=$event_id;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;			
		
	}

	static function getGroupList($schoolID){	
		$data = array();
		$q = mysql_query("SELECT * FROM groups WHERE schoolID=$schoolID");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	
	static function geteventTeachers($event_id){					
		$q = mysql_query("SELECT t.userid,t.fname,t.lname FROM teacher t INNER JOIN subjTeachers st ON t.userid=st.userid WHERE event_id=$event_id;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;	
	}
	
	static function geteventClass($event_id){		
	$data = array();
		//$q = mysql_query("SELECT * FROM Events ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, event_id DESC");
		$q = mysql_query("SELECT * FROM classgrp INNER JOIN subjClass ON classgrp.classid=subjClass.classid WHERE event_id=$event_id;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;					
	}
	
	static function getResClassList($event_id){		
	$data = array();
		//$q = mysql_query("SELECT * FROM Events ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, event_id DESC");
		$q = mysql_query("SELECT * FROM classgrp INNER JOIN subjClass ON classgrp.classid=subjClass.classid AND subjClass.event_id=$event_id;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;			
	}
	
	static function getStuSubjInfo($cid){		
	$data = array();
		//$q = mysql_query("SELECT * FROM Events ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, event_id DESC");
		$q = mysql_query("SELECT s.subjname, t.fname, t.lname FROM event s INNER JOIN classTeachers ct ON s.event_id=ct.event_id INNER JOIN teacher t ON t.userid=ct.userid WHERE ct.classid = $cid;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;			
	}
	
	static function viewClassResults($data){		
	$d = array();
	$data['assessID'] = mysql_real_escape_string($data['aname']);
	$data['cid'] = mysql_real_escape_string($data['classid']);
	
		//$q = mysql_query("SELECT * FROM Events ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, event_id DESC");
		$q = mysql_query("SELECT s.classid, s.studid, s.fname, s.lname, cg.level, cg.classname, sc.event_id, a.assessID, a.totalmarks, sm.marks FROM student s INNER JOIN subjClass sc ON s.classid = sc.classid INNER JOIN event sub ON sc.event_id=sub.event_id INNER JOIN assessment a ON a.event_id = sc.event_id INNER JOIN classgrp cg ON s.classid = cg.classid INNER JOIN studmarks sm ON sc.event_id=sm.event_id AND sm.studid=s.studid WHERE a.assessID='$data[assessID]' AND sc.classid = '$data[cid]' AND sc.event_id='$data[event_id]';");
		while ($r = mysql_fetch_assoc($q)) {
			$d[] = $r;
		}
		return $d;			
	}
	
	static function viewClassResultsEntry($data){		
	$d = array();
	$data['assessID'] = mysql_real_escape_string($data['aname']);
	$data['cid'] = mysql_real_escape_string($data['className']);
	$data['event_id'] = mysql_real_escape_string($data['event_id']);
	
		//$q = mysql_query("SELECT * FROM Events ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, event_id DESC");
		$q = mysql_query("SELECT s.classid, s.studid, s.fname, s.lname, cg.level, cg.classname, sc.event_id, a.assessID, a.totalmarks, sm.marks FROM student s INNER JOIN subjClass sc ON s.classid = sc.classid INNER JOIN event sub ON sc.event_id=sub.event_id INNER JOIN assessment a ON a.event_id = sc.event_id INNER JOIN classgrp cg ON s.classid = cg.classid LEFT OUTER JOIN studmarks sm ON sc.event_id=sm.event_id AND sm.studid=s.studid WHERE a.assessID='$data[assessID]' AND sc.classid = '$data[cid]' AND sc.event_id='$data[event_id]';");
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
	$s1 = "INSERT INTO studmarks (studid, classid, event_id, assessID, marks) VALUES ";		
		for ($i=0;$i<$count-1;$i++){			
			$s1 .="('".$data['studid'][$i]."','".$data['classid']."','".$data['event_id']."','".$data['assessID']."','".$data['marks'][$i]."'),"; 
				
		}
		$s1 .="('".$data['studid'][$i]."','".$data['classid']."','".$data['event_id']."','".$data['assessID']."','".$data['marks'][$i]."');"; 
		mysql_query($s1);
	//	mysql_query("INSERT INTO subjClass (event_id, classid) VALUES ('1','2');");				
	/*while ($r = mysql_fetch_assoc($q)) {
			$d[] = $r;
		}
		return $d;	*/
		return $data;
		//$q = mysql_query("SELECT * FROM Events ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, event_id DESC");
					
	}
	
	static function assignClassSub($data, $event_id) {
				
		$st = mysql_query("DELETE FROM subjClass WHERE event_id=$event_id;");
		mysql_query($st);
		$count = sizeof($data['classyid']);
		$s1 = "INSERT INTO subjClass (event_id, classid) VALUES ";		
		for ($i=0;$i<$count-1;$i++){			
			$s1 .="('".$event_id."','".$data['classyid'][$i]."'),"; 				
		}
		$s1 .="('".$event_id."','".$data['classyid'][$count-1]."');"; 
		mysql_query($s1);
	//	mysql_query("INSERT INTO subjClass (event_id, classid) VALUES ('1','2');");				
		//return $data;
	}

	static function assignSubTeacher($data, $event_id) {
		
	$st = mysql_query("DELETE FROM subjTeachers WHERE event_id=$event_id;");
		mysql_query($st);
		$count = sizeof($data['useid']);
		$s1 = "INSERT INTO subjTeachers (event_id, userid) VALUES ";		
		for ($i=0;$i<$count-1;$i++){			
			$s1 .="('".$event_id."','".$data['useid'][$i]."'),"; 
				
		}
		$s1 .="('".$event_id."','".$data['useid'][$count-1]."');"; 
		mysql_query($s1);
	//	mysql_query("INSERT INTO subjClass (event_id, classid) VALUES ('1','2');");				
		//return $data;
	}
	
		static function assignClassTeachers($data, $event_id) {
		
	$st = mysql_query("DELETE FROM classTeachers WHERE event_id=$event_id;");
		mysql_query($st);
		$count = sizeof($data['classid']);
		$s1 = "INSERT INTO classTeachers (classid,userid,event_id) VALUES ";		
		for ($i=0;$i<$count-1;$i++){			
			$s1 .="('".$data['classid'][$i]."','".$data['teacher'][$i]."','".$event_id."'),"; 
				
		}
		$s1 .="('".$data['classid'][$count-1]."','".$data['teacher'][$count-1]."','".$event_id."');"; 
		mysql_query($s1);
	//	mysql_query("INSERT INTO subjClass (event_id, classid) VALUES ('1','2');");				
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
	static function getAssessmentList ($event_id) {
		$data = array();
		//$q = mysql_query("SELECT * FROM Events ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, event_id DESC");
		$q = mysql_query("SELECT * from assessment WHERE event_id=$event_id");
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
		$data['event_id'] = mysql_real_escape_string($data['event_id']);
		$data['weightage'] = mysql_real_escape_string($data['weightage']);
		$data['aname'] = mysql_real_escape_string($data['aname']);
		$data['marks'] = mysql_real_escape_string($data['marks']);
		$data['totalmarks'] = mysql_real_escape_string($data['totalmarks']);
		$data['description'] = mysql_real_escape_string($data['description']);
		
		mysql_query("INSERT INTO assessment (event_id, weightage, aname, totalmarks, description) VALUES ('$data[event_id]', '$data[weightage]','$data[aname]','$data[totalmarks]', '$data[description]')");
		
		}
	static function editassessment ($data, $event_id) {
		
		$data['event_id'] = mysql_real_escape_string($data['event_id']);
		$data['weightage'] = mysql_real_escape_string($data['weightage']);
		$data['aname'] = mysql_real_escape_string($data['aname']);
		$data['marks'] = mysql_real_escape_string($data['marks']);
		$data['totalmarks'] = mysql_real_escape_string($data['totalmarks']);
		$data['description'] = mysql_real_escape_string($data['description']);
		
		mysql_query("UPDATE assessment SET event_id='$data[event_id]',  weightage='$data[weightage]', aname='$data[aname]',totalmarks='$data[totalmarks]', description='$data[description]' WHERE aid=$data[aid];");
		
	}
	static function deleteassessment ($aid) {
		$aid = intval($aid);
		mysql_query("DELETE FROM assessment WHERE assessID=$aid");
	/*	mysql_query("DELETE FROM attendance WHERE lessonID IN (SELECT lessonID FROM lessons WHERE event_id=$event_id)");
		mysql_query("DELETE FROM lessons WHERE event_id=$event_id");
		mysql_query("DELETE FROM signups WHERE event_id=$event_id");
		mysql_query("DELETE FROM facilitators WHERE event_id=$event_id");*/
	}
	
	
	function getAttending ($userID) {
		$userID = intval($userID);
		$q = mysql_query("SELECT Eventss.* FROM signups INNER JOIN Eventss ON signups.event_id = Eventss.event_id WHERE signups.userID = $userID AND Eventss.status='Ongoing' ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, Eventss.event_id DESC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	function getRegistered ($userID) {
		$userID = intval($userID);
		$q = mysql_query("SELECT Eventss.* FROM signups INNER JOIN Eventss ON signups.event_id = Eventss.event_id WHERE signups.userID = $userID AND Eventss.status='Upcoming' ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, Eventss.event_id DESC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	function getCompleted ($userID) {
		$userID = intval($userID);
		$q = mysql_query("SELECT Eventss.* FROM signups INNER JOIN Eventss ON signups.event_id = Eventss.event_id WHERE signups.userID = $userID AND Eventss.status='Over' ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, Eventss.event_id DESC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	function getFacilitating ($userID) {
		$userID = intval($userID);
		$q = mysql_query("SELECT Eventss.* FROM facilitators INNER JOIN Eventss ON facilitators.event_id = Eventss.event_id WHERE facilitators.userID = $userID ORDER BY (CASE status WHEN 'Upcoming' THEN 2 WHEN 'Ongoing' THEN 1 WHEN 'Over' THEN 3 END) ASC, signup DESC, Eventss.event_id DESC");
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
	function getLessonList ($event_id) {
		$event_id = intval($event_id);
		$q = mysql_query("SELECT * FROM lessons WHERE lessons.event_id=$event_id  ORDER BY time DESC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	function getLessonID ($event_id) {
		$event_id = intval($event_id);
		$q = mysql_query("SELECT lessons.lessonID FROM lessons WHERE lessons.event_id=$event_id  ORDER BY time DESC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = intval($r['lessonID']);
		}
		return $data;
	}
	function countLessons ($event_id) {
		$event_id = intval($event_id);
		$q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM lessons WHERE event_id=$event_id;"));
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
	function getUserAttendance ($event_id, $userID) {
		$event_id = intval($event_id);
		$userID = intval($userID);
		$lessons = Eventss::getLessonID($event_id);
		$weightage = array();
		$q = mysql_query("SELECT lessons.weightage, lessonID FROM lessons WHERE event_id=$event_id");
		$total = 0;
		$userVal = 0;
		while ($r = mysql_fetch_assoc($q)) {
			$weightage[intval($r['lessonID'])] = floatval($r['weightage']);
			$total += floatval($r['weightage']);
		}
		
		$q = mysql_query("SELECT lessonID FROM attendance WHERE lessonID IN (SELECT lessonID FROM lessons WHERE event_id=$event_id) AND userID=$userID");
		while ($r = mysql_fetch_assoc($q)) {
			$lessonID = intval($r['lessonID']);
			$userVal += floatval($weightage[$lessonID]);
		}
		return floatval($userVal*100/$total);
	}
	function getAttendanceMatrix ($event_id) {
		$event_id = intval($event_id);
		$people = Eventss::getSignupUID($event_id);
		$lessons = Eventss::getLessonID($event_id);
		$weightage = array();
		$q = mysql_query("SELECT lessons.weightage, lessonID FROM lessons WHERE event_id=$event_id");
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
		$q = mysql_query("SELECT userID, lessonID FROM attendance WHERE lessonID IN (SELECT lessonID FROM lessons WHERE event_id=$event_id)");
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
	
	function getFacilitatorList ($event_id) {
		$event_id = intval($event_id);
		$data = array();
		$q = mysql_query("SELECT facilitators.role, users.* FROM facilitators INNER JOIN users ON users.userID = facilitators.userID WHERE facilitators.event_id=$event_id;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	function getFacilitatorUID ($event_id) {
		$event_id = intval($event_id);
		$data = array();
		$q = mysql_query("SELECT facilitators.userID FROM facilitators WHERE facilitators.event_id=$event_id;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = intval($r['userID']);
		}
		return $data;
	}
	function countFacilitators ($event_id) {
		$event_id = intval($event_id);
		$q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM facilitators WHERE event_id=$event_id;"));
		return intval($q['num']);
	}
	function isFacilitator ($event_id, $userID) {
		$event_id = intval($event_id);
		$userID = intval($userID);
		return mysql_num_rows(mysql_query("SELECT facilID FROM facilitators WHERE event_id=$event_id AND userID=$userID;"));
	}
	
	
	/*function getAttendeeList ($event_id) {
		$event_id = intval($event_id);
		$q = mysql_query("SELECT users.*, signups.time, signups.notes FROM signups INNER JOIN users ON signups.userID = users.userID WHERE signups.event_id = $event_id ORDER BY name ASC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}*/
	function getSignupUID ($event_id) {
		$event_id = intval($event_id);
		$q = mysql_query("SELECT users.*, signups.time, signups.notes FROM signups INNER JOIN users ON signups.userID = users.userID WHERE signups.event_id = $event_id ORDER BY name ASC");
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = intval($r['userID']);
		}
		return $data;
	}
	function getSignupList ($event_id) {
		$event_id = intval($event_id);
		$data = array();
		$q = mysql_query("SELECT users.*, signups.time, signups.notes, batches.endyear, batches.level FROM signups INNER JOIN users ON signups.userID = users.userID INNER JOIN batches ON batches.startyear = users.startyear WHERE signups.event_id = $event_id ORDER BY name ASC");
		while ($r = mysql_fetch_assoc($q)) {
			$r['batch'] = "$r[startyear] - $r[endyear] ($r[level])";
			$data[] = $r;
		}

		return $data;
	}
	function getSignup ($event_id, $userID) {
		$event_id = intval($event_id);
		$userID = intval($userID);
		return mysql_fetch_assoc(mysql_query("SELECT * FROM signups WHERE event_id=$event_id AND userID=$userID"));
	}
	function countSignups ($event_id) {
		$event_id = intval($event_id);
		$q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM signups WHERE event_id=$event_id;"));
		return intval($q['num']);
	}
	function addSignup ($event_id, $userID) {
		$event_id = intval($event_id);
		$userID = intval($userID);
		if (!Eventss::getSignup($event_id, $userID))
		mysql_query("INSERT INTO signups (userID, event_id) VALUES ($userID, $event_id)");
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
	function addLesson ($data, $event_id) {
		$event_id = intval($event_id);
		$data['weightage'] = floatval($data['weightage']);
		mysql_query("INSERT INTO lessons (event_id, weightage) VALUES ($event_id, $data[weightage])");
		$lessonID = mysql_insert_id();
		$attendees = Eventss::getSignupUID($event_id);
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
		$attendees = Eventss::getSignupUID($lesson['event_id']);
		$current = Eventss::getAttendedUID($lessonID);
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
	function addLink ($data, $event_id) {
		$data['name'] = mysql_real_escape_string($data['name']);
		$data['url'] = mysql_real_escape_string($data['url']);
		$data['ranking'] = intval($data['ranking']);
		$event_id = intval($event_id);
		mysql_query("INSERT INTO EventsLinks (event_id, name, url, ranking) VALUES ($event_id, '$data[name]', '$data[url]', $data[ranking])");
		return mysql_insert_id();
	}
	function editLink($data, $linkID) {
		$data['name'] = mysql_real_escape_string($data['name']);
		$data['url'] = mysql_real_escape_string($data['url']);
		$data['ranking'] = intval($data['ranking']);
		$linkID = intval($linkID);
		mysql_query("UPDATE EventsLinks SET name='$data[name]', url='$data[url]', ranking=$data[ranking] WHERE linkID=$linkID;");
		return;
	}
	function deleteLink ($linkID) {
		mysql_query("DELETE FROM EventsLinks WHERE linkID=$linkID;");
		return;
	}
	function getLinkList ($event_id) {
		$event_id = intval($event_id);
		$q = mysql_query("SELECT * FROM EventsLinks WHERE event_id=$event_id ORDER BY ranking DESC, time ASC");
		
		$data = array();
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	function countEventsLink ($event_id) {
		$event_id = intval($event_id);
		$q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) AS num FROM EventsLinks WHERE event_id=$event_id;"));
		return intval($q['num']);
	}
	function getLink ($linkID) {
		$linkID = intval($linkID);
		return mysql_fetch_assoc(mysql_query("SELECT * FROM EventsLinks WHERE linkID=$linkID;"));
	}
	function linkExists($event_id, $linkID) {
		$event_id = intval($event_id);
		$linkID = intval($linkID);
		return (mysql_num_rows(mysql_query("SELECT linkID FROM EventsLinks WHERE linkID=$linkID and event_id=$event_id;")) == 1);
	}
	
	/* Generates the generic nav bar for Events related actions */
	static function navBar ($userID, $type) {
		$userID = intval($userID);
		$userData = Users::getUser($userID);
		if($userData['role'] == 1){
		$adminView =  1;
		}
	//View change according to user
		switch($type){
			case "Event":
			$output = "<div class='sidenav'><div class='list-group'>";
		$output .= "<div class='list-group-item nav-header'>Manage Events</div>";
		$output .= "<a href='viewEventList.php' class='list-group-item'><i class='icon-list'></i> View All Events</a>";
		$output .= "<a href='addEvent.php' class='list-group-item'><i class='icon-plus'></i> Add New Events</a>";		
		$output .= "</div></div>";
			break;
			
			case "Assessment":
			$output = "<div class='sidenav'><div class='list-group'>";
		$output .= "<div class='list-group-item nav-header'>Manage Assessment</div>";
		$output .= "<a href='viewAssessList.php' class='list-group-item'><i class='icon-list'></i> View All Assessment</a>";
		$output .= "<a href='addAssess.php' class='list-group-item'><i class='icon-plus'></i> Add New Assessment</a>";		
		$output .= "</div></div>";	
			break;			
			case "Announcement":
			$output = "<div class='sidenav'><div class='list-group'>";
		$output .= "<div class='list-group-item nav-header'>Manage Announcement</div>";
		$output .= "<a href='viewAnnounceList.php' class='list-group-item'><i class='icon-list'></i> View All Announcement</a>";
		$output .= "<a href='addAnnounce.php' class='list-group-item'><i class='icon-plus'> </i> Add New Announcement</a>";		
		$output .= "</div></div>";
			break;
			
		}
		
	return $output;
	}
	
	/* Generates the Events-specific nav bar for Events-specific actions*/
	static function EventsNavBar ($event_id, $userID, $type) {
		$userID = intval($userID);
		$userData = Users::getUser($userID);
		$event_id = intval($event_id);
        $data = Events::getEvents($event_id,0);
		if($userData['role'] == 1){
		$adminView =  1;
		}
		
		$output = "<div class='sidenav'><div class='list-group'>";
		
		switch($type){
		case "Event":		
		if ($adminView) $output .= "<div class='list-group-item nav-header'>".htmlentities($data['title'])."</div>";		
		if ($adminView) $output .= "<a class='list-group-item' href='editEvent.php?event_id=$event_id'><i class='icon-edit'> </i>Edit/Delete Event</a>";
		//if ($adminView) $output .= "<a class='list-group-item' href='eventStudents.php?event_id=$event_id'><i class='icon-edit'></i>View Students</a>";
		//$output .= "</div></div>";
		break;
	case "Assessment":
		if ($adminView) $output .= "<div class='list-group-item nav-header'>".htmlentities($data['title'])."</div>";
		if ($adminView) $output .= "<a class='list-group-item' href='editAssess.php?assess_id=$event_id'><i class='icon-edit'></i> Edit/Delete Assessment</a>";
		//if ($adminView) $output .= "<a class='list-group-item' href='eventStudents.php?event_id=$event_id'><i class='icon-edit'></i>View Students</a>";
		break;
			case "Announcement":
		if ($adminView) $output .= "<div class='list-group-item nav-header'>".htmlentities($data['title'])."</div>";
		if ($adminView) $output .= "<a class='list-group-item' href='editAnnounce.php?announce_id=$event_id'><i class='icon-edit'></i> Edit/Delete Announcement</a>";
		//if ($adminView) $output .= "<a class='list-group-item' href='eventStudents.php?event_id=$event_id'><i class='icon-edit'></i>View Students</a>";
		break;
	}
        $output .= "</div></div>";
	return $output;
	}
}
?>
