<?php 
require_once("session.php");
require_once("riken.php");
require_once("common.php");

$sess->authPage(2);
if (isset($_POST['submit'])) {
	if (riken::validateRiken($_POST)) {
		$riken_id = riken::addRiken($_POST);
		if ($_FILES['picture']['name'] != "")
		{
			if (riken::validateImage($riken_id, $_FILES)) {
				$isUpload = riken::attachPhoto($riken_id, $_FILES['picture']);
			}
		}
		successMessage("New Riken Added!", 'viewRiken.php?riken_id='.$riken_id);
		die(0);
	}
}
$riken = $_POST;
//$subject['category'] = implode(",", $subject['category']);
require_once("top.php");
?>
<div class="container"><div class="row"><div class="col-md-3">
<?php echo riken::navBar($mydata['userID']); ?></div><div class="col-md-1"></div>
<div class="col-md-8"><h2>Add Riken</h2>
<form name = "addriken" class='form-horizontal' enctype="multipart/form-data" method='post'><fieldset>
	<div class='form-group'>
		<label class="col-md-3" for="title">Title:</label>
		<input type="text" id="title" name="title" class="form-control" value="<?php echo htmlentities($riken['title']); ?>">
	</div>
	 <div class='form-group'>
		<label class='col-md-3' rel='tooltip'>Groups</label>
            <select name='group_id' value="<?php echo htmlentities($riken['group_id']);?>" class='col-md-3'>
			<?php
			$grpList = riken::getGroupList();
				foreach($grpList as $k => $v) {
				echo "<option value=".$v['group_id'].">".$v['group_name']."</option>";			
				}?>
		</select>
	</div>
      <div class='form-group'>
		<label class='col-md-3' rel='tooltip'>Riken Type</label>
            <select name='type' value="<?php echo htmlentities($riken['type']);?>" class='col-md-3'>
			<option value='Assessment'>Assessment</option>
				<option value='Event'>Event</option>
					<option value='Announcement'>Announcement</option>
		</select>
	</div>
      <div class='form-group'>
      <label class='col-md-3' rel='tooltip'>Date of Riken</label>
         <div id="datetimepicker" class="input-append">
    <input id="evtDate" name="evtDate" data-format="dd-MM-yyyy" type="text" value="<?php echo htmlentities($riken['evtDate']); ?>"></input>
    <span class="add-on">
      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
      </i>
    </span>
  </div>
	</div>
	
	<div class='form-group'>
		<label class="col-md-3" for="description">Description:</label>
			<textarea id="description" name="description" class="form-control" rows="15" placeholder="riken Description">
<?php echo htmlentities($riken['description']); ?>
</textarea>
	</div>
	  <div class='form-group'> 
	   <label class='col-md-3' rel='tooltip'>Add Event Image:</label></br> 
            <p><input type="file" size="32" name="picture" value="" id="picture" /></p>
            <p class="button"><input type="hidden" name="action" value="img" /><input type='hidden' name='MAX_FILE_SIZE' value='100000'>          
        <div id="dnd_result"></div>
    </div>
    <div class='form-group'>
		<button type="submit" class="btn btn-primary" name="submit">Add Riken</button>
		<button type="reset" class="btn clear">Clear</button>
	</div>
	 <input type="hidden" id="userID" name="userID" class="col-md-8" placeholder="" value="<?php echo htmlentities($mydata['userID']);?>">
    <input type="hidden" id="schoolID" name="schoolID" class="col-md-8" placeholder="" value="<?php echo htmlentities($mydata['schoolID']); ?>">

	
</fieldset>
</form>
</div></div></div>
<script type="text/javascript">
  $(function() {
    $('#datetimepicker').datetimepicker({
      pickTime: false
    });
  });
</script>
<?php
require_once("bottom.php");
?>
