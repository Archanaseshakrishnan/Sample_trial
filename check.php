<?php
session_start();
include 'databaseconnect.php';
if(isset($_POST['submit'])){
	//echo $_POST['username'].'---------------'.$_POST['password'];
	$username=@mysql_real_escape_string($_POST['username']);
	$password=@mysql_real_escape_string($_POST['password']);
	echo "Welcome ";
	SignIn($username, $password);
}
function SignIn($username, $password){
	
	$q="SELECT * FROM `users` WHERE `Username` = '$username' AND `Password` = '$password'";
	
	$result = mysql_query($q);
	if(mysql_num_rows($result)>=1){
		$_SESSION['username'] = $username;
		UpdateDB($username, $password);
		header("Location: dashboard.php");
	}
	else{
		echo "Try again!";
	}
}
function UpdateDB($username, $password){
	$datetime = date("Y-m-d H:i:s");
	$q2 = "UPDATE `users` SET `last_logged_in` = '$datetime' WHERE `username` = '$username'";
	mysql_query($q2);
	$q2 = "UPDATE `users` SET `Status` = 1 WHERE `username` = '$username'";
	mysql_query($q2);
}
?>

