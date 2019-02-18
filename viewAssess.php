<?php 
require_once("session.php");
require_once("events.php");
require_once("common.php");
require_once("groups.php");
$sess->authPage(2);

$assessID = intval($_GET['assess_id']);
$ass = events::getEvents($assessID,false);
//$subjdata = subjects::getStuSubjInfo($user['classid']);
require_once("top.php");
?>
<div class="container"><div class="row"><div class="col-md-3">
<?php 
echo events::navBar($mydata['userID'],'Assessment'); 
if($mydata['role']==1) echo events::EventsNavBar($assessID, $mydata['userID'],'Assessment');?></div>
<div class="col-md-8"><h2>View Assessment Details</h2>
<div class="gap"></div>
		<div class="row">		
			<div class="col-md-6">
				<dl class='dl-horizontal'>			
				<dt>Assessment Name:</dt>
				<dd><?php echo htmlentities($ass['title']); ?></dd>
                <dt>Group:</dt>
				<dd><?php echo htmlentities(groups::getGroupName($ass['group_id'])); ?></dd>
				  <dt>Date of Assessment:</dt>
				<dd><?php echo htmlentities(niceTime($ass['evtDate'])); ?></dd>
			<div class="gap"></div>
			<p><?php echo nl2br(htmlentities($ass['description'])); 
			
			if (stripos($ass['description'], "") !== false) {
			?>
			<div onClick="$(this).css('width', parseInt($(this).css('width'))+10);" style="background-color: #FF0000; width: 20px; height: 20px; -webkit-border-bottom-right-radius: 100px; -webkit-border-top-right-radius: 100px;-webkit-transition-duration: 0.1s; -moz-transition-duration: 0.1s; -o-transition-duration: 0.1s; transition-duration: 0.1s;"></div>
			<?php
			}
			?></p>
			</div>
        </div>
    </div>
</div>
</div>
<?php
require_once("newtail.php");
?>
