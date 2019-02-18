<?php
	require "head.php";
	echo "<!DOCTYPE html>
	<head>
		<link rel='stylesheet' href='style.css' type='text/css' />
		<title>Add New Event or Assignment</title>
		<script>
			function enableImage() {
				document.getElementById('image').disabled = false;
			}
			function disableImage() {
				document.getElementById('image').disabled = true;
			}
		</script>
	</head>
	<body>";
	$groups = getGroups();
	if (isset($_GET['verify'])) {
		if (trim($_POST['title']) == '')
		{
			$_GET['title'] = 1;
		}
		
		$date = $_POST['date'];
		
		if (!checkdate(intval(substr($date, 5, 2)),intval(substr($date, 8, 2)),intval(substr($date, 0, 4))))
		{
			$_GET['date'] = 1;
		}
		
		if (!isset($_GET['date']) && !isset($_GET['title'])) {
			$event['type'] = $_POST['type'];
			$event['title'] = safe($_POST['title']);
			$event['description'] = safe($_POST['description']);
			$event['date'] = $_POST['date'];
			$event['teacher_id'] = $uid;
			$event['group_id'] = $_POST['groupid'];
			if ($event['type'] == "Event") {
				if (trim($_POST['image']) == '') {
					$event['image'] = 'http://placekitten.com/600/400';
				}
				else {
					$event['image'] = $_POST['image'];
				}
			}
			createEvent($event);
			header('Location: /newevent.php?success=1');
		}
	}
	
	echo "<div class='content' align='center'>
		<h2 class='subtitle'>Add New Event or Assignment</h2>
		<div>
			<table>
				<form action='newevent.php?verify=1' method='post'>
					<tr>
						<td>Group: </td>
						<td><select name='groupid'>";
							for ($i = 0; $i < count($groups); $i++)
							{
								$group = $groups[$i];
								echo "<option value='".$group['group_id']."'";
								if (isset($_POST['groupid']) && $_POST['groupid'] == $group['group_id']) { echo " selected "; }
								echo ">".$group['group_name']."</option>";
							}
						echo "</td>
					</tr>
					<tr>
						<td>Type: </td>
						<td>
							<input type='radio' name='type' value='Assignment' id='assignment' onclick=disableImage() ";
							if ((isset($_POST['type']) && $_POST['type'] == "assignment") || (!isset($_POST['type']))) { echo "checked "; }
							echo ">Assignment 
							<input type='radio' name='type' value='Event' id='event' onclick=enableImage() ";
							if (isset($_POST['type']) && $_POST['type'] == "event") { echo "checked "; }
							echo ">Event
						</td>
					</tr>
					<tr>
						<td>Title: </td>
						<td><input type='text' name='title' size='75' ";
							if (isset($_POST['title'])) { echo "value='".$_POST['title']."' "; }
							echo "></td>
					</tr>";
						if (isset($_GET['title'])) { echo "<tr><td colspan='2'><p style='color: #d11212'>Please enter a title.</p></td></tr>'"; }
					echo "<tr>
						<td>Description: </td>
						<td><textarea name='description' rows='10'>";
							if (isset($_POST['description'])) { echo $_POST['description']; }
							echo "</textarea></td>
					</tr>
					<tr>
						<td>Date: </td>
						<td><input type='date' name='date' ";
							if (isset($_POST['date'])) { echo "value='".$_POST['date']."' "; }
							echo "></td>";
						if (isset($_GET['date'])) { echo "<tr><td colspan='2'><p style='color: #d11212'>Please enter a date.</p></td></tr>'"; }
						echo "
					</tr>
					<tr>
						<td>Image URL (Optional): </td>
						<td><input type='url' name='image' id='image' ";
						if (isset($_POST['image'])) { echo "value='".$_POST['image']."' "; }
						echo "/></td>
						<script>
						if(document.getElementById('assignment').checked) {
							disableImage();
						}
						else if(document.getElementById('event').checked) {
							enableImage();
						}
						</script>
					<tr>
						<td colspan='2' align='center'><input type='submit' value='Add'></td>
					</tr>";
					if (isset($_GET['success'])) { echo "<tr><td colspan='2' align='center'><p style='color:gray;'>Success!</p></td></tr>"; }
				echo "</form>
			</table>
		</div>";
	require "tail.php";
	echo "</body>";
?>