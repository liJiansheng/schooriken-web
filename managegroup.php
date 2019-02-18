<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="style.css" type="text/css" />
<title>Group Manager</title>
</head>

<body>
	<?php require "newhead.php"; ?>
    <h2 class='subtitle'>Group Manager</h2>
    <ul class='nav nav-tabs' id='schootab'>
    	<li><a href="#One" data-toggle="tab" onclick="one()">View Groups</a></li>
        <li><a href="#Two" data-toggle="tab" onclick="two()">Edit Groups</a></li>
        <li><a href="#Three" data-toggle="tab" onclick="three()">Add Groups</a></li>
    </ul>
    <div id="data" style="margin-left:20px;">
    </div>
    <script>
	$('#schootab a[href="#One"]').tab('show');
	one();
		function one()
		{
			document.getElementById("data").innerHTML="1";
		}
		function two()
		{
			document.getElementById("data").innerHTML="2";
		}
		function three()
		{
			document.getElementById("data").innerHTML="3";
		}
	</script>
    <?php require "newtail.php"; ?> 
</body>
</html>