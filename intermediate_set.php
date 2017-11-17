<!DOCTYPE html>
<html>
<head>
<title>TestPage</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
<?php
	session_start();
	include 'databaseconnect.php';
	$username = $_SESSION['username'];
	echo "<p>I am in intermediate_set</p>";
	$level = 2;
	//check User_Type
	$q1 = "SELECT * FROM `users` WHERE `Username`='$username'";
	$result = mysql_fetch_array(mysql_query($q1));
	if($result['User_Type']==2){
		//okay
		//first select the seven questions from the table 
		$q2 = "SELECT `Difficulty`,COUNT(`Difficulty`) AS `counti` FROM '$username' GROUP BY `Difficulty` HAVING `Difficulty`='2'";
		$r2 = mysql_fetch_array(mysql_query($q2));
		$num_of_rows = $r2["counti"];
		if($num_of_rows >= 9){
			//okay first 7 questions
			$query = "SELECT * FROM '$username' WHERE `Difficulty`='2'";
			$res1 = mysql_query($query);
			$interestindex=0;
			$interest=array();//interest and no correct - priority1
			$no_interestindex=0;
			$no_interest=array();//no interest and no correct - priority2
			$others_index=0;
			$others = array();//correct but not appeared in this test - priority3
			$questions=array();
			$index=0;//index for the final array
			while($row=mysql_fetch_array($res1)){
				if($row["Appeared_in_this_test"]==0){
					//total is greater than 10 condition checked initially
					if($row["Interest"]==1){
						if($row["Total_Correct"]==0){
							$interest[$interestindex]=$row["Question"];
							$interestindex+=1;
						}else{
							$others[$others_index]=$row["Question"];
							$others_index+=1;
						}
					}
					else{
						if($row["Total_Correct"]==0){
							$no_interest[$no_interestindex]=$row["Question"];
							$no_interestindex+=1;
						}else{
							$others[$others_index]=$row["Question"];
							$others_index+=1;
						}
					}
					if(count($interest)+count($no_interest) >= 9){
						//fine just randomize the questions and ask
						for($i=0;$i<9;$i+=1){
							$index = mt_rand()%9;
							//resolving collision
							if(array_key_exists($index,$question)){
								while(array_key_exists($index,$question))
									$index = ($index+1)%9;
								//check incase $interest runs out
								if($i<count($interest)){
									$question[$index]=$interest[$i];
								}
								else{
									//if interest section is over remaining from no_interest
									$question[$index]=$no_interest[$i-count($interest)];
								}
							}
							else{
								if($i<count($interest)){
									$question[$index]=$interest[$i];
								}
								else{
									$question[$index]=$no_interest[$i-count($interest)];
								}
							}
						}
						display_question($question, $username);//function to select the question from the table, display it, record the response, update the user table and the Questions table namely appeared_in_this_test, total correct, total attempts
						//by the end of it change the strengths and weaknesses and undefined categories

					}
					else{
						//check if the total count is between 4 to 8
							//if so repeat the restore else call the intermediate function
						if(count($interest)+count($no_interest) >=4 && count($interest)+count($no_interest)<9){
							for($i=0;$i<count($interest);$i+=1){
								$index = mt_rand()%count($interest);
								if(array_key_exists($index,$question)){
									while(array_key_exists($index,$question))
										$index = ($index+1)%count($interest);
									$question[$index]=$interest[$i];
								}
								else{
									$question[$index]=$no_interest[$i];
								}
							}
							for($i=0;$i<count($no_interest);$i+=1){
								$index = mt_rand()%count($no_interest);
								if(array_key_exists($index,$question)){
								while(array_key_exists($index,$question))
									$index = ($index+1)%count($no_interest);
								$question[$index]=$no_interest[$i];
								}
								else{
									$question[$index]=$no_interest[$i];
								}
							}
							for($j=0;$j<9;$j+=1){
								if(array_key_exists($j,$question)){
									while(array_key_exists($j,$question))
										$j=($j+1)%10;
									$question[$j]=$others[$j%10];
								}
								else{
									$question[$j]=$others[$j%10];
								}
							}
							display_question($question, $username);//function to select the question from the table, display it, record the response, update the user table and the Questions table namely appeared_in_this_test, total correct, total attempts
						}else{
							//update User_type and call intermediate function
							$q3 = "UPDATE `users` SET `User_Type` = '3' WHERE `Username`='$username'";
						}
					}
				}
			}
		}
		else{
			//questions difficulty level is upgraded
			//no change in User_Type
			$result2 = mysql_query("SELECT * FROM '$username' WHERE `Difficulty`='2'");
			while($row = mysql_fetch_array($result2)){
				$diff = $row['Difficulty'];
				$diff+=1;
				mysql_query("UPDATE '$username' SET `Difficulty`='$diff' WHERE `Question`='$row['Question']'");
			}
			include 'advanced_set.php';
		}
	}
	else{
		//means not enough questions in the previous level
		//very simple - no loss of touch: <50% User_Type =1; >= 50% User_Type = 2
		//this differs in advanced_set
		$query = "SELECT `Difficulty`,COUNT(`Difficulty`) AS `counti` FROM '$username' GROUP BY `Difficulty` HAVING `Difficulty`='2' ";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		//echo "<p>".$row["Difficulty"]." ".$row["counti"]."</p>";
		$num_of_rows=$row["counti"];
		if($num_of_rows>=10){
			//means there are atleast 10 questions in total in this category
			$q1 = "SELECT * FROM '$username' WHERE `Difficulty`='2'";
			$res1 = mysql_query($query);
			$interestindex=0;
			$interest=array();//interest and no correct - priority1
			$no_interestindex=0;
			$no_interest=array();//no interest and no correct - priority2
			$others_index=0;
			$others=array();//correct but not appeared in this test - priority3
			$questions=array();
			$index=0;//index for the final array
			while($row=mysql_fetch_array($res1)){
				if($row["Appeared_in_this_test"]==0){
					//total is greater than 10 condition checked initially
					if($row["Interest"]==1){
						if($row["Total_Correct"]==0){
							$interest[$interestindex]=$row["Question"];
							$interestindex+=1;
						}else{
							$others[$others_index]=$row["Question"];
							$others_index+=1;
						}
					}
					else{
						if($row["Total_Correct"]==0){
							$no_interest[$no_interestindex]=$row["Question"];
							$no_interestindex+=1;
						}else{
							$others[$others_index]=$row["Question"];
							$others_index+=1;
						}
					}
					if(count($interest)+count($no_interest) >= 10){
						//fine just randomize the questions and ask
						for($i=0;$i<10;$i+=1){
							$index = mt_rand()%10;
							//resolving collision
							if(array_key_exists($index,$question)){
								while(array_key_exists($index,$question))
									$index = ($index+1)%10;
								//check incase $interest runs out
								if($i<count($interest)){
									$question[$index]=$interest[$i];
								}
								else{
									//if interest section is over remaining from no_interest
									$question[$index]=$no_interest[$i-count($interest)];
								}
							}
							else{
								if($i<count($interest)){
									$question[$index]=$interest[$i];
								}
								else{
									$question[$index]=$no_interest[$i-count($interest)];
								}
							}
						}
						
						
						display_question($question, $username);//function to select the question from the table, display it, record the response, update the user table and the Questions table namely appeared_in_this_test, total correct, total attempts
						//by the end of it change the strengths and weaknesses and undefined categories
						//recompute User_Type
			$qscore = "SELECT * FROM '$username' WHERE `Difficulty`='2'";
			$resu = mysql_query($qscore);
			$total_num_of_ques = mysql_num_rows($resu);
			$qscore = "SELECT * FROM '$username' WHERE `Difficulty`='2' AND `Total_Correct`='1'";
			$resu2 = mysql_query($qscore);
			$correct = mysql_num_rows($resu2);
			$score = (floatval)$correct/$total_num_of_ques;
			if($score > 0.5){
				//update User_Type
				$qw = "UPDATE `users` SET `User_Type` = '2' WHERE `Username`='$username'";
			}
					}
					else{
						//check if the total count is between 4 to 9
							//if so repeat the restore else call the intermediate function
						if(count($interest)+count($no_interest) >=4 && count($interest)+count($no_interest)<10){
							for($i=0;$i<count($interest);$i+=1){
								$index = mt_rand()%count($interest);
								if(array_key_exists($index,$question)){
								while(array_key_exists($index,$question))
									$index = ($index+1)%count($interest);
								$question[$index]=$interest[$i];
								}
								else{
									$question[$index]=$no_interest[$i];
								}
							}
							for($i=0;$i<count($no_interest);$i+=1){
								$index = mt_rand()%count($no_interest);
								if(array_key_exists($index,$question)){
								while(array_key_exists($index,$question))
									$index = ($index+1)%count($no_interest);
								$question[$index]=$no_interest[$i];
								}
								else{
									$question[$index]=$no_interest[$i];
								}
							}
							for($j=0;$j<10;$j+=1){
								if(array_key_exists($j,$question)){
									while(array_key_exists($j,$question))
										$j=($j+1)%10;
									$question[$j]=$others[$j%10];
								}
								else{
									$question[$j]=$others[$j%10];
								}
							}
							display_question($question, $username,10);//function to select the question from the table, display it, record the response, update the user table and the Questions table namely appeared_in_this_test, total correct, total attempts
							//recompute User_Type
							$qscore = "SELECT * FROM '$username' WHERE `Difficulty`='2'";
							$resu = mysql_query($qscore);
							$total_num_of_ques = mysql_num_rows($resu);
							$qscore = "SELECT * FROM '$username' WHERE `Difficulty`='2' AND `Total_Correct`='1'";
							$resu2 = mysql_query($qscore);
							$correct = mysql_num_rows($resu2);
							$score = (floatval)$correct/$total_num_of_ques;
							if($score > 0.5){
								//update User_Type
								$qw = "UPDATE `users` SET `User_Type` = '2' WHERE `Username`='$username'";
							}
						}else{
							//update User_type and call intermediate function
							$q3 = "UPDATE `users` SET `User_Type` = '3' WHERE `Username`='$username'";
						}
					}
				}
			}
			
		}
		else{
			//less than 10 questions in this category
			//here the User_Type is not changed
			$result2 = mysql_query("SELECT * FROM '$username' WHERE `Difficulty`='2'");
			while($row = mysql_fetch_array($result2)){
				$diff = $row['Difficulty'];
				$diff+=1;
				mysql_query("UPDATE '$username' SET `Difficulty`='$diff' WHERE `Question`='$row['Question']'");
			}
			include 'advance_set.php';
		}
	}
	
	function display_question($question, $username, $bound){?>
		<div class="container question">
		<p>	Responsive Quiz Application Using PHP, MySQL, jQuery, Ajax and Twitter Bootstrap</p>
		<form class="form-horizontal" role="form" id='login' method="post" >
		<?php
			$i=0;
			for($i=0;$i<$bound;$i+=1){
				if($i<$bound-1){
					$q5 = "SELECT * FROM `questions` WHERE `Question` = '$question[$i]'";
					$result = mysql_fetch_array(mysql_query($q5));
					?>
					<div id='question<?php echo $i;?>' class='cont'>
					<p class='questions' id="qname<?php echo $i;?>"><?php echo $i+1?>.<?php echo $result['Question'];?></p>
					<input type="radio" value="1" id='radio1_<?php echo $i+1;?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option1'];?><br/>
					<input type="radio" value="2" id='radio2_<?php echo $i+1;?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option2'];?><br/>
					<input type="radio" value="3" id='radio3_<?php echo $i+1;?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option3'];?><br/>
					<input type="radio" value="4" id='radio4_<?php echo $i+1;?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option4'];?><br/>
					
					<center><button id='next<?php echo $i;?>' class='next' type='button'>Next</button><br>
					</div>
				<?php}
				else if($i==$bound){
					$q5 = "SELECT * FROM `questions` WHERE `Question` = '$question[$i]'";
					$result = mysql_fetch_array(mysql_query($q5));
					?>
					<div id='question<?php echo $i;?>' class='cont'>
					<p class='questions' id="qname<?php echo $i;?>"><?php echo $i+1?>.<?php echo $result['Question'];?></p>
					<input type="radio" value="1" id='radio1_<?php echo $i+1;?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option1'];?><br/>
					<input type="radio" value="2" id='radio2_<?php echo $i+1;?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option2'];?><br/>
					<input type="radio" value="3" id='radio3_<?php echo $i+1;?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option3'];?><br/>
					<input type="radio" value="4" id='radio4_<?php echo $i+1;?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option4'];?><br/>
					
					<center><button id='next<?php echo $i;?>' class='next' type='submit' name='first_seven'>Submit</button><br>
					</div>
				<?php}
			}
		?>
<script>
		$('.cont').hide();
		count=$('.questions').length;
		 $('#question'+1).show();

		 $(document).on('click','.next',function(){
		     element=$(this).attr('id');
		     last = parseInt(element.substr(element.length - 1));
		     nex=last+1;
		     $('#question'+last).hide();

		     $('#question'+nex).show();
		 });

</script>
		
<?php
		if(isset($_POST['first_seven'])){ 
		    $keys=array_keys($_POST);
		    $order=join(",",$keys);
			$response=mysql_query("select `Question`,`Answer` from `questions` where `Question` IN('$order') ORDER BY FIELD(`Question`,'$order')")   or die(mysql_error());	
			while($result=mysql_fetch_array($response)){
				$thisques = $result["Question"];
				$q7 = "SELECT `Total_Attempts`, `Total_Correct` FROM '$username' WHERE `Question`='$thisques'";
				$q7result = mysql_fetch_array(mysql_query($q7));
				$attempts = $q7result["Total_Attempts"];
				$attempts+=1;
				$correct = $q7result["Total_Correct"];
				$q8 = "SELECT `Number_of_attempts`, `Number_correct` FROM `questions` WHERE `Question`='$thisques'";
				$q8result = mysql_fetch_array(mysql_query($q8));
				$totalattempts = $q8result["Number_of_attempts"]; 
				$totalattempts+=1;
				$totalcorrect = $q8result['Number_correct'];
				$q6 = "UPDATE '$username' SET `Appeared_in_this_test`='1', `Total_Attempts`='$attempts' WHERE `Question`='$thisques'";
				mysql_query($q6);
				$q9 = "UPDATE `questions` SET `Number_of_attempts`='$totalattempts' WHERE `Question`='$thisques'";
				mysql_query($q9);
			    if($result["Answer"]==$_POST[$result["Question"]]){
					//modify the questions appeared in this test to 1, total_attempts+1, total_correct+1 in both the tables
					$correct+=1;
					$totalcorrect+=1;
					$q10 = "UPDATE '$username' SET `Total_Correct`='$correct' WHERE `Question`='$thisques'";
					mysql_query($q10);
					$q11 = "UPDATE `questions` SET `Number_correct`='$totalcorrect' WHERE `Question`='$thisques'";
					mysql_query($q11);
				}
		    }
			restore_table($question, $username);//function to recompute the difficulty level of the questions in questions table as well as user table and clean the Appeared_in_this_test value
		}
		//this situation will not arise
		$qeasy = "SELECT * FROM '$username' WHERE `Difficulty`='1' AND `Total_Correct`='0' AND `Appeared_in_this_test`='0'"; 
		$reasy1 = mysql_fetch_array(mysql_query($qeasy));
		if(count($reasy1) >= 1){
			$index = mt_rand()%count($reasy1);
			$ques = mysql_fetch_array(mysql_query("SELECT * FROM `questions` WHERE `Question` = '$reasy1[$index]'"));
		}
		else{
			//this is the only branch that is always executed
			$qeasy = "SELECT * FROM '$username' WHERE `Difficulty`='1' AND `Appeared_in_this_test`='0'"; 
			$reasy1 = mysql_fetch_array(mysql_query($qeasy));
			if(count($reasy1) >= 1){
			$index = mt_rand()%count($reasy1);
			$ques = mysql_fetch_array(mysql_query("SELECT * FROM `questions` WHERE `Question` = '$reasy1[$index]'"));
			}
		}
			?>
			<div id='question<?php echo "7";?>' class='cont'>
			<p class='questions' id="qname<?php echo "8";?>"><?php echo "8"?>.<?php echo $ques['Question'];?></p>
			<input type="radio" value="1" id='radio1_<?php echo "8";?>' name='<?php echo $ques['Question'];?>'/><?php echo $ques['Option1'];?><br/>
			<input type="radio" value="2" id='radio2_<?php echo "8";?>' name='<?php echo $ques['Question'];?>'/><?php echo $ques['Option2'];?><br/>
			<input type="radio" value="3" id='radio3_<?php echo "8";?>' name='<?php echo $ques['Question'];?>'/><?php echo $ques['Option3'];?><br/>
			<input type="radio" value="4" id='radio4_<?php echo "8";?>' name='<?php echo $ques['Question'];?>'/><?php echo $ques['Option4'];?><br/>
			
			<center><button id='next<?php echo "7";?>' class='next' type='submit' name='eighth'>Submit</button><br>
			</div>
			<?php
		//check the answer
		$nextmodule = "";
		if(isset($_POST['eighth'])){ 
		    $keys=array_keys($_POST);
		    $order=join(",",$keys);
			$response=mysql_query("select `Question`,`Answer` from `questions` where `Question` IN('$order') ORDER BY FIELD(`Question`,'$order')")   or die(mysql_error());	
			while($result=mysql_fetch_array($response)){
				$thisques = $result["Question"];
				$q7 = "SELECT `Total_Attempts`, `Total_Correct` FROM '$username' WHERE `Question`='$thisques'";
				$q7result = mysql_fetch_array(mysql_query($q7));
				$attempts = $q7result["Total_Attempts"];
				$attempts+=1;
				$correct = $q7result["Total_Correct"];
				$q8 = "SELECT `Number_of_attempts`, `Number_correct` FROM `questions` WHERE `Question`='$thisques'";
				$q8result = mysql_fetch_array(mysql_query($q8));
				$totalattempts = $q8result["Number_of_attempts"]; 
				$totalattempts+=1;
				$totalcorrect = $q8result['Number_correct'];
				$q6 = "UPDATE '$username' SET `Appeared_in_this_test`='1', `Total_Attempts`='$attempts' WHERE `Question`='$thisques'";
				mysql_query($q6);
				$q9 = "UPDATE `questions` SET `Number_of_attempts`='$totalattempts' WHERE `Question`='$thisques'";
				mysql_query($q9);
			    if($result["Answer"]==$_POST[$result["Question"]]){
					//modify the questions appeared in this test to 1, total_attempts+1, total_correct+1 in both the tables
					$correct+=1;
					$totalcorrect+=1;
					$q10 = "UPDATE '$username' SET `Total_Correct`='$correct' WHERE `Question`='$thisques'";
					mysql_query($q6);
					$q11 = "UPDATE `questions` SET `Number_correct`='$totalcorrect' WHERE `Question`='$thisques'";
					mysql_query($q9);
					$nextmodule = "intermediate_nine";
				}
				else{
					$nextmodule = "easy_nine";
				}
		    }
			restore_table(mysql_fetch_array($response), $username);//function to recompute the difficulty level of the questions in questions table as well as user table and clean the Appeared_in_this_test value
			$nextmodule($username, $question);// to be checked
		}
?>

<?php
	else{
			header( "Location: dashboard.php" ) ;

		}
	}
	function restore_table($question, $username){
		//iterate through all the questions and then recompute the difficulty
		//if there is a change then update the user table
		foreach($question as $q){
			$q12 = "SELECT * FROM `questions` WHERE `Question`='$q'";
			$result = mysql_fetch_array(mysql_query($q12));
			$attempt = $result['Number_of_attempts'];
			$correct = $result['Number_correct'];
			$score = $correct/$attempt;
			$thisques = $result['Question'];
			if($attempt>10){
				if($score<0.2){
						//update difficulty to hard
						mysql_query("UPDATE `questions` SET `Difficulty`='3' WHERE `Question`='$thisques'");
						mysql_query("UPDATE '$username' SET `Difficulty`='3', `Appeared_in_this_test`='0' WHERE `Question`='$thisques");
				}else if($score>=0.2 && $score<=0.4){
						//update difficulty to moderate
						mysql_query("UPDATE `questions` SET `Difficulty`='2' WHERE `Question`='$thisques'");
						mysql_query("UPDATE '$username' SET `Difficulty`='2', `Appeared_in_this_test`='0' WHERE `Question`='$thisques'");
				}else{
					//do nothing
				}
			}
		}
	}
	function intermediate_nine($username, $question){
		//just display the 7th and 8th index of the array question
		for($i=7;$i<9;$i+=1){
				if($i==7){
					$q5 = "SELECT * FROM `questions` WHERE `Question` = '$question[$i]'";
					$result = mysql_fetch_array(mysql_query($q5));
					?>
					<div id='question<?php echo "8";?>' class='cont'>
					<p class='questions' id="qname<?php echo "9";?>"><?php echo "9"?>.<?php echo $result['Question'];?></p>
					<input type="radio" value="1" id='radio1_<?php echo "9";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option1'];?><br/>
					<input type="radio" value="2" id='radio2_<?php echo "9";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option2'];?><br/>
					<input type="radio" value="3" id='radio3_<?php echo "9";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option3'];?><br/>
					<input type="radio" value="4" id='radio4_<?php echo "9";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option4'];?><br/>
					
					<center><button id='next<?php echo "8";?>' class='next' type='button'>Next</button><br>
					</div>
				<?php}
				else if($i==8){
					$q5 = "SELECT * FROM `questions` WHERE `Question` = '$question[$i]'";
					$result = mysql_fetch_array(mysql_query($q5));
					?>
					<div id='question<?php echo "9";?>' class='cont'>
					<p class='questions' id="qname<?php echo "10";?>"><?php echo "10"?>.<?php echo $result['Question'];?></p>
					<input type="radio" value="1" id='radio1_<?php echo "10";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option1'];?><br/>
					<input type="radio" value="2" id='radio2_<?php echo "10";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option2'];?><br/>
					<input type="radio" value="3" id='radio3_<?php echo "10";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option3'];?><br/>
					<input type="radio" value="4" id='radio4_<?php echo "10";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option4'];?><br/>
					
					<center><button id='next<?php echo "9";?>' class='next' type='submit' name='last'>Submit</button><br>
					</div>
				<?php}
			}
		if(isset($_POST['last'])){ 
		    $keys=array_keys($_POST);
		    $order=join(",",$keys);
			$response=mysql_query("select `Question`,`Answer` from `questions` where `Question` IN('$order') ORDER BY FIELD(`Question`,'$order')")   or die(mysql_error());	
			while($result=mysql_fetch_array($response)){
				$thisques = $result["Question"];
				$q7 = "SELECT `Total_Attempts`, `Total_Correct` FROM '$username' WHERE `Question`='$thisques'";
				$q7result = mysql_fetch_array(mysql_query($q7));
				$attempts = $q7result["Total_Attempts"];
				$attempts+=1;
				$correct = $q7result["Total_Correct"];
				$q8 = "SELECT `Number_of_attempts`, `Number_correct` FROM `questions` WHERE `Question`='$thisques'";
				$q8result = mysql_fetch_array(mysql_query($q8));
				$totalattempts = $q8result["Number_of_attempts"]; 
				$totalattempts+=1;
				$totalcorrect = $q8result["Number_correct"];
				$q6 = "UPDATE '$username' SET `Appeared_in_this_test`='1', `Total_Attempts`='$attempts' WHERE `Question`='$thisques'";
				mysql_query($q6);
				$q9 = "UPDATE `questions` SET `Number_of_attempts`='$totalattempts' WHERE `Question`='$thisques'";
				mysql_query($q9);
			    if($result["Answer"]==$_POST[$result["Question"]]){
					//modify the questions appeared in this test to 1, total_attempts+1, total_correct+1 in both the tables
					$correct+=1;
					$totalcorrect+=1;
					$q10 = "UPDATE '$username' SET `Total_Correct`='$correct' WHERE `Question`='$thisques'";
					mysql_query($q10);
					$q11 = "UPDATE `questions` SET `Number_correct`='$totalcorrect' WHERE `Question`='$thisques'";
					mysql_query($q11);
					//$nextmodule = "intermediate_nine";
				}
		    }
			restore_table($question, $username);//function to recompute the difficulty level of the questions in questions table as well as user table and clean the Appeared_in_this_test value
			header( "Location: result.php" ) ;
		}
	}
	function easy_nine($username, $question){
		//just display the 7th and 8th index of the array question
		$qtemp = "SELECT * FROM '$username' WHERE `Difficulty`='1' AND `Appeared_in_this_test`='0'";
		$resultqtemp = mysql_fetch_array(mysql_query($qtemp));
		$in = mt_rand()%count($resultqtemp);
		$result = $resultqtemp[$in];
		?>
		<div id='question<?php echo "8";?>' class='cont'>
		<p class='questions' id="qname<?php echo "9";?>"><?php echo "9"?>.<?php echo $result['Question'];?></p>
		<input type="radio" value="1" id='radio1_<?php echo "9";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option1'];?><br/>
		<input type="radio" value="2" id='radio2_<?php echo "9";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option2'];?><br/>
		<input type="radio" value="3" id='radio3_<?php echo "9";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option3'];?><br/>
		<input type="radio" value="4" id='radio4_<?php echo "9";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option4'];?><br/>
		
		<center><button id='next<?php echo "8";?>' class='next' type='button'>Next</button><br>
		</div>
		<?php
		
		$in1 = mt_rand()%count($resultqtemp);
		if($in1 == $in)
			$in1+=1;
		$result = $resultqtemp[$in];
		?>
		<div id='question<?php echo "9";?>' class='cont'>
		<p class='questions' id="qname<?php echo "10";?>"><?php echo "10"?>.<?php echo $result['Question'];?></p>
		<input type="radio" value="1" id='radio1_<?php echo "10";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option1'];?><br/>
		<input type="radio" value="2" id='radio2_<?php echo "10";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option2'];?><br/>
		<input type="radio" value="3" id='radio3_<?php echo "10";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option3'];?><br/>
		<input type="radio" value="4" id='radio4_<?php echo "10";?>' name='<?php echo $result['Question'];?>'/><?php echo $result['Option4'];?><br/>
		
		<center><button id='next<?php echo "9";?>' class='next' type='submit' name='last'>Submit</button><br>
		</div>
		<?php
		}
		if(isset($_POST['last'])){ 
		    $keys=array_keys($_POST);
		    $order=join(",",$keys);
			$number = 0;
			$response=mysql_query("select `Question`,`Answer` from `questions` where `Question` IN('$order') ORDER BY FIELD(`Question`,'$order')")   or die(mysql_error());	
			while($result=mysql_fetch_array($response)){
				$thisques = $result["Question"];
				$q7 = "SELECT `Total_Attempts`, `Total_Correct` FROM '$username' WHERE `Question`='$thisques'";
				$q7result = mysql_fetch_array(mysql_query($q7));
				$attempts = $q7result["Total_Attempts"];
				$attempts+=1;
				$correct = $q7result["Total_Correct"];
				$q8 = "SELECT `Number_of_attempts`, `Number_correct` FROM `questions` WHERE `Question`='$thisques'";
				$q8result = mysql_fetch_array(mysql_query($q8));
				$totalattempts = $q8result["Number_of_attempts"]; 
				$totalattempts+=1;
				$totalcorrect = $q8result['Number_correct'];
				$q6 = "UPDATE '$username' SET `Appeared_in_this_test`='1', `Total_Attempts`='$attempts' WHERE `Question`='$thisques'";
				mysql_query($q6);
				$q9 = "UPDATE `questions` SET `Number_of_attempts`='$totalattempts' WHERE `Question`='$thisques'";
				mysql_query($q9);
			    if($result["Answer"]==$_POST[$result["Question"]]){
					//modify the questions appeared in this test to 1, total_attempts+1, total_correct+1 in both the tables
					$correct+=1;
					$totalcorrect+=1;
					$q10 = "UPDATE '$username' SET `Total_Correct`='$correct' WHERE `Question`='$thisques'";
					mysql_query($q10);
					$q11 = "UPDATE `questions` SET `Number_correct`='$totalcorrect' WHERE `Question`='$thisques'";
					mysql_query($q11);
					$number+=1;
					//$nextmodule = "intermediate_nine";
				}
		    }
			restore_table($question, $username);//function to recompute the difficulty level of the questions in questions table as well as user table and clean the Appeared_in_this_test value
			if(number>=2){
				//2/3 correct so no change
			}
			else{
				//lower the user_type
				$q12 = "UPDATE `users` SET `User_Type`='1' WHERE `Username`='$username'";
				mysql_query($q12);
			}
			header( "Location: result.php" ) ;
		}
	}
	
	?>
</form>
<script>
		$('.cont').hide();
		count=$('.questions').length;
		 $('#question'+1).show();

		 $(document).on('click','.next',function(){
		     element=$(this).attr('id');
		     last = parseInt(element.substr(element.length - 1));
		     nex=last+1;
		     $('#question'+last).hide();

		     $('#question'+nex).show();
		 });

</script>

</body>
</html>