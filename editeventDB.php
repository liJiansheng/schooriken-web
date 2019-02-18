<?php 
require "head.php";
if (!isset($_GET['eventid'])) {
	header('Location: /events.php');
}

$eventid = $_GET['eventid'];
$oldevent = getEventByID($eventid);
if ($oldevent['teacher_id'] != $uid) {
	header('Location: /events.php');
}

$event = array();
$event['title'] = $_POST['title'];
$event['description'] = $_POST['description'];
$event['date'] = $_POST['date'];

$errors = "?";

if ($oldevent['type'] == "Event") {
	if (trim($_POST['image']) == '') {
		$event['image'] = 'http://placekitten.com/600/400';
	}

	else {
		$event['image'] = $_POST['image'];
	}
}

if (trim($_POST['title']) == '')
{
	$errors .= "title=1&";
}

$date = $_POST['date'];

if (!checkdate(intval(substr($date, 5, 2)),intval(substr($date, 8, 2)),intval(substr($date, 0, 4))))
{
	$errors .= "date=1&";
}

if ($errors != "?") {
	header('Location: /editevent.php'.$errors."eventid=".$eventid);
}

else {
	editEvent($event,$eventid);
	header("Location: /editevent.php?success=1&eventid=$eventid");
}
require "tail.php";