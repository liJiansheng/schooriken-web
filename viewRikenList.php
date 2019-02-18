<?php
require_once("session.php");
require_once("riken.php");
require_once("common.php");
$sess->authPage(1);
require_once("top.php");
//Get role
$rikensID = intval($_GET['riken_id']);
$user = Users::getUser($userID);
$evtList = riken::getRikenList($mydata['userID']);

?>
<div class="container"><div class="row"><div class="col-md-3">
<?php echo riken::navBar($mydata['userID']); ?></div><div class="col-md-1"></div>
<div class="col-md-8"><h2>Rikens List</h2>
<hr>
<table class="table table-bordered">
	<colgroup>
	</colgroup>
	<thead>
		<tr>
			<th>Name</th>
			<th>Group</th>
			<th>Date</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($evtList as $k => $v) { ?>
		<tr>
			<td><?php echo htmlentities($v['title']); ?></td>
			<td style='text-align:center;'><?php echo htmlentities($v['group_name']); ?></td>
			<td style='text-align:center;'><?php echo htmlentities(niceTime($v['evtDate'])); ?></td>
			<td>
				<center>
					<?php echo "<a class='' href='viewRiken.php?riken_id=$v[riken_id]'>View</a>"; ?>
				</center>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>


</div></div></div>
 <?php require_once("newtail.php");?>