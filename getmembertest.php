<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Get Member Test</title>
</head>

<body>
<script src="js/jquery.js"></script>
Member Test<br/>
<input type='text' id='abc' name='group'/><br/>
<input type='submit' value='Test' onclick='runajax()'/><br />
Results
<div id='res'></div>
<script>
function runajax()
{
	var group_id=$("#abc").val();
	//alert(group_id);
	$.ajax({
		type:"POST",
		url:"getmembers.php",
		data: {group:group_id}
	}).done(function(result){
		$("#res").text(result);
	});
}
</script>
</body>

</html>