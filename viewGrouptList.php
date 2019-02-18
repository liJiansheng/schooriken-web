<?php
require_once("session.php");
require_once("groups.php");
require_once("common.php");
$sess->authPage(1);
//Get role
$groupID = intval($_GET['groupID']);
$user = Users::getUser($userID);
$grpList = group::getGroupList($mydata['userID'],$mydata['schoolID']);
require_once("top.php");
?>
<div class="container"><div class="row"><div class="col-md-3">
<?php echo groups::navBar($mydata['userID']); ?></div><div class="col-md-1"></div>
<div class="col-md-8"><h2>Group List</h2>
<hr>
<table class="table table-bordered">
	<colgroup>
	</colgroup>
	<thead>
		<tr>
			<th>Name</th>
			<th>Group Type</th>			
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($grpList as $k => $v) { ?>
		<tr>		
			<td style='text-align:center;'><?php echo htmlentities($v['group_name']); ?></td>
			<td style='text-align:center;'><?php echo htmlentities($v['type']); ?></td>
			<td>
				<center>
					<?php echo "<a class='' href='viewGroup.php?group_id=$v[group_id]'>View</a>"; ?>
				</center>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>


</div></div></div>
 <?php require_once("newtail.php");?>