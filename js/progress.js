
$(document).ready(function(){
var randIDS = '<?php echo $uiq?>'; 
// Add Hidden Field
var hidden = $("<input>");
hidden.attr({
name:"APC_UPLOAD_PROGRESS",
id:"progress_key",
type:"hidden",
value:randIDS
});
$("#uploadImage").prepend(hidden); 
$("#uploadImage").submit(function(e){

var p = $(this);
p.attr('target','tmpForm');
  
// creating iframe
if($("#tmpForm").length == 0){
var frame = $("<iframe>");
frame.attr({
name:'tmpForm',
id:'tmpForm',
action:'about:blank',
border:0
}).css('display','none');
p.after(frame);
}  
// Start file upload  
$.get("get_progress.php", {progress_key:randIDS, rand:Math.random()},
function(data){ 
var uploaded = parseInt(data);
if(uploaded == 100){
$(".progress, .badge").hide();
clearInterval(loadLoader);
}
else if(uploaded < 100)
{
$(".progress, .badge").show();
$(".badge").text(uploaded+"%");
var cWidth = $(".bar").css('width', uploaded+"%");           
}
if(uploaded < 100)
var loader = setInterval(loadLoader,2000);
});
  
var loadLoader = function(){
$.get("get_progress.php", {progress_key:randIDS, rand:Math.random()}, function(data)
{
var uploaded = parseInt(data);
if(uploaded == 100){
$(".progress, .badge").hide();
parent.location.href="index.php?success";
}
else if(uploaded < 100)
{
$(".progress, .badge").show();
$(".badge").text(uploaded+"%");
var cWidth = $(".bar").css('width', uploaded+"%");           
}
});
}
});});
