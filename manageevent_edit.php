<?php
require_once("session.php");
require_once("users.php");
require_once("common.php");
$sess->authPage(1);
require_once("top.php"); ?>
   <div class="container">
        <p></p>
      <h2>Event Manager</h2>
        <ul class='nav nav-tabs' id='schootab'>
            <li><a href="manageevent_view.php">View Events</a></li>
            <li class="active"><a href="#">Edit Events</a></li>
            <li><a href="manageevent_add.php">Add Events</a></li>
        </ul>        
        <div id="data" class="row">
           <div class="buffer-sm"> </div>
            <div class="col-md-4">
            <form><fieldset>Filter: <input id="filt_input" type="text" /></fieldset></form>
               <div class="buffer-sm"> </div>
            <span id='opts'><select id='optio' onchange='update_group()'>
            </select></span>
            </div>
            <div class="col-md-4">
            <h2>Event Info</h2><br/>
            <span id='group_info'></span>
            <form action="manageevent_edit.php" method='post'>
            	<input type='hidden' name='group_id' id='grp_id' />
                <span id='edit_span'>
                </span>
            </form>
            <span id='fetch'></span>
            </div>
        </div>
    </div>
    <?php require "newtail.php"; ?> 
<script>
var group_names = new Array();
var group_id= new Array();
var group_type= new Array();
var group_desc= new Array();
var group_datedue= new Array();
var group_datepos= new Array();
var group_teacher= new Array();
var group_grp= new Array();
var name_valid=true;
//populate group_names with actual group names using php, api
<?php
$sql="select * from events";
$res=mysql_query($sql);
if (!$res)
{
	echo "alert(\"Error: ".mysql_error()."\")\n";
}
$i=0;
while($row=mysql_fetch_array($res)){
	echo "group_id.push(\"".$row[0]."\");\n";
	echo "group_names.push(\"".$row[2]."\");\n";
	echo "group_type.push(\"".$row[1]."\");\n";
	$reparr=array(
		"0" => "\r",
		"1" => "\n",
	);
	if ($i==0)
	{
		echo "group_desc.push(\"".str_replace($reparr,"<br/>",$row[3])."\");\n";
		/*for ($j=0; $j<strlen($row[3]); $j++)
		{
			echo "(".ord($row[3][$j]).",".$row[3][$j].")";
			//echo ord(($row[3])[$j]);
			echo ",";
		}
		echo "\n";*/
	}
	else
	{
		echo "group_desc.push(\"".str_replace($reparr,"<br/>",$row[3])."\");\n";
	}
	echo "group_datedue.push(\"".$row[4]."\");\n";
	echo "group_datepos.push(\"".$row[5]."\");\n";
	echo "group_teacher.push(\"".$row[6]."\");\n";
	echo "group_grp.push(\"".$row[7]."\");\n";
	$i+=1;
}
?>
update_options();
var selid=-1;
$("#filt_input").bind('input',function (){
	update_options();
});
<?php
//to check if parsed in initial values
if (isset($_POST["group_id"]))
{
	echo "$(\"#optio\").val(\"".$_POST["group_id"]."\");\n";
	echo "update_group_input(\"".$_POST["group_id"]."\");\n";
}
?>
function update_options()
{
	var filt=document.getElementById("filt_input").value;
	var ans="<select size='10' id='optio' onchange='update_group()'>\n";
	for (var i=0; i<group_names.length; i++)
	{
		if (group_names[i].toLowerCase().indexOf(filt.toLowerCase())!=-1 || filt=="")
		{
			ans=ans+"<option>"+group_names[i]+"</option>\n";
		}
	}
	ans=ans+"</select>\n";
	document.getElementById("opts").innerHTML=ans;
}
function update_group_input(grp)
{
	var ans="<form action='update_group.php' method='post'><table border='1'>";
	var i;
	ans=ans+"<tr><td>Name: </td><td><input type='text' value='"+grp+"'/></td></tr>";
	for (var i=0; i<group_names.length; i++)
	{
		if (group_names[i]==grp)
		{
			var types=["Class","CCA","Others"];
			ans=ans+"<tr><td>Type: </td><td><select name='type_sel' id='type_opt' onchange='eval_type()'>";
			ans=ans+"<option>"+group_type[i]+"</option>";
			for (j=0; j<types.length; j+=1)
			{
				//alert(types[j]);
				//alert(group_type[j]);
				if (types[j]!=group_type[i])
				{
					ans=ans+"<option>"+types[j]+"</option>";
				}
			}
			ans=ans+"</select><span id='type_span'></span></td></tr></table></form>";
			//ans=ans+"Group Members: <br/>";
			selid=group_id[i];
		}
	}
	document.getElementById("group_info").innerHTML=ans;
	$("#fetch").text("Fetching Data...");
	$("#grp_id").val(selid);
	$("#edit_span").html("<input class='btn btn-primary' type='submit' value='Confirm Changes'/>");
	runajax(selid);
}
function update_group()
{
	var grp=document.getElementById("optio").value;
	var ans="<form action='update_group.php' method='post'><table border='1'>";
	var i;
	ans=ans+"<tr><td>Name: </td><td><input type='text' value='"+grp+"'/></td></tr>";
	for (var i=0; i<group_names.length; i++)
	{
		if (group_names[i]==grp)
		{
			var types=["Class","CCA","Others"];
			ans=ans+"<tr><td>Type: </td><td><select name='type_sel' id='type_opt' onchange='eval_type()'>";
			ans=ans+"<option>"+group_type[i]+"</option>";
			for (j=0; j<types.length; j+=1)
			{
				//alert(types[j]);
				//alert(group_type[j]);
				if (types[j]!=group_type[i])
				{
					ans=ans+"<option>"+types[j]+"</option>";
				}
			}
			ans=ans+"</select>";
			ans=ans+"<tr><td>Description: </td><td><textarea id='grp_desc' name='grp_desc' rows='10'>"+group_desc[i]+"</textarea></td></tr>";
			ans=ans+"<tr><td>Date Due: </td><td>"+group_datedue[i]+"</td></tr>";
			$dt1=new Date(group_datedue[i]);
			$dt2=new Date();
			$val=Math.round(($dt1.getTime()-$dt2.getTime())/(24*60*60*1000));
			if ($val<0)
			{
				$val=-$val;
				ans=ans+"<tr><td>Days <span style='color:#FF0000;font-size:x-large'><strong>Over</strong></span>: </td><td><span style='color:#FF0000;font-size:x-large'>"+$val+"</span></td></tr>";
			}
			else
			{
				ans=ans+"<tr><td>Days Left: </td><td>"+$val+"</td></tr>";
			}
			ans=ans+"<tr><td>Days Posted: </td><td>"+group_datepos[i]+"</td></tr>";
			ans=ans+"<tr><td>Posted By: </td><td>"+group_teacher[i]+"</td></tr>";
			ans=ans+"<tr><td>Group Affected: </td><td>"+group_grp[i]+"</td></tr></table>";
			ans=ans+"<span id='type_span'></span></td></tr></table></form>";
			//ans=ans+"Group Members: <br/>";
			selid=group_id[i];
		}
	}
	document.getElementById("group_info").innerHTML=ans;
	$("#fetch").text("Fetching Data...");
	$("#grp_id").val(selid);
	$("#edit_span").html("<input class='btn btn-primary' type='submit' value='Confirm Changes'/>");
	runajax(selid);
}
function runajax(group_id)
{
	//alert(group_id);
	$.ajax({
		type:"POST",
		url:"getmembers.php",
		data: {group:group_id}
	}).done(function(result){
		//alert("Done");
		//alert(result);
		var result=result.split(",");
		//alert(result.length);
		var err=result[0];
		err=parseInt(err);
		var msg="We have encountered "+err+" errors\n";
		for (i=1; i<=err; i++)
		{
			msg=msg+result[i]+"\n";
		}
		msg=msg+"Please inform sys_admin";
		if (err!=0)
		{
			alert(msg);
		}
		var ans="<table class='table table-bordered'><tr><td>Group Members</td></tr>";
		//alert(err+1);
		//alert(result.length);
		//alert(result[err+1]);
		for (i=0; i<result[err+1]; i++)
		{
			ans=ans+"<tr><td>"+result[err+2+i]+"<a href='#'><img src='minus_sign.png' alt='remove' onclick=\"removeguy('"+result[err+2+i]+"')\" style='float:right' width='16' height='16'/></a></td></tr>";
		}
		ans=ans+"<tr><td><input type='text' id='member_add'/><a href='#'><img src='plus_sign.png' alt='remove' onclick='addguy()' style='float:right' width='32' height='32'/></a>";
		ans=ans+"</table>";
		//$("#group_info").append(ans);
		$("#fetch").html(ans);
	});
}
function eval_type()
{
	var type=$("#type_opt").val();
	//Operate on type_span to change to please specify on others
	//alert(type);
	if (type=="Others")
	{
		//alert("Success");
		$("#type_span").html("<br/>Please Specify: <input name='specify_type' type='text' id='type_specify'/>");
	}
	else
	{
		$("#type_span").html("");
	}
}
function addguy()
{
	var guy=$("#member_add").val();
	alert("Adding: "+guy);
}
function removeguy(guy)
{
	alert("Removing: "+guy);
}
</script>
</body>
</html>