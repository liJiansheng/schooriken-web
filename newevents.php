<?php
	require "newhead.php";
	echo "<!DOCTYPE html>
	<head>
		<link rel='stylesheet' href='style.css' type='text/css' />
		<title>Edit Events & Assignments</title>
	</head>
	<body>
		<div class='content' align='center'>
		<h2 class='subtitle'>Edit Events & Assignments</h2>
		<div>
			<p style='color:gray;align=center'>Click on event names to see their details and edit them.</p>
			<table class='table' border='1';>
				<thead>
					<tr>
						<th style='width:5%;'>No.</th>
						<th style='width:5%;'>Type</th>
						<th style='width:85%;'>Event/Assignment Name</th>
						<th style='width:5%;'>Delete</th>
					</tr>
				</thead>
				<tbody style='overflow:auto;'>";
	$query = mysql_query("SELECT * FROM `events` WHERE `teacher_id` = '".$uid."' ORDER BY type DESC") or die(mysql_error());
	$i = 0;
	while ($row = mysql_fetch_array($query))
	{
		$i++;
		echo "<tr>
				<td><p style='text-align:right'>".$i."</p></td>
				<td>".$row['type']."</td>
				<td><a href='/editevent.php?eventid=".$row['event_id']."'>".$row['title']."</a></td>
				<td>
					<center><a href='/deleteevent.php?eventid=".$row['event_id']."'><img src='redcross.png' style='width:16px; height:16px;' /></a></center>
				</td>
			</tr>";
	}			
	echo "</tbody>
	</table>";
	require "newtail.php";
	echo "</body>";
?>