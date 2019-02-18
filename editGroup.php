<?php 
require_once("session.php");
require_once("groups.php");
require_once("common.php");
$sess->authPage(2);

if (!isset($_GET['group_id']) || !groups::groupExists($_GET['group_id'])) {
	errorMessage('No group can be found with the provided ID.');
}
$groupID = intval($_GET['group_id']);

//$isImage = Groups::getEventPhoto($eventID);
$grp = groups::getGroup($groupID);

//$grp['evtDate'] = niceTime($grp['evtDate']);

if (isset($_POST['submit'])) {
    $_POST['group_id'] = $groupID;
	if (groups::validateGroup($_POST)) {
		groups::editGroup($_POST, $groupID);
		successMessage("Group edited", 'viewGroup.php?group_id='.$groupID);
		die(0);
	}
}else if(isset($_POST['delete'])) {
		groups::deleteGroup($groupID);
		successMessage("Group has been deleted.", 'viewGroupList.php');
	}
//$grp = $_POST;
//$subject['category'] = implode(",", $subject['category']);

require_once("top.php");

?>
<div class="container"><div class="row"><div class="col-md-3">
<?php echo Groups::navBar($mydata['userID']); ?></div>
<div class="col-md-8"><h2>Edit Group</h2>

<form class='form-horizontal' enctype="multipart/form-data" method='post'><fieldset>

	<div class='form-group'>
		<label class="col-md-3" for="group_name">Group Name:</label>
		<input type="text" id="group_name" name="group_name" maxlength="150" class="form-control" value="<?php echo htmlentities($grp['group_name']); ?>">
	</div>

 <div class='form-group'>
		<label class='col-md-3' rel='tooltip'>Group Type:</label>
            <select name='type' value="<?php echo htmlentities($grp['type']);?>" class='col-md-3'>
			<option value='Class'>Class</option>
			<option value='CCA'>CCA</option>
			<option value='others'>Others</option>
		</select>
	</div>
	<div class='form-group'>
		<label class="col-md-3" for="description">Description:</label>
			<textarea id="description" name="description" class="form-control" rows="5" maxlength="400" placeholder="Group Description">
<?php echo htmlentities($grp['description']); ?>
</textarea>
	</div>
	 <input type="hidden" id="userID" name="userID" class="col-md-8" placeholder="" value="<?php echo htmlentities($mydata['userID']);?>">
    <input type="hidden" id="schoolID" name="schoolID" class="col-md-8" placeholder="" value="<?php echo htmlentities($mydata['schoolID']); ?>">

    <div class='form-group'>
		<button type="submit" class="btn btn-primary" name="submit">Edit Group</button>
		<button type="submit" class="btn btn-primary" name="delete">Delete Group</button>
	</div>
</fieldset>
</form>
</div></div></div>

<?php

require_once("bottom.php");
?>
