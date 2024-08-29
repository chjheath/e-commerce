<?php

    session_start();

	include "header.php";

    require_once 'js/validation.php';

    if (!isset($_SESSION['uid'])) { //if user not logged in anymore
        session_destroy();
        Header ("Location:login.php"); //send user to logout automatically
    }

?>

<!--
	Name: Christian Heatherly
	File name: changePwd.php
	Date created: 11/08/22
	Date last modified: 11/08/22

	Sources:
    -->

<?php

    //variables to keep track of states, errors, etc.

    $passError = '';
    $confPassError = '';
    $currPassError = '';

    $currPassOK = FALSE;
    $passOK = FALSE;

    $currPass = '';
    $pass = '';
    $confPass = '';

    if (isset($_POST['enter'])) { //form submitted

	if (isset($_POST['currPass']) && $_POST['currPass'] !== 'Type here...') {
                $currPass = sqlReplace(trim($_POST['currPass']));

                $stmt = $con->prepare("SELECT COUNT(*) as num FROM USERS WHERE UserID = ? AND Password = ?");
                if ($stmt->execute(array($_SESSION['uid'], $currPass)) && isset($_POST['pass']) && isset($_POST['confPass'])) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $count = $row['num'];

                    if ($count < 1) {
                        $passOK = FALSE;
                        $currPassError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>Your current password is incorrect.</p>";
                    } else {
                        if (validateQuery(sqlReplace(trim($_POST['pass'])))) {
                        
                            $pass = sqlReplace(trim($_POST['pass']));
                            $confPass = sqlReplace(trim($_POST['confPass']));
    
                            if ($pass != $confPass || ($pass == "" || $confPass == "")) {
                                $passOK = FALSE;
                                
                                //return to form
                                $confPassError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-213px;'>Your passwords do not match!</p>";
                    
                            } else if ($pass === $confPass) {
                                $passOK = TRUE;
                            }
                        } else {
                            $passOK = FALSE;
                            $passError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>Your password does not meet specifications (Must be >= 10 characters, must contain both letters & digits)</p>";
                        }
                    }
                }
            }

            if ($passOK) {
                //Update user info

                $stmt = $con->prepare("UPDATE USERS SET Password = ? WHERE UserID = ?");
                $stmt->execute(array($pass, $_SESSION['uid']));

                Header("Location:userInfo.php?source=upd");
            }


    }

?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Change your password</title>
        <link rel="stylesheet" href="css/style.css" type="text/css">
        <link rel="stylesheet" type="text/css" href="css/mobile.css">
        <script src="js/mobile.js" type="text/javascript"></script>
    </head>

    <body>
            <div id="body">
                <!-- Top of Page (Not including nav) -->
                <div class="header">
                    <div class="contact">
                        <div class="detailBox">
                            <h1 style="color:black">Enter your information below.</h1>
                            <!-- Code from Lab1 -->
                            <form id="changePwd" action="changePwd.php" method="post">

                                <!-- Current Password -->
                                <label for="currPass"><h3>Enter current password <?php print $currPassError?></h3></label>
                                <input type="password" name="currPass" value="Type here..." onclick="this.value='';">

                                <!-- Password -->
                                <label for="pass"><h3>Create new password <?php print $passError?></h3></label>
                                <input type="password" name="pass" value="Type here..." onclick="this.value='';">
                            
                                <!-- Confirm Password -->
                                <label for="confPass"><h3>Confirm new password <?php print $confPassError?></h3></label>
                                <input type="password" name="confPass" value="Type here..." onclick="this.value='';">

                                <input type="submit" name="enter" id="submit" value="Change" style="width:96%">
                                <br><br>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Middle of page -->

                <div class="body">

                </div>
            </div>

            <!-- Bottom of page -->

            <div id="footer">
                
            </div>
        </div>
    </body>
</html>