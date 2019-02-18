<?php 
require_once("session.php");
require_once("groups.php");
require_once("common.php");
$sess->authPage(2);
if (isset($_POST['submit'])) {
	if (groups::validateGroup($_POST)) {
		$group_id = groups::addGroup($_POST);
		successMessage("Group added", 'viewGroup.php?group_id='.$group_id);
		die(0);
	}
}
$group= $_POST;
//$subject['category'] = implode(",", $subject['category']);

require_once("top.php");
?>
<div class="container"><div class="row"><div class="col-md-3">
<?php echo groups::navBar($mydata['userID']); ?></div>
<div class="col-md-8"><h2>Add Group</h2>
<form class='form-horizontal' method='post'><fieldset>
	<div class='form-group'>
		<label class="col-md-3" for="group_name">Group Name:</label>
		<input type="text" id="group_name" name="group_name" class="form-control" value="<?php echo htmlentities($group['group_name']); ?>">
	</div>
	
 <div class='form-group'>
		<label class='col-md-3' rel='tooltip'>Group Type:</label>
            <select name='type' value="<?php echo htmlentities($group['type']);?>" class='col-md-3'>
			<option value='Class'>Class</option>
			<option value='CCA'>CCA</option>
			<option value='Others'>Others</option>
		</select>
	</div>
	
	<div class='form-group'>
		<label class="col-md-3" for="description">Description:</label>
			<textarea id="description" name="description" class="form-control" rows="5" maxlength="400" placeholder="Group Description">
<?php echo htmlentities($events['description']); ?>
</textarea>
	</div>
	 <input type="hidden" id="userID" name="userID" class="col-md-8" placeholder="" value="<?php echo htmlentities($mydata['userID']);?>">
    <input type="hidden" id="schoolID" name="schoolID" class="col-md-8" placeholder="" value="<?php echo htmlentities($mydata['schoolID']); ?>">

    <div class='form-group'>
		<button type="submit" class="btn btn-primary" name="submit">Add Group</button>
		<button type="reset" class="btn clear">Clear</button>
	</div>
 
	
</fieldset>
</form>
</div></div></div>
<?php
require_once("newtail.php");
?>
