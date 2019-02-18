<?php
require_once("connect.php");
require_once('class.upload.php');
require_once("users.php");
class Announcement {

	static function AnnounceExist ($announce_id) {
		$announce_id = intval($announce_id);
		if ($announce_id == null) return false;
		else return (mysql_num_rows(mysql_query("SELECT Announce_id FROM Announces WHERE announce_id=$announce_id;")) == 1);
	}

	static function getAnnouncePhoto ($announce_id) {
		$announce_id = intval($announce_id);
		if(mysql_num_rows(mysql_query("SELECT image FROM announceextras WHERE announce_id=$announce_id;"))>0){
			return true;
		}else{
			return false;
		}
	}	
	static function getAnnounce ($announce_id, $isImg) {
		$announce_id = intval($announce_id);
		if($isImg){
		return mysql_fetch_assoc(mysql_query("SELECT e.announce_id, e.type, e.title, e.description, e.evtDate, e.userID, e.group_id, e.schoolID, ex.image FROM announcements e INNER JOIN announceextras ex ON e.announce_id=ex.announce_id WHERE e.announce_id=$announce_id"));
		}else{
			return mysql_fetch_assoc(mysql_query("SELECT * FROM anouncements WHERE Announce_id=$announce_id;"));
		}
	}


	static function getAnnounceList ($user_id, $schoolID) {
		$data = array();
		//$q = mysql_query("SELECT * FROM Announces ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, Announce_id DESC");
		$q = mysql_query("SELECT e.announce_id,e.title, e.description,e.evtDate,g.group_name from announcements e INNER JOIN groups g ON e.group_id=g.group_id WHERE e.userID=$user_id AND e.schoolID=$schoolID;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}

	static function countAnnounce ($userID = -1) {
		$userID = intval($userID);
		$q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM annoucements;"));
		/*if ($userID == -1) $q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM Announce;"));
		else $q = mysql_fetch_assoc(mysql_query("SELECT COUNT(pre.Announce_id) as num FROM (SELECT Announce_id FROM signups WHERE userID=$userID UNION SELECT Announce_id FROM facilitators WHERE userID=$userID) as pre"));*/
		return intval($q['num']);
	}
	
	static function validateAnnounce ($data) {
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
					//$picLocation = "Announce_imgs/$itemID/".$file['picture']['name'];
					//echo $itemID."<br><img src='$picLocation'><br>";
				}	
			}
				return true;	
	}

	static function addAnnounce ($data) {

		$edate = new DateTime($data['evtDate']); // format: MM/DD/YYYY
		$data['title'] = mysql_real_escape_string($data['title']);		
		$data['evtDate'] = $edate->format('U'); 
		//$data['posted'] = $data['post_day']."-".$data['post_month']."-".$data['post_year'];
		$data['description'] = mysql_real_escape_string($data['description']);
		$data['userID'] = mysql_real_escape_string($data['userID']);
		$data['group_id'] = mysql_real_escape_string($data['group_id']);
		$data['schoolID'] = mysql_real_escape_string($data['schoolID']);
		
            // everything was fine !
		mysql_query("INSERT INTO announcements (title, evtDate, userID,group_id,schoolID,description) VALUES ('$data[title]',$data[evtDate]','$data[userID]','$data[group_id]', '$data[schoolID]', '$data[description]')");
			
		$announce_id = mysql_insert_id();
		return $announce_id;               
	}

	static function editAnnounce ($data, $announce_id) {
		$announce_id = intval($announce_id);
		$edate = new DateTime($data['evtDate']); // format: MM/DD/YYYY
		$data['title'] = mysql_real_escape_string($data['title']);		
		$data['evtDate'] = $edate->format('U');
		//$data['posted'] = $data['post_day']."-".$data['post_month']."-".$data['post_year'];
		$data['description'] = mysql_real_escape_string($data['description']);
		$data['userID'] = mysql_real_escape_string($data['userID']);
		$data['group_id'] = mysql_real_escape_string($data['group_id']);
		$data['schoolID'] = mysql_real_escape_string($data['schoolID']);
		
		mysql_query("UPDATE announcements SET title='$data[title]', evtDate='$data[evtDate]', userID='$data[userID]', group_id='$data[group_id]', schoolID='$data[schoolID]', description='$data[description]' WHERE announce_id=$announce_id;");
		
	}
	static function deleteAnnounce ($announce_id) {
		$announce_id = intval($announce_id);
		mysql_query("DELETE FROM announcements WHERE announce_id=$announce_id");
	/*	mysql_query("DELETE FROM attendance WHERE lessonID IN (SELECT lessonID FROM lessons WHERE Announce_id=$announce_id)");
		mysql_query("DELETE FROM lessons WHERE Announce_id=$announce_id");
		mysql_query("DELETE FROM signups WHERE Announce_id=$announce_id");
		mysql_query("DELETE FROM facilitators WHERE Announce_id=$announce_id");*/
	}
	
static function attachPhoto ($iid, $uploadedfile) {
	$PIC_FOLDER = "announce_img/";
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
            mysql_query("INSERT INTO announceextras (announce_id, image) VALUES ($iid, '$picName');");
             return 1;
            } else {
              return 0;
            }
	
}
//TODO: Add some verification code
static function attachPhotoUrl ($iid, $url) {
    mysql_query("INSERT INTO announceextras (announce_id, image) VALUES ($iid,'$url');");
    return 1;
}

static function deletePhoto ($iid){
	$PIC_FOLDER = "announce_img/";
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
      mysql_query("DELETE FROM announceextras WHERE announce_id='$iid';");
 }
}
static function editPhoto ($iid, $uploadedfile) {
	$PIC_FOLDER = "announce_img/";
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
            mysql_query("UPDATE announceextras SET image = '$picName' WHERE announce_id='$iid';");
          
             return 1;
            } else {
              return 0;
            }
	
}

static function getGroupList($schoolID){	
		$data = array();
		$q = mysql_query("SELECT * FROM groups WHERE schoolID=$schoolID");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}
	
	/* Generates the generic nav bar for Announces related actions */
	static function navBar ($userID) {
		$userID = intval($userID);
		$userData = Users::getUser($userID);
		if($userData['role'] == 1){
		$adminView =  1;
		}
	//View change according to user
		
		$output = "<div class='sidenav'><div class='list-group'>";
		$output .= "<div class='list-group-item nav-header'>Manage Announcements</div>";
		$output .= "<a href='viewAnnounceList.php' class='list-group-item'><i class='icon-plus'></i>View All Announcements</a>";
		$output .= "<a href='addAnnounce.php' class='list-group-item'><i class='icon-plus'></i>Add New Announcements</a>";		
		$output .= "</div></div>";

	return $output;
	}
	
	/* Generates the Announces-specific nav bar for Announces-specific actions*/
	static function AnnounceNavBar ($announce_id, $userID) {
		$userID = intval($userID);
		$userData = Users::getUser($userID);
		$announce_id = intval($announce_id);	
		$data = Announces::getAnnounces($announce_id);
		if($userData['role'] == 1){
		$adminView =  1;
		}
		
		$output = "<div class='sidenav'><div class='list-group'>";
		
		if ($adminView) $output .= "<div class='list-group-item nav-header'>".htmlentities($data['title'])."</div>";		
		if ($adminView) $output .= "<a class='list-group-item' href='editAnnounce.php?Announce_id=$announce_id'><i class='icon-edit'></i>Edit/Delete Announcement</a>";
		if ($adminView) $output .= "<a class='list-group-item' href='AnnounceStudents.php?Announce_id=$announce_id'><i class='icon-edit'></i>View Students</a>";
		$output .= "</div></div>";
	
	return $output;
	}
}
?>
