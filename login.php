<?php
    session_start();

    $_SESSION['timeout'] = time();

    if (isset($_SESSION['uid'])) { //if user already logged in
        Header ("Location:userInfo.php"); //send user to user page
    }

	include "header.php";
?>

<!--
	Name: Christian Heatherly
	File name: login.php
	Date created: 09/30/22
	Date last modified: 11/10/22

	Sources:
		ch_12_sessions
-->

<?php

    $loginEmail = "";
    $loginPass = "";

    $emailErrorMessage = "";
    $passErrorMessage = "";
    $loginErrorMessage = "";

    $enteredEmail = FALSE;
    $enteredPass = FALSE;

    if (isset($_POST['enter'])) {
        if (isset($_POST['loginEmail']) && $_POST['loginEmail'] != "") { //email not entered
            $loginEmail = trim($_POST['loginEmail']);
            $loginEmail = sqlReplace($loginEmail);
            $emailErrorMessage = "";
            $enteredEmail = TRUE;
            
        } else {
            $emailErrorMessage = "<p style='color:black; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter an e-mail!</p>";
        }
    
        if (isset($_POST['loginPass']) && $_POST['loginPass'] != "") { //pass not entered
            $passErrorMessage = "";
            $loginPass = trim($_POST['loginPass']);
            $loginPass = sqlReplace($loginPass);
            $enteredPass = TRUE;
        } else {
            $passErrorMessage = "<p style='color:black; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a password!</p>";
        }
    
        if ($enteredEmail && $enteredPass) {        
            $stmt = $con->prepare("SELECT count(*) as result FROM USERS WHERE Email = ? AND Password = ? AND Active = 'YES'");
            $stmt->execute(array($loginEmail, $loginPass));
            $row = $stmt->fetch(PDO::FETCH_OBJ);

            $count = $row->result;

            if ($count == 1) {
                $stmt = $con->prepare("SELECT UserID FROM USERS WHERE Email = ? AND Password = ? AND Active = 'YES'");
                $stmt->execute(array($loginEmail, $loginPass));

                $row = $stmt->fetch(PDO::FETCH_OBJ);

                $uid = $row->UserID;

                $_SESSION['uid'] = $uid;
                $_SESSION['email'] = $loginEmail;

                $stmt = $con->prepare("UPDATE USERS SET LastLogin = ? WHERE UserID = ? AND Active = 'YES'");
                $stmt->execute(array(time(), $uid));

                $stmt->closeCursor();

                Header("Location:userInfo.php");
            } else {
                echo '<p style="color: red; position: absolute; left: 730px; top: 365px;">Your information was incorrect. Please try again.</p>';
                $enteredEmail = FALSE;
                $enteredPass = FALSE;
            }
        
        }
    } else {
        if (isset($_GET['source'])) {
            $tag = $_GET['source'];

            if ($tag == 'reg') {
                echo '<p style="color: green; position: absolute; left: 730px; top: 365px;">Thank you for registering! Please login below.</p>';
            }
        }
    }
?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login to your account</title>
        <link rel="stylesheet" href="css/style.css" type="text/css">
        <link rel="stylesheet" type="text/css" href="css/mobile.css">
        <script src="js/mobile.js" type="text/javascript"></script>
    </head>

    <body>
            <div id="body">
                <!-- Top of Page (Not including nav) -->
                <div class="header">
                    <div class="contact">
                    <div id="loginBox" class="detailBox">
                        
                        <h3 style="text-align:center">Want to login as an admin? Click <a href="adminLogin.php">here</a>.</h3>
                        <h2>Please login using your details below.</h2>
                        <form id="login" action="login.php" method="post">
                            <div id="loginField" style="margin-top: -30px">
                                <!-- E-mail address -->
                                <label for="loginEmail"><h3 style="color:red;">Enter e-mail address<?php print $emailErrorMessage?></h3></label>
                                <input id="loginEmail" type="email" name="loginEmail" onfocus="this.select()">
                                
                                <!-- Password -->
                                <label for="loginPass"><h3 style="color:red;">Enter password<?php print $passErrorMessage?></h3></label>
                                <input id="loginPass" type="password" name="loginPass">
        
                                <!-- Can't use external CSS for this because of how the template works -->

                                <p style='margin-bottom: 10px; margin-top: -10px; margin-left: -320px;'><a style="font-size: 16px; color: rgb(0,102,204)" href="register.php">Create account</p>
                                
                                <input name="enter" id="submit" style="
                                                                    background-color: #3c0f38;
                                                                    color: #ffffff;
                                                                    cursor: pointer;
                                                                    display: block;
                                                                    float: left;
                                                                    font-family: audiowide-regular-webfont;
                                                                    font-size: 14px;
                                                                    font-weight: normal;
                                                                    border-radius: 5px;
                                                                    height: 31px;
                                                                    line-height: 31px;
                                                                    margin: 0;
                                                                    padding: 0;
                                                                    text-align: center;
                                                                    text-transform: uppercase;
                                                                    width: 100%;"
                                type="submit" value="Submit">
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