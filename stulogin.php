<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>LOGIN</title>

</head>

<body>

<h1>Login Page</h1>
<form id="login" name="login" method="post" action="api.php">
  <p>
    <label for="username">Username</label>
    <input type="text" name="user" id="user" />
  </p>
  <p>
    <label for="password">Password</label>
    <input name="pass" id="pass" type="password"/>
  </p>
   <p>
    <label for="query">Query</label>
    <input name="query" id="query" type="text"/>
  </p>
  <p>
    <input type="submit" name="submit" id="submit" value="Submit" />
  </p>
</form>
</body>
</html>