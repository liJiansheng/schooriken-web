<?php 
require_once("session.php");
require_once("events.php");
require_once("common.php");
$sess->authPage(2);
if (isset($_POST['submit'])) {
	if (events::validateEvents($_POST)) {
		$assess_id = events::addEvents($_POST);
		successMessage("Assessment added", 'viewAssess.php?assess_id='.$assess_id);
		die(0);
	}
}
$assess = $_POST;
//$subject['category'] = implode(",", $subject['category']);

require_once("top.php");
?>
<div class="container"><div class="row"><div class="col-md-3">
<?php echo events::navBar($mydata['userID'],'Assessment'); ?></div>
<div class="col-md-8"><h2>Add Assessment</h2>
<form class='form-horizontal' method='post'><fieldset>
	<div class='form-group'>
		<label class="col-md-3" for="title">Assessment Name:</label>
		<input type="text" id="title" name="title" class="form-control"  maxlength="150" value="<?php  echo htmlentities($assess['title']); ?>">
	</div>

	   <div class='form-group'>
      <label class='col-md-3' rel='tooltip'>Date of Assessment:</label>
         <div id="datetimepicker" class="input-append">
    <input id="evtDate" name="evtDate" data-format="dd-MM-yyyy" type="text" value="<?php  echo htmlentities($assess['evtDate']); ?>"></input>
    <span class="add-on">
      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
      </i>
    </span>
  </div>
	</div>
	 <div class='form-group'>
		<label class='col-md-3' rel='tooltip'>Groups:</label>
            <select name='group_id' value="<?php if ($isView) echo htmlentities($assess['group_id']);?>" class='col-md-3'>
			<?php
			$grpList = events::getGroupList($mydata['schoolID']);
				foreach($grpList as $k => $v) {
				echo "<option value=".$v['group_id'].">".$v['group_name']."</option>";			
				}?>
		</select>
	</div>
     
	
	<div class='form-group'>
		<label class="col-md-3" for="description">Description:</label>
			<textarea id="description" name="description" class="form-control" rows="5" maxlength="400" placeholder="Assessment Description">
<?php if ($isView) echo htmlentities($assess['description']); ?>
</textarea>
	</div>
    <div class='form-group'>
		<button type="submit" class="btn btn-primary" name="submit">Add Assessment</button>
		<button type="reset" class="btn clear">Clear</button>
	</div>
 <input type="hidden" id="type" name="type" class="col-md-8" placeholder="" value="Assignment">	
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
require_once("newtail.php");
?>
