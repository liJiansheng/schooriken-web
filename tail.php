<?php
	echo "<div class='content' align='center'>";
	if ($_SERVER["REQUEST_URI"] != '/home.php')
	{
		echo "<a href='home.php'>Home</a> | ";
	}
	echo "<a href='logout.php'>Logout</a> | ";
	if ($_SERVER["REQUEST_URI"] != '/newgroup.php')
	{
		echo "<a href='newgroup.php'>Add New Group</a> | ";
	}
	if ($_SERVER["REQUEST_URI"] != '/deletegroup.php')
	{
		echo "<a href='deletegroup.php'>Delete Group</a> | ";
	}
	if ($_SERVER["REQUEST_URI"] != '/newevent.php')
	{
		echo "<a href='newevent.php'>Add New Events & Assignments</a> | ";
	}
	if ($_SERVER["REQUEST_URI"] != '/events.php')
	{
		echo "<a href='events.php'>Edit Events & Assignments</a><br />";
	}
	echo "</div>";
	mysql_close($con);
?>