<?php
/*
Expects the following variables from post:
group_name => group name
type => group type
add_opt => group type if other (Opt.)

Does the following:
Check and add new group
*/
//var_dump($_POST);
//echo "<br/>";
$err=false; //Presume no errors
//Look for required variables
if (!isset($_POST["group_name"]) || $_POST["group_name"]=="")
{
	//Ouch, error
	$err=true;
	echo "Group Name is not set<br/>";
}
else
{
	$grp_name=addslashes($_POST["group_name"]);
}
if (!isset($_POST["type"]) || $_POST["type"]=="")
{
	//Ouch, error
	$err=true;
	echo "Group Type is not set<br/>";
}
else
{
	$grp_type=addslashes($_POST["type"]);
	if ($_POST["type"]=="Others" && (!isset($_POST["add_opt"]) || $_POST["add_opt"]==""))
	{
		//Ouch, error
		$err=true;
		echo "Group Type (Others) is not set<br/>";
	}
	elseif ($_POST["type"]=="Others")
	{
		$grp_type=addslashes($_POST["add_opt"]);
	}
}
$db_host = "localhost";
$db_username = "schooriken";
$db_passwd = "pass123";
$db= mysql_connect($db_host, $db_username, $db_passwd) or die (mysql_error());
$sql="use schooriken";
if (!mysql_query($sql))
{
	$err=true;
	echo "Error: ".mysql_error()."<br/>";
}
//If no error, proceed
if (!$err)
{
	//Add new group
	$sql="insert into groups(type,group_name) values('".$grp_type."','".$grp_name."');";
	if (!mysql_query($sql))
	{
		$err=true;
		echo "Error: ".mysql_error()."<br/>";
	}
}
//If error, report
if ($err)
{
	echo "You have encountered errors.<br/>Please report them to System Admin via email.<br/>Thanks<br/><form action='managegroup_add.php' method='post'><input type='submit' value='Return'/></form>";
}
else
{
	//Redirect
	header("Location: /managegroup_add.php");
}
?>