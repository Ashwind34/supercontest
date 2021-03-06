<?php 

session_start();

require_once('sessioncheck.php');
require_once('pdo_connect.php');
require_once('pinupdate.php');
require_once('sendMessage.php');

if(!adminCheck()) {
	header("Location: ./login.php");
}

?>


<!DOCTYPE HTML>
<html>
	<head>
		<title>
			Update PINs
		</title>
        <meta name="viewport" content="width=device-width">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0" />
	    <link rel="stylesheet" type="text/css" href="../css/style.css">
		
	</head>

<?php

//check if $_POST is empty

if (!empty($_POST["select"])) {

    $emailstoreset = $_POST["select"];

    //loop through each email address and generate new PIN in DB, then mail PIN to email address
 
    foreach ($emailstoreset as $key=>$email) {
        list($recipientEmail, $recipiantName, $subject, $body) = PinUpdate($email);
        send_email_message($recipientEmail, $recipiantName, $subject, $body);
    }
    echo        '<div class="pickselect">New PINs have been sent!
                <br><br><a href="./admin.php">Return to Admin Page</a></div>
                <audio src="../css/audio/extrapoint.mp3" id="page_audio"></audio>
                <script src="../audio.js"></script>';
    exit();
}

?>
    <body>
        <div class="wrapper">
            <div class="messageContainer">
                <div class="redHead">
					PLAYER PIN RESET
				</div>

                <!-- Select multiple emails for manual user PIN updates -->

                <form action="pinresetall.php" method="POST">
                    <select class="allselect" multiple size="20" name="select[]">
                
                    <?php 
                    $email_query = $conn->prepare("SELECT email FROM player_roster ORDER BY email ASC");
                    $email_query->execute();                    
                    $email_list = $email_query->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach($email_list as $k=>$v) {                
                        echo '<option value='. $v['email'] . '>' . $v['email'] . '</option>';                 
                    }
                            
                    ?>  

                    </select>
                    <input type="submit" value="Submit">
                    <br>
                    <p><a href="./admin.php">Return to Admin Page</a></p>
                </form>
            </div>
        </div>
    </body>
</html>