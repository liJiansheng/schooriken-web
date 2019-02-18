<?php
require_once("connect.php");
require_once("users.php");

class Groups {

	static function getGroupList ($userID, $schoolID) {
		$data = array();
		//$q = mysql_query("SELECT * FROM Groups ORDER BY (CASE status WHEN 'Upcoming' THEN 1 WHEN 'Ongoing' THEN 2 WHEN 'Over' THEN 3 END) ASC, signup DESC, group_id DESC");
		$q = mysql_query("SELECT * from groups WHERE userID=$userID AND schoolID=$schoolID;");
		while ($r = mysql_fetch_assoc($q)) {
			$data[] = $r;
		}
		return $data;
	}

	static function groupExists ($group_id) {
		$group_id = intval($group_id);
		if ($group_id == null) return false;
		else return (mysql_num_rows(mysql_query("SELECT group_id FROM groups WHERE group_id=$group_id;")) == 1);
	}

	static function getGroup ($group_id) {
		$group_id = intval($group_id);
			return mysql_fetch_assoc(mysql_query("SELECT * FROM groups WHERE group_id=$group_id;"));		
	}
    
    static function getGroupName($group_id) {
        $group_id = intval($group_id);
        $t = mysql_fetch_assoc(mysql_query("SELECT group_name FROM groups WHERE group_id=$group_id"));
        return $t['group_name'];
    }
	

	static function countGroup ($userID = -1) {
		$userID = intval($userID);
		$q = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) as num FROM group;"));
	
		return intval($q['num']);
	}
	
	static function validateGroup ($data) {
		$fieldsToCheck = array('group_name','description');
		foreach ($fieldsToCheck as $k => $field) {
			if (!isset($data[$field]) || $data[$field] == "") {
				errorMessage("Field '$field' cannot be left blank!");
				return false;
			}
		}	
		return true;
	}

	static function addGroup ($edata) {

		$edata['group_name'] = mysql_real_escape_string($edata['group_name']);		
		$edata['type'] = mysql_real_escape_string($edata['type']);		
		//$data['posted'] = $data['post_day']."-".$data['post_month']."-".$data['post_year'];
		$edata['description'] = mysql_real_escape_string($edata['description']);
		$edata['group_id'] = mysql_real_escape_string($edata['group_id']);
		$edata['userID']=mysql_real_escape_string($edata['userID']);
		$edata['schoolID'] = mysql_real_escape_string($edata['schoolID']);
		
            // everything was fine !
		mysql_query("INSERT INTO groups (group_id, group_name, type, userID,schoolID,description) VALUES ('$edata[group_id]','$edata[group_name]','$edata[type]','$edata[userID]', '$edata[schoolID]', '$edata[description]')");
			
		$group_id = mysql_insert_id();
		return $group_id;               
	}

	static function editGroup ($data, $group_id) {
		$group_id = intval($group_id);
		$data['group_name'] = mysql_real_escape_string($data['group_name']);		
		$data['type'] = mysql_real_escape_string($data['type']);		
		//$data['posted'] = $data['post_day']."-".$data['post_month']."-".$data['post_year'];
		$data['description'] = mysql_real_escape_string($data['description']);
		$data['group_id'] = mysql_real_escape_string($data['group_id']);
		$data['userID']=mysql_real_escape_string($data['userID']);
		$data['schoolID'] = mysql_real_escape_string($data['schoolID']);
		mysql_query("UPDATE groups SET userID='$data[userID]',group_name='$data[group_name]', type='$data[type]', group_id='$data[group_id]', schoolID='$data[schoolID]', description='$data[description]' WHERE group_id=$group_id;");
		
	}
	static function deleteGroup ($group_id) {
		$group_id = intval($group_id);
		mysql_query("DELETE FROM groups WHERE group_id=$group_id");
		mysql_query("DELETE FROM studentgroups WHERE group_id=$group_id");
		mysql_query("DELETE FROM studentevents WHERE group_id=$group_id");
	/*	mysql_query("DELETE FROM attendance WHERE lessonID IN (SELECT lessonID FROM lessons WHERE group_id=$group_id)");
		mysql_query("DELETE FROM lessons WHERE group_id=$group_id");
		mysql_query("DELETE FROM signups WHERE group_id=$group_id");
		mysql_query("DELETE FROM facilitators WHERE group_id=$group_id");*/
	}


	/* Generates the generic nav bar for Groups related actions */
	static function navBar ($userID) {
		$userID = intval($userID);
		$userData = Users::getUser($userID);
		if($userData['role'] == 1){
		$adminView =  1;
		}
	//View change according to user
		$output = "<div class='sidenav'><div class='list-group'>";
		$output .= "<div class='list-group-item nav-header'>Manage Groups</div>";
		$output .= "<a href='viewGroupList.php' class='list-group-item'><i class='icon-list'></i> View All Groups</a>";
		$output .= "<a href='addGroup.php' class='list-group-item'><i class='icon-plus'></i> Add New Groups</a>";		
		$output .= "</div></div>";
		return $output;
	}
	
	/* Generates the Groups-specific nav bar for Groups-specific actions*/
	static function GroupsNavBar ($group_id, $userID) {
		$userID = intval($userID);
		$userData = Users::getUser($userID);
		$group_id = intval($group_id);	
		$data = Groups::getGroup($group_id);
		if($userData['role'] == 1){
		$adminView =  1;
		}
		
		$output = "<div class='sidenav'><div class='list-group'>";

		if ($adminView) $output .= "<div class='list-group-item nav-header'>".htmlentities($data['group_name'])."</div>";		
		if ($adminView) $output .= "<a class='list-group-item' href='editGroup.php?group_id=$group_id'><i class='icon-edit'></i> Edit/Delete Groups</a>";
		$output .= "</div></div>";
		return $output;
	}
}
?>
