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
	echo "<!DOCTYPE html>
	<head>
		<link rel='stylesheet' href='style.css' type='text/css' />
		<title>Edit Event or Assignment</title>
	</head>
	<body>
		<div class='content' align='center'>
		<h2 class='subtitle'>Edit Event or Assignment</h2>
		<div>
			<table>
				<form action='editeventDB.php?eventid=".$eventid."' method='post'>
					<tr>
						<td>Title: </td>
						<td><input type='text' name='title' size='75' value='$event[title]'></td>";
						if (isset($_GET['title'])) { echo "<tr><td colspan='2'><p style='color: #d11212'>Please enter a title.</p></td></tr>'"; }
						echo "
					</tr>
					<tr>
						<td>Description: </td>
						<td><textarea name='description' rows='10'>$event[description]</textarea></td>
					</tr>
					<tr>
						<td>Date: </td>
						<td><input type='date' name='date' value='$event[date]'></td>";
						if (isset($_GET['date'])) { echo "<tr><td colspan='2'><p style='color: #d11212'>Please enter a date.</p></td></tr>'"; }
						echo "
					</tr>";
					if ($event['type'] = "Event") {
					echo "<tr>
						<td>Image URL (Optional): </td>
						<td><input type='url' name='image' value='".$event['image']."'/></td>
					</tr>"; }
					echo "<tr>
						<td colspan='2' align='center'><input type='submit' value='Edit'></td>
					</tr>".
					(isset($_GET['success'])?"<tr>
						<td colspan='2' align='center'><p style='color:gray;'>Success!</p></td>
					</tr>":"")."
				</form>
			</table>";
			if (lower($event['type']) == "assignment") {
				$yesText = "Completed Assignment";
				$noText = "Not Yet Completed";
			}
			
			else {
				$yesText = "Accepted Invitation";
				$noText = "Not Yet Accepted";
			}
			$students = getEventStudents($eventid);
			echo "<h2 class='subtitle'>".$yesText."</h2>
				<table>";
			$i = 0;
			for ($j = 0; $j < count($students); $j++)
			{
				$student = $students[$j];
				if ($student['flag'] == 1) {
					$i++;
					echo "<tr>
						<td><p style='text-align:right'>".$i."</p></td><td>".$student['name']."</td>
					</tr>";
				}
			}					
			echo "</table>
			<h2 class='subtitle'>".$noText."</h2>
			<table>";
			$k = 0;
			for ($l = 0; $l < count($students); $l++)
			{
				$student = $students[$l];
				if ($student['flag'] == 0) {
					$k++;
					echo "<tr>
						<td><p style='text-align:right'>".$k."</p></td><td>".$student['name']."</td>
					</tr>";
				}
			}
		echo "</table>
		</div>
	</div>";
	require "newtail.php";
echo "</body>";
?>