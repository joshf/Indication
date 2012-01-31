<?php
require("login.php");
?>
<!-- SHTracker, Copyright Josh Fradley 2012 -->
<html> 
<head>
<title>SHTracker: Add A Download</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body>
<h1>SHTracker: Add A Download</h1>
<form action="actions/add.php" method="post">
<p>Name: <input type="text" size="50" name="name" /></p>
<p>ID: <input type="text" size="50" name="id" /></p>
<p>URL: <input type="text" size="50" name="url" /></p>
<p>Count: <input type="text" size="50" name="count" /></p>
<input type="submit" name="command" value="Add" />
</form>
<hr />
<p><a href="../admin">Go Back</a></p>
</body>
</html>