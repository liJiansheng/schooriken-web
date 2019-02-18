<?php 
require_once("session.php");
require_once("groups.php");
require_once("common.php");
$sess->authPage(2);

$groupID = intval($_GET['group_id']);
$grp = groups::getGroup($groupID);
//$subjdata = subjects::getStuSubjInfo($user['classid']);
require_once("top.php");
?>
<div class="container"><div class="row"><div class="col-md-3">
<?php 
echo groups::navBar($mydata['userID'],false); 
if($mydata['role']==1) echo groups::groupsNavBar($groupID, $mydata['userID']);?></div>
<div class="col-md-8"><h2>View Group Details</h2>
		<div class="gap"></div>
		<div class="row">			
			<div class="col-md-6">
				<dl class='dl-horizontal'>
				<dt>Group Name:</dt>
				<dd><?php echo htmlentities($grp['group_name']); ?></dd>
                <dt>Group Type:</dt>
				<dd><?php echo htmlentities($grp['type']); ?></dd>
                <div class="gap"></div>
				<p><?php echo nl2br(htmlentities($grp['description'])); 
			
			if (stripos($grp['description'], "") !== false) {
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
