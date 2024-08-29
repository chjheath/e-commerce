<?php

    session_start();

    $_SESSION['timeout'] = time();

    if (isset($_SESSION['aid']) || isset($_SESSION['sid'])) { //if user already logged in
        Header ("Location:admin.php"); //send user to user page
    }

	include "header.php";
?>

<!--
	Name: Christian Heatherly
	File name: adminLogin.php
	Date created: 09/30/22
	Date last modified: 11/12/22

	Sources:
		ch_12_sessions
-->

<?php

    $loginUser = "";
    $loginPass = "";

    $userErrorMessage = "";
    $passErrorMessage = "";
    $loginErrorMessage = "";

    $enteredUser = FALSE;
    $enteredPass = FALSE;

    if (isset($_POST['enter'])) {
        if (isset($_POST['loginUser']) && $_POST['loginUser'] != "") { //email not entered
            $loginUser = trim($_POST['loginUser']);
            $loginUser = sqlReplace($loginUser);
            $userErrorMessage = "";
            $enteredUser = TRUE;
            
        } else {
            $userErrorMessage = "<p style='color:black; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a username!</p>";
        }
    
        if (isset($_POST['loginPass']) && $_POST['loginPass'] != "") { //pass not entered
            $passErrorMessage = "";
            $loginPass = trim($_POST['loginPass']);
            $loginPass = sqlReplace($loginPass);
            $enteredPass = TRUE;
        } else {
            $passErrorMessage = "<p style='color:black; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a password!</p>";
        }
    
        if ($enteredUser && $enteredPass) {      
            
            //If user is super-admin
            
            $stmt = $con->prepare("SELECT count(*) as result FROM ADMINS WHERE Username = ? AND Password = ? AND Active = 'YES'");
            $stmt->execute(array($loginUser, $loginPass));
            $row = $stmt->fetch(PDO::FETCH_OBJ);

            $count = $row->result;

            if ($count == 1) {
                $stmt = $con->prepare("SELECT AdminID, SuperAdmin FROM ADMINS WHERE Username = ? AND Password = ?");
                $stmt->execute(array($loginUser, $loginPass));

                $row = $stmt->fetch(PDO::FETCH_OBJ);

                $status = $row->SuperAdmin;
                $aid = $row->AdminID;

                if ($status == "YES") {
                    $_SESSION['sid'] = $aid;
                } else {
                    $_SESSION['aid'] = $aid;
                }

                $_SESSION['user'] = $loginUser;

                $stmt = $con->prepare("UPDATE ADMINS SET LastLogin = ? WHERE AdminID = ?");
                $stmt->execute(array(time(), $aid));

                $stmt->closeCursor();

                Header("Location:admin.php");
            } else {
                echo '<p style="color: red; position: absolute; left: 730px; top: 315px;">Your information was incorrect. Please try again.</p>';
                $enteredUser = FALSE;
                $enteredPass = FALSE;
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
        <title>Login to admin page</title>
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
                        <h2>Please login using your details below.</h2>
                        <form id="adminLogin" action="adminLogin.php" method="post">
                            <div id="loginField">
                                <!-- E-mail address -->
                                <label for="loginUser"><h3 style="color:red;">Enter username<?php print $userErrorMessage?></h3></label>
                                <input id="loginUser" type="text" name="loginUser" onfocus="this.select()">
                                
                                <!-- Password -->
                                <label for="loginPass"><h3 style="color:red;">Enter password<?php print $passErrorMessage?></h3></label>
                                <input id="loginPass" type="password" name="loginPass">
        
                                <!-- Can't use external CSS for this because of how the template works -->
                                
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