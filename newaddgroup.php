<!DOCTYPE html>
	<head>
		<link rel='stylesheet' href='style.css' type='text/css' />
		<title>Add New Group</title>
	</head>
	<body>
    <?php require "newhead.php"; ?>
    <div class='content' align='center'>
		<h2 class='subtitle'>Add New Group</h2>
			<table>
				<form action='newgroup.php?verify=1' method='post'>
					<tr>
						<td>Type: </td>
						<td><select name='type'>
							<option value='Class'>Class</option>
							<option value='CCA'>CCA</option>
							<option value='Other'>Other</option>
                            </select>
						</td>
					</tr>
					<tr>
						<td>Group Name: </td>
						<td><input type='text' name='group_name' size='75'></td>
					</tr>
                    <tr>
						<td colspan='2' align='center'><input type='submit' value='Add'/></td>
					</tr>
                    <tr><td colspan='2' align='center'><p style='color:gray;'>Success!</p></td></tr>
                    </form>
			</table>
		</div>
        <?php require "newtail.php"; ?> 
    </body>
</html>