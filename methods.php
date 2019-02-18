<?php
	
	//sql.safe_mode=false;
	$con=mysql_connect("localhost","webmaster","gu4ngu4n");
	mysql_select_db("schooriken");
	function getStudentByID($sid){ //Returns an array containing student information
		$query=mysql_query("SELECT u.userID, u.schoolID, u.name,u.email,s.class FROM users u INNER JOIN students WHERE s.student_id = u.userID AND student_id=$sid");
		while($row=mysql_fetch_assoc($query)){
			return $row;
		}
	}
	function getGroupByID($gid){ //Returns an array containing student information
		$query=mysql_query("SELECT * FROM groups WHERE group_id=$gid");
		while($row=mysql_fetch_assoc($query)){
			return $row;
		}
	}
	function getEventByID($eid){ //Returns an array containing event information
		$query=mysql_query("SELECT * FROM events WHERE event_id=$eid");
		while($row=mysql_fetch_assoc($query)){
			return $row;
		}
	}
	
	function getTeacherByID($tid){ //Returns an array containing teacher information
		$query=mysql_query("SELECT * FROM users WHERE userID=$tid AND role=1");
		while($row=mysql_fetch_assoc($query)){
			return $row;
		}
	}
	function getImageByEventID($eid){
		$query=mysql_query("SELECT image FROM eventextras WHERE event_id=$eid");
		while($row=mysql_fetch_assoc($query)){
			//echo "<br/>**** Debug ****<br/>".$row['image']."<br/>";
			return $row['image'];
		}
	}
	function getStudentEvents($sid){ //Returns the details of all the events a student needs to see
		$query=mysql_query("SELECT * FROM studentevents WHERE student_id=$sid");
		//$query=mysql_query("SELECT se.event_id, se.student_id,se.group_id, se.flag, se.show FROM studentevents se INNER JOIN studentgroups sg ON se.group_id = sg.group_id WHERE sg.student_id=$sid");
		$i=0;
		//$r=mysql_fetch_assoc($query);
		
		while($row=mysql_fetch_assoc($query)){
            $isShow = $row['isShow'];	
			$eid=$row['event_id'];
			$gid=$row['group_id'];
			$temp=getEventByID($eid);	
			$grp = getGroupByID($gid);		
			$temp['flag']=$row['flag'];
			//echo "getstuevents ".niceTime($temp['evtDate']);
            if ($temp['evtDate']<time()) {
                $isShow = 0;
            }
			$temp['evtDate'] = niceTime($temp['evtDate']);	
			$temp['posted'] = niceTime($temp['posted']);
			$temp['group_name'] = $grp['group_name'];			
			$temp['image']=getImageByEventID($eid);
			$temp['isShow'] = $isShow;
			//echo "getstuevents ".niceTime($row['show']);
			if($isShow==1){ //Remove events that need not be shown
				$events[$i]=$temp;
				$i++;	
			}
		}
		return isset($events)?$events:null;
	}
	
	function getTeacherEvents($tid){ //Returns an array containing all the events a teacher has created ever
		$query=mysql_query("SELECT * FROM events WHERE userID=$tid");
		$i=0;
		while($row=mysql_fetch_assoc($query)){
			$eid=$row['event_id'];
			$temp=getEventByID($eid);
			$temp['image']=getImageByEventID($eid);
			$events[$i]=$temp;
			$i++;
		}
		return $events;
	}
	
	function getEventStudents($eid){ //Returns an array containing all the students in an event and the flag status
		$query=mysql_query("SELECT * FROM studentevents WHERE event_id=$eid");
		$i=0;
		$students = array();
		while($row=mysql_fetch_assoc($query)){
			$sid=$row['student_id'];
			$temp=getStudentByID($sid);
			$temp['flag']=$row['flag']; //Append the flag to the student information
			$students[$i]=$temp;
			$i++;
		}
		return $students;
	}
	
	function getStudentGroups($sid){ //Returns an array containing details of all the groups a student is in
		$query=mysql_query("SELECT * FROM studentgroups WHERE student_id=$sid");
		$i=0;
		$groups=array();
		while($row=mysql_fetch_assoc($query)){
			$gid=$row['group_id'];
			$temp=getGroupByID($gid);
			$groups[$i]=$temp;
			$i++;
		}
		return $groups;
	}
	
	function getGroupStudents($gid){ //Returns an array containing all the students in a group
		$query=mysql_query("SELECT student_id FROM studentgroups WHERE group_id=$gid");
		$i=0;
		while($row=mysql_fetch_assoc($query)){
			$students[$i]=getStudentByID($row['student_id']);
			$i++;
		}
		return $students;
	}
	
	function getGroups(){ //Returns an array containing the information of every group
		$query=mysql_query("SELECT * FROM groups");
		echo mysql_error();
		$i=0;
		while($row=mysql_fetch_assoc($query)){
			$groups[$i]=$row;
			$i++;
		}
		return $groups;
	}
	
	function addStudentToGroup($sid,$gid){ //Given student and group ID, adds the student to a group
		mysql_query("INSERT INTO studentgroups (student_id,group_id) VALUES ($sid,$gid)") or die(mysql_error());
		echo "Adding to Group<br/>";
		$sql="select event_id from events where group_id=$gid";
		echo $sql."<br/>";
		$res=mysql_query($sql);
		if (mysql_error())
		{
			echo "Error: ".mysql_error()."<br/>";
		}
		else
		{
			while ($r=mysql_fetch_array($res))
			{
				//var_dump($r);
				$sql="insert into studentevents(event_id,student_id,group_id,flag) values(".$r[0].",$sid,$gid,0)";
				//echo $sql."<br/>";
				mysql_query($sql);
				if (mysql_error())
				{
					echo "Error: ".mysql_error()."<br/>";
				}
			}
		}
		return true;
	}
	
	function removeStudentFromGroup($sid,$gid){ //Given student and group ID, remove the student from a group
		mysql_query("DELETE FROM studentgroups WHERE student_id=$sid AND group_id=$gid") or die(mysql_error());
		mysql_query("DELETE FROM studentevents WHERE group_id=$gid AND student_id=$sid") or die(mysql_error());
		return true;
	}
	
	function safe($str){ //Make a string safe for SQL use
		return mysql_real_escape_string($str);
	}
	
	function verifyStudentID($sid){ //Check if student ID is valid
		$query=mysql_query("SELECT student_id FROM students WHERE student_id=$sid");
		return mysql_num_rows($query)>0?true:false;
	}
	
	function verifyGroupID($gid){ //Check if group ID is valid
		$query=mysql_query("SELECT group_id FROM groups WHERE group_id=$gid");
		return mysql_num_rows($query)>0?true:false;
	}
	
	function createEvent($event){ //Given an array of event details, it creates an event
		mysql_query("INSERT INTO events (type,title,description,date,posted,teacher_id,group_id) VALUES ('$event[type]','$event[title]','$event[description]','$event[date]', CURDATE(),'$event[teacher_id]','$event[group_id]')") or die(mysql_error());
		$students=getGroupStudents($event['group_id']);
		$eid=mysql_insert_id();
		$gid=$event['group_id'];
		for($i=0;$i<count($students);$i++){
			$sid=$students[$i]['student_id'];
			mysql_query("INSERT INTO studentevents (event_id,student_id,group_id,flag) VALUES ($eid,$sid,$gid, 1)");
		}
		if(strtolower(trim($event['type']))=="event"){
			mysql_query("INSERT INTO 'eventextras' ('image', 'event_id') VALUES ('$event[image]', $eid)") or die(mysql_error());
		}
		return true;
	}
	function editEvent($event,$eid){ //Given an array of event details, it edits an event
		mysql_query("UPDATE events SET title='$event[title]',description='$event[description]',date='$event[date]' WHERE event_id=$eid") or die(mysql_error());
		$eventb=getEventByID($eid);
		if(strtolower($eventb['type'])=="event"){mysql_query("UPDATE 'eventextras' SET 'image'='$event[image]' WHERE event_id=$eid");}
		return true;
	}
	function deleteEvent($eid){ // Deletes events completely
		mysql_query("DELETE FROM events WHERE event_id=$eid");
		mysql_query("DELETE FROM studentevents WHERE event_id=$eid");
		return true;
	}
	
	function createGroup($group){ //Given an array of group details, it creates a group
		mysql_query("INSERT INTO groups (type,group_name) VALUES ('$group[type]','$group[group_name]')") or die(mysql_error());
		return true;
	}
	
	function deleteGroup($gid){
		mysql_query("DELETE FROM groups WHERE group_id=$gid");
		mysql_query("DELETE FROM studentgroups WHERE group_id=$gid");
		$query=mysql_query("SELECT event_id FROM events WHERE group_id=$gid");
		while($row=mysql_fetch_assoc($query)){
			deleteEvent($row['event_id']);
		}
		return true;
		
	}
	function getStudentID($user, $pass){ //Retrieve user ID from username and password
		$query=mysql_query("SELECT userID FROM users WHERE username = '$user' and password='$pass' and role=2");
		while($row=mysql_fetch_assoc($query)){
			return $row['userID'];
		}
	}
	function setFlagTrue($eid,$sid){ //Given an event ID and student ID, set the flag true
		mysql_query("UPDATE studentevents SET flag=1 WHERE event_id=$eid AND student_id=$sid");
		$event=getEventByID($eid);
		if($event['type']=="Assignment"){
			setShowFalse($eid,$sid);
		}
		return true;
	}
	
	function setFlagFalse($eid,$sid){ //Given an event ID and student ID, set the flag false
		mysql_query("UPDATE studentevents SET flag=0 WHERE event_id=$eid AND student_id=$sid");
		return true;
	}
	
	function setShowFalse($eid, $sid){ //Given an event ID and a student ID, set show to be false
		mysql_query("UPDATE studentevents SET isShow=0 WHERE event_id=$eid AND student_id=$sid");
		return true;
	}
	function isStudentGroup($gid,$sid){ //Checks if a student is in a group using the student and group id
		$query=mysql_query("SELECT group_id FROM studentgroups WHERE group_id=$gid AND student_id=$sid");
		if(mysql_num_rows($query)>0){
			return true;
		}
		else{
			return false;
		}
	}
	function getNonStudentGroups($sid){ //Gets all the groups that a student is not a part of
		$Groups=getGroups();
		$j=0;
		for($i=0;$i<count($Groups);$i++){
			$gid=$Groups[$i]['group_id'];
			if(!isStudentGroup($gid,$sid)){
				$nsGroups[$j]=$Groups[$i];
				$j++;
			}
		}
		return $nsGroups;
	}
	function getFlag($sid,$eid){
		$query=mysql_query("SELECT flag FROM studentevents WHERE student_id=$sid AND event_id=$eid");
		while($row=mysql_fetch_assoc($query)){
			return $row['flag'];
		}
	}
	function getShow($sid,$eid){
		$query=mysql_query("SELECT isShow FROM studentevents WHERE student_id=$sid AND event_id=$eid");
		while($row=mysql_fetch_assoc($query)){
			return $row['isShow'];
		}
	}
	$query=mysql_query("SELECT * FROM studentevents WHERE DATEDIFF(day, date, CURDATE())>0 AND isShow=1");
	while($row=mysql_fetch_assoc($query)){
		$event=getEventByID($row['event_id']);
		if(strtolower($event['type'])=="event"){
			setShowFalse($row['event_id'],$row['student_id']);
		}
	}
	
	function niceTime($timeStr) {
	//$t = strtotime($timeStr);

	$dt = new DateTime("@$timeStr");  // convert UNIX timestamp to PHP DateTime
return $dt->format('Y-m-d H:i:s'); 
}
?>