<?php 
require "newhead.php";
if (!isset($_GET['eventid'])) {
	header('Location: /events.php');
}

$eventid = $_GET['eventid'];
$event = getEventByID($eventid);
if ($event['teacher_id'] != $uid) {
	header('Location: /events.php');
}

$tid = $event['teacher_id'];
$teacher = getTeacherByID($tid);
$headers = "From: ".$teacher['username']."@schooriken.riicc.sg";
$subject = $event['title']." has been deleted";
$message = "Kindly ignore any future notifications for this event.";
$recipients = getEventStudents($eventid);
for ($i = 0; $i < count($recipients); $i++) {
	if (getShow($recipients[$i]['student_id'],$eventid) == 1) {
		mail($recipients[$i]['email'],$subject,$message,$headers);
	}
}

deleteEvent($eventid);
header('Location: /events.php?success=1'); 
mysql_close($con);
?>