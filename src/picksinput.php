<?php

session_start();

require_once('pdo_connect.php');
require_once('datecheck.php');
require_once('picks_query.php');
require_once('weekly_schedule.php');

if( isset($_SESSION['player_id'])) {
	
	//PDO prepared statement
	$record = $conn->prepare("SELECT player_id, first_name FROM player_roster WHERE player_id = :id"); 
	$record->bindParam(':id',$_SESSION['player_id']);
	$record->execute();
	
	//create associative array from query
	$result = $record->fetch(PDO::FETCH_ASSOC);
	
	
	//set $user as array that contains query data
	if (COUNT($result) > 0 ) {
	$user = $result;
	} else {
		die("No result returned");
	}
}


//skip sql query before data is entered

if (empty($_POST['submit'])) {
	} else {

		//make sure that all picks are filled
	
		if(!empty($_POST['pick_1']) && !empty($_POST['pick_2']) && !empty($_POST['pick_3']) && !empty($_POST['pick_4']) 
		&& !empty($_POST['pick_5'])) {
	
			
			//insert picks into picks log table in case there is a problem
			
			$submit = $conn->prepare("INSERT INTO picks_log (player_id, pick_1, pick_2, pick_3, pick_4, pick_5, week) 
									VALUES (:player_id, :pick_1, :pick_2, :pick_3, :pick_4, :pick_5, :weekmarker)");
									
									
			$submit->BindParam(':pick_1', $_POST['pick_1']);
			$submit->BindParam(':pick_2', $_POST['pick_2']);
			$submit->BindParam(':pick_3', $_POST['pick_3']);
			$submit->BindParam(':pick_4', $_POST['pick_4']);
			$submit->BindParam(':pick_5', $_POST['pick_5']);
			$submit->BindParam(':player_id', $_SESSION['player_id']);
			$submit->BindParam(':weekmarker', $weekmarker);
			
				//make sure statement executes correctly, then send to table with all player picks 
							
				if ($submit->execute()) {
					header("Location: weekly_picks_table.php");
				} else {
					echo "It seems like there was a problem submitting your picks.  Please try again.";
						
				}
				
				//updates player picks table to show current most recent picks
				//CREATE A FUNCTION FOR THESE UPDATE STATEMENTS, USE FOREACH LOOP TO PROCESS EACH PICK
				
			$pick_1_in = $_POST['pick_1'];
			$pick_2_in = $_POST['pick_2'];
			$pick_3_in = $_POST['pick_3'];
			$pick_4_in = $_POST['pick_4'];
			$pick_5_in = $_POST['pick_5'];
			$player_id_in = $_SESSION['player_id'];
						
			$submit_1 = "UPDATE player_picks 
						SET pick_1 = '$pick_1_in' 
						WHERE week = '$weekmarker'
						AND player_id = '$player_id_in'";
						
			$submit_2 = "UPDATE player_picks 
						SET pick_2 = '$pick_2_in' 
						WHERE week = '$weekmarker'
						AND player_id = '$player_id_in'";
						
			$submit_3 = "UPDATE player_picks 
						SET pick_3 = '$pick_3_in' 
						WHERE week = '$weekmarker'
						AND player_id = '$player_id_in'";
						
			$submit_4 = "UPDATE player_picks 
						SET pick_4 = '$pick_4_in' 
						WHERE week = '$weekmarker'
						AND player_id = '$player_id_in'";

			$submit_5 = "UPDATE player_picks 
						SET pick_5 = '$pick_5_in'
						WHERE week = '$weekmarker'
						AND player_id = '$player_id_in'";
						
					
					TRY {
					
					$conn->exec($submit_1);
					$conn->exec($submit_2);
					$conn->exec($submit_3);
					$conn->exec($submit_4);
					$conn->exec($submit_5);
										
					}
					CATCH (PDOException $e) {
					
					echo $e->getMessage();
					}
		
					echo '<meta HTTP-EQUIV="Refresh" Content="0; URL=player_picks_table.php">';
							
				header("Location: ./player_picks_table.php");
			
			
				
			
			} else {
				echo '<p style=text-align:center><b>Please select 5 teams!</b></p><br>';
				echo '<p style=text-align:center><a href="picksinput.php">Try Again</a></p><br>';
				echo '<p style=text-align:center><a href="index.php">Return to Home Page</a></p>';
				exit();
				
			
			}
	}
			
	
	
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<title>Week <?php echo $weekmarker;?> Picks</title>
<body>

<style>

h1 {
	text-align:center;
	color:red;
}

p {
	text-align:center;
	color:green;
}

</style>

<h1>Make your picks for Week <?php echo "$weekmarker, $user[first_name]";?>!</h1>

<h2 style=text-align:center; color:blue><i>Your Current Picks</i></h2>


<!-- $player_picks_table located in picks_query.php -->

<h2 style=text-align:center;><?php echo $player_picks_table;?></h2>

<!--dropdown menus for each pick, referenced from function in player_picks_query.php -->

<form action="picksinput.php" method="post">


	<p>Pick #1</p>
	
	<p><?php PickDropdown($pick_1,'pick_1'); ?></p>
	
	<p>Pick #2</p>

	<p><?php PickDropdown($pick_2, 'pick_2'); ?></p>
	
	<p>Pick #3</p>

	<p><?php PickDropdown($pick_3, 'pick_3'); ?></p>
		
	<p>Pick #4</p>

	<p><?php PickDropdown($pick_4, 'pick_4'); ?></p>
	
	<p>Pick #5</p>

	<p><?php PickDropdown($pick_5,'pick_5'); ?></p>

	
	<p><input type="submit" name="submit" value="Submit Your Picks"></p>
	</form>
	
	<br>
	<p style=text-align:center; color:blue;></p>
	<p style=text-align:center;><a href="../index.php">Return to Home Page</a></p><br>
	<h3 style=text-align:center; color:blue>Week <?php echo $weekmarker ;?> Lines</h3>
	
	<!-- display table with weekly lines from weekly_schedule.php -->
	
	<p style=text-align:center;><?php echo $weekly_lines_table?></p>

	</body>
	
	



</html>