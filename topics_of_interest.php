<html>
<head>
<title> Topics Selection </title>
</head>
<body>
<form method = "post">
  <input type="checkbox" name="concepts[]" value="Constructors">Constructors<br>
  <input type="checkbox" name="concepts[]" value="Integer_and_Float">Integer_and_Float<br>
  <input type="checkbox" name="concepts[]" value="Relational_Operators">Relational_Operators<br>
  <input type="checkbox" name="concepts[]" value="Vectors_and_Arrays">Vectors_and_Arrays<br>
  <input type="checkbox" name="concepts[]" value="Input_and_output">Input_and_output<br>
  <input type="submit" value="Submit" name="submit">
</form>
<?php
session_start();
include 'databaseconnect.php';
echo '<p>'.$_SESSION['username'].'</p>';
$interests = "";
$username = $_SESSION['username'];

if(isset($_POST["submit"])){
	if(!empty($_POST["concepts"])){
		echo '<h3>You have selected the following: </h3><br>';
		foreach($_POST["concepts"] as $concepts){
			$interests .= $concepts.",";
			//echo '<p>'.$lan.'</p>';
		}
		$interests = rtrim($interests,',');
		echo '<p>'.$interests.'</p>';
		updateinterest($username, $interests);
		initialsetup($username);
		header("Location: dashboard.php");
	}
}

function updateinterest($username, $interests){
	$q2 = "UPDATE `users` SET `Interest` = '$interests' WHERE `username` = '$username'";
	mysql_query($q2);
	
}
function initialsetup($username){
    /*We need all the Category, QuestionID and Difficulty from "questions" table. We need Interest from "users" table.*/
	$q = "SELECT `Category`, `Question`, `Difficulty` FROM `questions`";
	$result = mysql_query($q);
	
	while($r = mysql_fetch_array($result)){
		/*echo $r['Category'];
		echo " ".$r['ID'];
		echo " ".$r['Difficulty'];
		echo "<br>";*/
		$cat = $r['Category'];
		$id = $r['Question'];
		$diff = $r['Difficulty'];
		mysql_query("INSERT INTO `$username` (`Category`, `Question`, `Appeared_in_this_test`, `Interest`, `Total_Attempts`, `Total_Correct`,`Difficulty`) VALUES ('$cat', '$id', '0', '0', '0', '0', '$diff')");
		
	}
	
	$q1 = "SELECT `Interest` FROM `users` WHERE `Username` LIKE '$username' ";
	$result1 = mysql_query($q1);
	$temp = mysql_fetch_array($result1);

	$interest = $temp['Interest'];
	echo $interest;
	$interestarray = explode(",", $interest);
	$len = count($interestarray);
	$q3 = "UPDATE `$username` SET `Interest` = 1 WHERE `Category` IN (";
	for($i=0;$i<$len;$i++){
		if($i<$len-1){
			$q3.="'$interestarray[$i]',";
		}
		else{
			$q3.="'$interestarray[$i]')";
		}
	}
	echo $q3;
	mysql_query($q3);
}
?>
</body>
</html>