<?php 
require_once("session.php");
require_once("events.php");
require_once("common.php");
require_once("users.php");
require_once("groups.php");
$sess->authPage(2);

$announceID = intval($_GET['announce_id']);
$isImg = events::getEventPhoto($announceID);
$ann = events::getEvents($announceID,$isImg);
$ann['evtDate'] = niceTime($ann['evtDate']);

//$subjdata = subjects::getStuSubjInfo($user['classid']);
require_once("top.php");
?>
<div class="container"><div class="row"><div class="col-md-3">
<?php 
echo events::navBar($mydata['userID'],'Announcement'); 
if($mydata['role']==1) echo events::EventsNavBar($announceID, $mydata['userID'],'Announcement');?></div>
<div class="col-md-8"><h2>View Announcement Details</h2>
<div class="gap"></div>
	<div class="row">
			<div class="col-md-6">
            <?php
			if($isImg){?>
			<img src="<?php echo htmlentities($ann['image']);?>"></img>
			<?php }
			?>
            </div>
        </div>
         <div class="gap"></div>
		<div class="row">	
			<div class="col-md-6">
				<dl class='dl-horizontal'>
				<dt>Announcement Name:</dt>
				<dd><?php echo htmlentities($ann['title']); ?></dd>
                <dt>Announcement Group:</dt>
				<dd><?php echo htmlentities(groups::getGroupName($ann['group_id'])); ?></dd>
				  <dt>Announcement Date:</dt>
				<dd><?php echo htmlentities($ann['evtDate']); ?></dd>
			<div class="gap"></div>
			<p><?php echo nl2br(htmlentities($ann['description'])); 
			
			if (stripos($ann['description'], "") !== false) {
			?>
			<div onClick="$(this).css('width', parseInt($(this).css('width'))+10);" style="background-color: #FF0000; width: 20px; height: 20px; -webkit-border-bottom-right-radius: 100px; -webkit-border-top-right-radius: 100px;-webkit-transition-duration: 0.1s; -moz-transition-duration: 0.1s; -o-transition-duration: 0.1s; transition-duration: 0.1s;"></div>
			<?php
			}
			?></p>
			</div>
		
<?php
require_once("bottom.php");
?>
