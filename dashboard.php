<?php 
session_start();
include 'databaseconnect.php';
?>
<html>
<head>
<title>dashboard</title>
<head>
<body>
	<?php
		echo '<h3>'."WELCOME ".$_SESSION['username'].'</h3>';
		$username = $_SESSION['username'];
		$query = "SELECT `Strength`, `Weakness`, `Undefined` FROM `users` WHERE `Username`='$username'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		if($row["Strength"] && $row["Weakness"] && $row["Undefined"])
			echo "[".$row["Strength"].",".$row["Weakness"].",".$row["Undefined"]."],";
		else
			echo '<p><center>'."Strength, weakness and undefined are null. New user!".'</center></p><br><br>';
		
	?>
<form action="test.php" method="post">
<input type="submit" value="Ready to take the test" name="submit"/>
</form>
</body>
</html>