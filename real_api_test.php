<html>
	<body>
		<form method="post" action="http://schooriken.riicc.sg/api.php">
			User: <input type="text" name="user" value="drcoconut"/><br/>
			Pass: <input type="text" name="pass" value="pass123"/><br/>
			Query: <input type="text" name="query" value="trueflagarr"/><br/>
			EventIdArr: <input type="text" name="eventidarr" value="[5,2]"/><br/>
			<input type="submit" value="Test"/>
		</form>
		<div>
			<?php
				$a=json_decode("[5,2]");
				var_dump($a);
			?>
		</div>
	</body>
</html>