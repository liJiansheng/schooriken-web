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
            <li><a href="manageevent_edit.php">Edit Events</a></li>
            <li class="active"><a href="#">Add Events</a></li>
        </ul>
        <div id="data" style="margin-left:20px;">
           <div class="buffer-sm"> </div>
            <div class='content' align='center'>
                <form action='addgrp.php' method='post' onsubmit="return check_form()">
                <table>
                    
                    <tbody>
                        <tr>
                            <td>Group Name: </td>
                            <td><input type='text' name='group_name' size='75' id='group_name'></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><span id='nameerror'></span></td>
                        </tr>
                        <tr>
                            <td>Type: </td>
                            <td><select name='type' id='type_select' onchange='check_type()'>
                                <option value='Class'>Class</option>
                                <option value='CCA'>CCA</option>
                                <option value='Others'>Others</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                        	<td><span id='specify'></span></td>
                            <td><span id='pls_specify'></span></td>
                        </tr>
                        <tr>
                        	<td></td>
                            <td><span id='grpname_error' style='color:#FF0000;'></span></td>
                        </tr>
                        <tr>
                            <td colspan='2' align='center'><input type='submit' value='Add'/></td>
                        </tr>
                    </tbody>
                    
                </table>
                </form>
            </div>
        </div>
    </div>
    <?php require "newtail.php"; ?>
    <script>
	var group_names = new Array();
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
				echo "group_names.push(\"".$row[2]."\");\n";
				$i+=1;
			}
		?>
		//end populate
		//provide event listener
		$("#group_name").bind('input',function (){
			check_name();
		});
	var h=function(){
		check_grptype();
	};
	var others_spec=false;
	function check_name()
	{
		var name=document.getElementById("group_name").value;
		for (i=0; i<group_names.length; i++)
		{
			if (group_names[i].toLowerCase()==name.toLowerCase())
			{
				document.getElementById("nameerror").innerHTML="<span style='color:#FF0000;'>Name has been taken</span>";
				name_valid=false;
				return;
			}
		}
		document.getElementById("nameerror").innerHTML="<span style='color:#00FF00;'>Name is valid</span>";
		name_valid=true;
		if (name=="")
		{
			document.getElementById("nameerror").innerHTML="<span style='color:#FF0000;'>Can't have empty name</span>";
			name_valid=false;
		}
	}
	function check_type()
	{
		var type=$("#type_select").val();
		if (type=='Others')
		{
			$("#pls_specify").html("<input type='text' name='add_opt' id='add_opt'/>");
			$("#specify").html("Please Specify:");
			if (others_spec==false)
			{
				//bind event listener
				$("#add_opt").bind('input',h);
			}
			others_spec=true;
		}
		else
		{
			if (others_spec==true)
			{
				//unbind event listener
				$("#add_opt").unbind('input',h);
			}
			$("#pls_specify").html("");
			$("#specify").html("");
		}
	}
	function check_grptype()
	{
		if ($("#type_select").val()=="Others" && $("#add_opt").val()=="")
		{
			$("#grpname_error").html("Cannot be empty");
		}
		else
		{
			$("#grpname_error").html("");
		}
	}
	function check_form()
	{
		var success=true;
		var msg="";
		check_name();
		if (document.getElementById("group_name").value=="")
		{
			msg=msg+"Error: Name can't be empty\n";
			success=false;
		}
		else
		{
			if (!name_valid)
			{
				msg=msg+"Error: Name has been taken\n";
				success=false;
			}
		}
		if ($("#type_select").val()=="Others" && $("#add_opt").val()=="")
		{
			msg=msg+"Error: Group Type cannot be left empty\n";
			$("#grpname_error").html("Cannot be empty");
			success=false;
		}
		else
		{
			$("#grpname_error").html("");
		}
		if (!success)
		{
			alert(msg);
		}
		return success;
	}
	</script>
</body>
</html>