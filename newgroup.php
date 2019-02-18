<?php
	require "newhead.php";
	echo "<!DOCTYPE html>
	<head>
		<link rel='stylesheet' href='style.css' type='text/css' />
		<title>Add New Group</title>
	</head>
	<body>";
	if (isset($_GET['verify']))
	{
		if ($_POST['group_name'] != '')
		{
			$group['type'] = $_POST['type'];
			$group['group_name'] = safe($_POST['group_name']);
			createGroup($group);
			header('Location: /newgroup.php?success=1');
		}
	}
	echo "<div class='content' align='center'>
		<h2 class='subtitle'>Add New Group</h2>
		<div>
			<table>
				<form action='newgroup.php?verify=1' method='post'>
					<tr>
						<td>Type: </td>
						<td><select name='type'>
							<option value='Class' "; if (isset($_POST['type']) && $_POST['type'] == 'Class') { echo "selected "; } echo ">Class</option>
							<option value='CCA' "; if (isset($_POST['type']) && $_POST['type'] == 'CCA') { echo "selected "; } echo ">CCA</option>
							<option value='Other' "; if (isset($_POST['type']) && $_POST['type'] == 'Other') { echo "selected "; } echo ">Other</option>
						</td>
					</tr>
					<tr>
						<td>Group Name: </td>
						<td><input type='text' name='group_name' size='75' ";
							if (isset($_POST['group_name'])) { echo "value='".$_POST['group_name']."' "; }
							echo "></td>
					</tr>";
						if (isset($_POST['group_name'])) { echo "<tr><td colspan='2'><p style='color: #d11212'>Please enter a group name.</p></td></tr>'"; }
					echo "<tr>
						<td colspan='2' align='center'><input type='submit' value='Add'></td>
					</tr>";
					if (isset($_GET['success'])) { echo "<tr><td colspan='2' align='center'><p style='color:gray;'>Success!</p></td></tr>"; }
				echo "</form>
			</table>
		</div>";
	require "newtail.php";
	echo "</body>";
?>