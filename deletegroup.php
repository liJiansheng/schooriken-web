<?php 
require "head.php";
if (isset($_POST['groupid'])) {
	$groupid = $_POST['groupid'];
	$group = getGroupByID($groupid);	
	$teacher = getTeacherByID($uid);
	$headers = "From: ".$teacher['username']."@schooriken.riicc.sg";
	$subject = $group['group_name']." has been deleted";
	$message = "Please ignore any future notifications regarding events associated with this group.";
	$recipients = getGroupStudents($groupid);
	for ($i = 0; $i < count($recipients); $i++) {
		mail($recipients[$i]['email'],$subject,$message,$headers);
	}
	deleteGroup($groupid);
	header('Location: /deletegroup.php?success=1');
}

$groups = getGroups();

echo "<!DOCTYPE html>
<head>
	<link rel='stylesheet' href='style.css' type='text/css' />
	<title>Delete Group</title>
</head>
<body>
	<div class='content' align='center'>
	<h2 class='subtitle'>Delete Group</h2>
	<table>
		<form action='deletegroup.php' method='post'>
			<tr>
				<td>Group: </td>
				<td><select name='groupid'>";
					for ($i = 0; $i < count($groups); $i++)
					{
						$group = $groups[$i];
						echo "<option value='".$group['group_id']."'>".$group['group_name']."</option>";
					}
					echo "</td>
			</tr>
			<tr>
				<td colspan='2' align='center'><input type='submit' value='Delete'></td>
			</tr>";
		if (isset($_GET['success'])) { echo "<tr><td colspan='2' align='center'><p style='color:gray;'>Success!</p></td></tr>"; }
				echo "</form>
	</table>";	
require "tail.php";
echo "</body>";
?>