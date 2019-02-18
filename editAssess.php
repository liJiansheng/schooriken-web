<?php 
require_once("session.php");
require_once("events.php");
require_once("common.php");
$sess->authPage(2);

if (!isset($_GET['assess_id']) || !Events::EventsExists($_GET['assess_id'])) {
	errorMessage('No assessment can be found with the provided ID.');
}
$assessID = intval($_GET['assess_id']);

//$isImage = events::getEventPhoto($eventID);
$ass= events::getEvents($assessID, false);

$ass['evtDate'] = niceTime($ass['evtDate']);

if (isset($_POST['submit'])) {
	if (events::validateEvents($_POST)) {
		events::editEvents($_POST, $assessID);
		successMessage("Assessment edited", 'viewAssess.php?assess_id='.$assessID);
		die(0);
	}
}else if(isset($_POST['delete'])) {
		events::deleteEvents($assessID);
		successMessage("Assessment has been deleted.", 'viewAssessList.php');
	}

//$subject['category'] = implode(",", $subject['category']);

require_once("top.php");
?>
<div class="container"><div class="row"><div class="col-md-3">
<?php echo events::navBar($mydata['userID'],'Assessment'); ?></div>
<div class="col-md-8"><h2>Edit Assessment</h2>

<form class='form-horizontal' enctype="multipart/form-data" method='post'><fieldset>

	<div class='form-group'>
		<label class="col-md-3" for="title">Assessment Name:</label>
		<input type="text" id="title" name="title" class="form-control" maxlength="150" value="<?php echo htmlentities($ass['title']); ?>">
	</div>
	 <div class='form-group'>
      <label class='col-md-3' rel='tooltip'>Date of Assessment:</label>
           <div id="datetimepicker" class="input-append">
    <input id="evtDate" name="evtDate" data-format="dd-MM-yyyy" type="text" value="<?php echo htmlentities($ass['evtDate']); ?>"></input>
    <span class="add-on">
      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
      </i>
    </span>
  </div>
	</div>
	 <div class='form-group'>
		<label class='col-md-3' rel='tooltip'>Groups:</label>
            <select name='group_id' value="<?php echo htmlentities($ass['group_id']);?>" class='col-md-3'>
			<?php
			$grpList = events::getGroupList($mydata['schoolID']);
			$grp = htmlentities($ass['group_id']);
				foreach($grpList as $k => $v) {
				if($v['group_id'] ==$grp){
				echo "<option value=".$v['group_id']." selected='selected'>".$v['group_name']."</option>";	
				}else{
				echo "<option value=".$v['group_id'].">".$v['group_name']."</option>";	
				}		
				}?>
		</select>
	</div>
     
	<div class='form-group'>
		<label class="col-md-3" for="description">Description:</label>
			<textarea id="description" name="description" class="form-control" rows="5" maxlength="400" placeholder="Event Description">
<?php echo htmlentities($ass['description']); ?>
</textarea>
	</div>
    <div class='form-group'>
		<button type="submit" class="btn btn-primary" name="submit">Edit Assessment</button>
		<button type="submit" class="btn btn-primary" name="delete">Delete Assessment</button>
	</div>
 <input type="hidden" id="type" name="type" class="col-md-8" placeholder="" value="Assignment">	
	 <input type="hidden" id="userID" name="userID" class="col-md-8" placeholder="" value="<?php echo htmlentities($mydata['userID']);?>">
    <input type="hidden" id="schoolID" name="schoolID" class="col-md-8" placeholder="" value="<?php echo htmlentities($mydata['schoolID']); ?>">
     <input type="hidden" id="type" name="type" class="col-md-8" placeholder="" value="Assignment">

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
