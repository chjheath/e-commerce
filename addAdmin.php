<?php
    session_start();

    include "header.php";

    require_once 'js/validation.php';

    if (!isset($_SESSION['sid'])) {
        Header("Location:admin.php");
    }
?>

<!--
	Name: Christian Heatherly
	File name: addAdmin.php
	Date created: 09/30/22
	Date last modified: 11/13/22

	Sources:
-->

<?php

	//string variables to use when reporting specific errors to the user

	$firstError = "";
	$lastError = "";

	$usernameError = "";

	$passError = "";
	$confPassError = "";

    $submitted = "";

    $hiddenClass = "hidden detailBox";
    $hiddenStyle = "margin-top:-575px;";
    $submittedClass = "detailBox";

	//boolean variables to keep track of if the user has entered a value

	$enteredFirst = FALSE;
	$enteredLast = FALSE;

	$enteredUser = FALSE;

	$enteredPass = FALSE;
	$hasConfirmedPass = FALSE;
	$passMatches = FALSE;

	//variables to keep track of the entered values

	$first = "";
	$last = "";

    $username = "";

	$pass = "";
	$confirmPass = "";

    $activeStatus = "";
    $superStatus = "";

	if (isset($_POST['enter'])) {

		//Name Posting

		if (isset($_POST['firstName']) && $_POST['firstName'] != "Type here...") {
			$enteredFirst = TRUE;
			$first = trim($_POST['firstName']);
		} else {
			//return to form
			$firstError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a first name!</p>";


		}

		if (isset($_POST['lastName']) && $_POST['lastName'] != "Type here...") {
			$enteredLast = TRUE;
			$last = trim($_POST['lastName']);
		} else {
			//return to form
			$lastError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a last name!</p>";
		}

		//Email Validation & Posting

        if (isset($_POST['username']) && $_POST['username'] != "Type here...") {
            $stmt = $con->prepare("SELECT COUNT(*) as c FROM ADMINS WHERE Username = ?");
            $stmt->execute(array(sqlReplace(trim($_POST['username']))));
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $row['c'];

            if ($count == 0) {
                $enteredUser = TRUE;
                $username = sqlReplace(trim($_POST['username']));
            } else {
                $enteredUser = FALSE;
                $usernameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>Admin with username already exists.</p>";
            }
		} else {
			//return to form
			$usernameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a username!</p>";
		}

        //Password Validation & Posting

        if (isset($_POST['pass']) && $_POST['pass'] !== "Type here...") {
            if (validateQuery(trim($_POST['pass']))) {
                $pass = trim($_POST['pass']);
            } else {
                $passError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>Your password does not meet specifications (Must be >= 10 characters, must contain both letters & digits)";
            }

        } else {
            //return to form
            $passError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a password!</p>";
        }

        //Confirm password Posting

        if (isset($_POST['confPass']) && $_POST['confPass'] !== "Type here...") {
            $confirmPass = trim($_POST['confPass']);
        }

        if ($pass != $confirmPass || ($pass == "" || $confirmPass == "")) {
            $passMatches = FALSE;
            
            //return to form
            $confPassError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-213px;'>Your passwords do not match!</p>";

        } else if ($pass === $confirmPass) {
            $passMatches = TRUE;
        }

        if (isset($_POST['superStatus'])) {
            $superStatus = "YES";
        } else {
            $superStatus = "NO";
        }

        if (isset($_POST['activeStatus'])) {
            $activeStatus = "YES";
        } else {
            $activeStatus = "NO";
        }

		if ($enteredFirst && $enteredLast && $enteredUser && $passMatches) {
            $first = sqlReplace($first);
            $last = sqlReplace($last);

            $username = sqlReplace($username);
            $pass = sqlReplace($pass);
            $superStatus = sqlReplace($superStatus);

            $stmt = $con->prepare("INSERT INTO ADMINS VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(array($username, $pass, $first, $last, time(), $superStatus, $activeStatus));

            $stmt->closeCursor();

            Header("Location:admin.php?source=admin");
        }
	}
?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add Admin</title>
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
                            <h1 style="color:black">Add new admin</h1>
                            <form id="addAdmin" action="addAdmin.php" method="post">
                                <!-- First Name (Left Hand) -->
                                <label for="firstName"><h3>Enter first name <?php print $firstError?></h3></label>
                                <input type="text" name="firstName" value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';" required="true">
                                    
                                <!-- Last Name -->
                                <label for="lastName"><h3>Enter last name <?php print $lastError?></h3></label>
                                <input type="text" name="lastName" value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';" required>
                                
                                <!-- E-mail address -->
                                <label for="username"><h3>Enter username <?php print $usernameError?></h3></label>
                                <input type="text" name="username" value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';" required>

                                <!-- Password -->
                                <label for="pass"><h3>Enter password <?php print $passError?></h3></label>
                                <input type="password" name="pass" value="Type here..." onclick="this.value='';" required>
                            
                                <!-- Confirm Password -->
                                <label for="confPass"><h3>Confirm password <?php print $confPassError?></h3></label>
                                <input type="password" name="confPass" value="Type here..." onclick="this.value='';" required>

                                <br><br><br><br>

                                <!-- Super Status -->
                                <input style="position:absolute;bottom:170px;left:530px;" type="checkbox" name="superStatus">
                                <p style="margin-top:-75px; width: 100%; max-width:100%; margin-left:15px;">Choose whether the admin will be a super admin.</p>

                                <br><br><br><br>

                                <!-- Active Status -->
                                <input style="position:absolute;bottom:80px;left:530px;" type="checkbox" name="activeStatus">
                                <p style="margin-top:-75px; width: 100%; max-width:100%; margin-left:15px;">Choose whether this admin will be active or not.</p>

                                <input type="submit" name="enter" id="submit" value="Add Admin" style="width:96%">
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