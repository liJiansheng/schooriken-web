<?php 
require_once("session.php");
require_once("riken.php");
require_once("common.php");
$sess->authPage(2);

$rikenID = intval($_GET['riken_id']);
$riken = riken::getRiken($rikenID);
$riken['evtDate'] = niceTime($riken['evtDate']);
//$subjdata = subjects::getStuSubjInfo($user['classid']);
require_once("top.php");
?>
<div class="container"><div class="row"><div class="col-md-3">
<?php 
echo riken::navBar($mydata['userID']); 
if($mydata['role']==1) echo riken::rikenNavBar($eventID, $mydata['userID']);?></div><div class="col-md-1"></div>
<div class="col-md-8"><h2>View Riken Details<small class='pull-right'><?php echo htmlentities($riken['title']); ?></small></h2>

		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-6">
				<dl class='dl-horizontal'>
				<dt>Riken ID:</dt>
				<dd><?php echo intval($riken['riken_id']); ?></dd>
				<dt>Riken Name:</dt>
				<dd><?php echo htmlentities($riken['title']); ?></dd>
                <dt>Group:</dt>
				<dd><?php echo htmlentities($riken['group_name']); ?></dd>
				  <dt>Date of Event:</dt>
				<dd><?php echo htmlentities($riken['evtDate']); ?></dd>
			
			<p><?php echo nl2br(htmlentities($riken['description'])); 
			
			if (stripos($evt['description'], "") !== false) {
			?>
			<div onClick="$(this).css('width', parseInt($(this).css('width'))+10);" style="background-color: #FF0000; width: 20px; height: 20px; -webkit-border-bottom-right-radius: 100px; -webkit-border-top-right-radius: 100px;-webkit-transition-duration: 0.1s; -moz-transition-duration: 0.1s; -o-transition-duration: 0.1s; transition-duration: 0.1s;"></div>
			<?php
			}
			?></p>
			</div>
		
<?php
require_once("bottom.php");
?>
