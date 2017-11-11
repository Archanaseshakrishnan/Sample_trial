<html>
<head>
<title>TestPage</title>
</head>
<body>
	<?php
		session_start();
		include 'databaseconnect.php';
		$username = $_SESSION['username'];
		$query = "SELECT * FROM `users` WHERE `Username`='$username'";
		$result = mysql_query($query);
		$r = mysql_fetch_array($result);
		if($r['User_Type']=='1'){
			//novice
			novice_set($username);
		}
		else if($r['User_Type']=='2'){
			//intermediate
			intermediate_set($username);
		}
		else if($r['User_Type']=='3'){
			//advanced
			advanced_set($username);
		}
		else{
			//expert
			expert_set($username);
		}
		
		function novice_set($username){
			echo "<p>I am novice</p>";
			echo $username;
			$level=1;
			$query = "SELECT `Difficulty`,COUNT(`Difficulty`) AS `counti` FROM $username GROUP BY `Difficulty` HAVING `Difficulty`='1'";
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
			//echo "<p>".$row["Difficulty"]." ".$row["counti"]."</p>";
			$num_of_rows=$row["counti"];
			if($num_of_rows>=10){
			//means there are atleast 10 questions in total in this category
			$q1 = "SELECT * FROM $username WHERE DIFFICULTY='1'";
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
					if(count($interest)+count($no_interest) >= 10){
						//fine just randomize the questions and ask
						for($i=0;$i<10;$i+=1){
							$index = mt_rand()%10;
							if(array_key_exists($index,$question)){
								while(array_key_exists($index,$question))
									$index = ($index+1)%10;
								if($i<count($index)){
									$question[$index]=$interest[$i];
								}
								else{
									$question[$index]=$no_interest[$i-count($index)];
								}
							}
							else{
								if($i<count($index)){
									$question[$index]=$interest[$i];
								}
								else{
									$question[$index]=$no_interest[$i-count($index)];
								}
							}
						}
						display_question($question, $username);//function to select the question from the table, display it, record the response, update the user table and the Questions table namely appeared_in_this_test, total correct, total attempts
						//by the end of it display the strengths and weaknesses and undefined categories
						restore_table($question, $username);//function to recompute the difficulty level of the questions in questions table as well as user table and clean the Appeared_in_this_test value
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
										$j+=1;
									$question[$j]=$others[$j%10];
								}
								else{
									$question[$j]=$others[$j%10];
								}
							}
							display_question($question, $username);//function to select the question from the table, display it, record the response, update the user table and the Questions table namely appeared_in_this_test, total correct, total attempts
							restore_table($question, $username);//function to recompute the difficulty level of the questions in questions table as well as user table and clean the Appeared_in_this_test value
						}else{
							//update User_type and call intermediate function
							$q3 = "UPDATE `users` SET `User_Type` = '2' WHERE `Username`='$username'";
						}
					}
				}
			}
		}
	
		}
		
		function intermediate_set($username){
		}
		
		function advanced_set($username){
		}
		
		function expert_set($username){
		}
	?>
</body>
</html>