<?php 
require_once("session.php");
require_once("events.php");
require_once("users.php");
require_once("common.php");
require_once("groups.php");
$sess->authPage(2);

$eventID = intval($_GET['event_id']);
$isImg = events::getEventPhoto($eventID);
$evt = events::getEvents($eventID,$isImg);
$evt['evtDate'] = niceTime($evt['evtDate']);
$studList = Users::getEventStudent($eventID);
//$subjdata = subjects::getStuSubjInfo($user['classid']);
require_once("top.php");
?>
<div class="container"><div class="row"><div class="col-md-3">
<?php 
echo events::navBar($mydata['userID'],'Event'); 
if($mydata['role']==1) echo events::EventsNavBar($eventID, $mydata['userID'],'Event');?></div>
<div class="col-md-8"><h2>View Event Details</h2>
<div class="gap"></div>
		<div class="row">
			<div class="col-md-6">
            <?php
			if($isImg){?>
			<img src="<?php echo htmlentities($evt['image']);?>"></img>
			<?php }
			?>
            </div>
        </div>
     <div class="gap"></div>
        <div class="row">
			<div class="col-md-6">
				<dl class='dl-horizontal'>			
				<dt>Event Name:</dt>
				<dd><?php echo htmlentities($evt['title']); ?></dd>
                <dt>Group:</dt>
				<dd><?php echo htmlentities(groups::getGroupName($evt['group_id'])); ?></dd>
				  <dt>Date of Event:</dt>
				<dd><?php echo htmlentities($evt['evtDate']); ?></dd>
			<div class="gap"></div>
			<p><?php echo nl2br(htmlentities($evt['description'])); 
			
			if (stripos($evt['description'], "") !== false) {
			?>
			<div onClick="$(this).css('width', parseInt($(this).css('width'))+10);" style="background-color: #FF0000; width: 20px; height: 20px; -webkit-border-bottom-right-radius: 100px; -webkit-border-top-right-radius: 100px;-webkit-transition-duration: 0.1s; -moz-transition-duration: 0.1s; -o-transition-duration: 0.1s; transition-duration: 0.1s;"></div>
			<?php
			}
			?></p>
			</div>
		<table class="table table-bordered">
	<colgroup>
	</colgroup>
    <?php
		if($studList!=null){ ?>
	<thead>
		<tr>
			<th>Name</th>
			<th>Class</th>
            <th>Email</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($studList as $k => $v) { ?>
		<tr>		
			<td style='text-align:center;'><?php echo htmlentities($v['name']); ?></td>
            <td style='text-align:center;'><?php echo htmlentities($v['class']); ?></td>
			<td style='text-align:center;'><?php echo htmlentities($v['email']); ?></td>			
		</tr>
		<?php }} ?>
	</tbody>
</table>
</div> <!--/row-->
</div> <!--/col-md-8-->
</div> <!--/container?-->
</div> <!--No idea what this is anymore-->
<?php
require_once("newtail.php");
?>
