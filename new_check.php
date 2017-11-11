<?php
session_start();
include 'databaseconnect.php';
if(isset($_POST['submit'])){
	$username=@mysql_real_escape_string($_POST['username']);
	$password=@mysql_real_escape_string($_POST['password']);
	SignUp($username, $password);
	CreateTable($username, $password);
	header("Location: topics_of_interest.php");
}
function SignUp($username, $password){
	$act = date("Y-m-d H:i:s");
	$q="SELECT * FROM `users` WHERE `Username` = '$username' AND `Password` = '$password'";
	//echo $q;
	$result = mysql_query($q);
	if(mysql_num_rows($result)>=1){
		echo "Username or password already exists";
	}
	else{
	$q="INSERT INTO `users` (`Username`, `Status`, `Password`, `last_logged_in`, `User_Type`, `Interest`, `Strength`, `Weakness`, `Undefined`) VALUES ('$username', '0', '$password', '$act', '1', NULL, NULL, NULL, NULL)";
	mysql_query($q);
	$_SESSION['username'] = $username;
	echo "Welcome ". $username;
	}
}
function CreateTable($username, $password){
	$q = "CREATE TABLE IF NOT EXISTS `$username` (`Category` VARCHAR(500) NOT NULL, `Question` VARCHAR(10000) NOT NULL, `Appeared_in_this_test` INT(11), `Interest` INT(11), `Total_Attempts` INT(11), `Total_Correct` INT(11), `Difficulty` INT(11) NOT NULL)";
	mysql_query($q);
	
}
?>