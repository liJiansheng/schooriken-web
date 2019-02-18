<?php
$ex=array();
$exnum=0;
if (isset($_POST["group"]))
{
	$grp=addslashes($_POST["group"]);
}
else
{
	$ex[$exnum]="No group id parsed";
	$exnum+=1;
	$grp=-1;
}
$db_host = "localhost";
$db_username = "schooriken";
$db_passwd = "pass123";
$db= mysql_connect($db_host, $db_username, $db_passwd) or die (mysql_error());
$sql="use schooriken";
if (!mysql_query($sql))
{
	$ex[$exnum]=mysql_error();
	$exnum+=1;
}
$sql="select * from studentgroups where group_id='".$grp."'";
//$ex[$exnum]=$sql;
//$exnum+=1;
$res=mysql_query($sql);
$ans=array();
$i=0;
if (!$res)
{
	$ex[$exnum]=mysql_error();
	$exnum+=1;
}
else
{
	//$ex[$exnum]="successful result";
	//$exnum+=1;
	while ($a=mysql_fetch_array($res))
	{
		//$ex[$exnum]="returned a student";
		//$exnum+=1;
		$sql="select * from students where student_id='".$a[0]."'";
		//$ex[$exnum]=$sql;
		//$exnum+=1;
		if (!$resres=mysql_query($sql))
		{
			$ex[$exnum]=mysql_error();
			$exnum+=1;
		}
		else
		{
			while ($b=mysql_fetch_array($resres))
			{
				$ans[$i]=$b[2];
				$i+=1;
			}
		}
	}
}
echo $exnum.",";
for ($j=0; $j<$exnum; $j++)
{
	echo $ex[$j].",";
}
echo $i.",";
for ($j=0; $j<$i; $j++)
{
	echo $ans[$j].",";
}
?>