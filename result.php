<?php
session_start();
include 'databaseconnect.php';
$username = $_SESSION['username'];
$numerator=mysql_fetch_array(mysql_query("SELECT COUNT(`Total_Correct`) AS `Correct`, `Category` FROM '$username' WHERE `Total_Correct`='1' GROUP BY `Category`"));
$denominator=mysql_fetch_array(mysql_query("SELECT COUNT(`Total_Attempts`) AS `Attempts`, `Category` FROM '$username' GROUP BY `Category`"));
header("Location: dashboard.php");
?>