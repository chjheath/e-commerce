<?php

    session_start();

	include "header.php";

    require_once 'js/validation.php';

    if (isset($_SESSION['aid']) || isset($_SESSION['sid'])) { //if user not logged in anymore
        
    } else {
        session_destroy();
        Header ("Location:index.php"); //send user to logout automatically
    }

    $chosenID = '';

    $stmt = $con->prepare("SELECT FirstName, LastName, Username FROM ADMINS WHERE AdminID = ?");
    
    if (isset($_SESSION['sid'])) {
        $chosenID = $_SESSION['sid'];
        $stmt->execute(array($_SESSION['sid']));
    } else if (isset($_SESSION['aid'])) {
        $chosenID = $_SESSION['aid'];
        $stmt->execute(array($_SESSION['aid']));
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $in_first = $row['FirstName'];
    $in_last = $row['LastName'];
    $in_user = $row['Username'];

    $stmt->closeCursor(); //close current statement result

?>

    <!--
	Name: Christian Heatherly
	File name: adminUpdateInfo.php
	Date created: 11/11/22
	Date last modified: 11/11/22

	Sources:
    -->

<?php

    //variables to keep track of states, errors, etc.

    $firstError = '';
    $lastError = '';
    $usernameError = '';

    $firstOK = TRUE;
    $lastOK = TRUE;
    $userOK = TRUE;
    $isChangingUser = FALSE;

    $first = '';
    $last = '';
    $user = '';


    if (isset($_POST['enter'])) { //form submitted

        //First Name

        if (isset($_POST['firstName']) && $_POST['firstName'] !== '') {
            $first = sqlReplace(trim($_POST['firstName']));
        } else {
            $firstOK = FALSE;
            $firstError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a first name! If you don't want to change your name, do not touch the textbox.</p>";
        }

        //Last Name

        if (isset($_POST['lastName']) && $_POST['lastName'] !== '') {
            $last = sqlReplace(trim($_POST['lastName']));
        } else {
            $lastOK = FALSE;
            $lastError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a last name! If you don't want to change your name, do not touch the textbox.</p>";
        }

        //Username

        if ($_POST['username'] !== $in_user) {
            $isChangingUser = TRUE;
        }

        if ((isset($_POST['username']) && $_POST['username'] !== '') && $isChangingUser) {
            $stmt = $con->prepare("SELECT COUNT(Username) as c FROM ADMINS WHERE Username = ?");
            $stmt->execute(array($_POST['username']));

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $row['c'];

            if ($count == 0) {
                $user = sqlReplace(trim($_POST['username']));
            } else {
                $userOK = FALSE;
                $usernameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>That username already exists!</p>";
            }
        } else if ((isset($_POST['username']) && $_POST['username'] == '') && !$isChangingUser) {
            $userOK = FALSE;
            $usernameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a username! If you don't want to change your username, do not touch the textbox.</p>";
        } else if ((isset($_POST['username']) && $_POST['username'] == '')) {
            $userOK = FALSE;
            $usernameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a username! If you don't want to change your username, do not touch the textbox.</p>";
        }

        if ($firstOK && $lastOK && $userOK) {

            if ($isChangingUser) {
                //Update user info

                $stmt = $con->prepare("UPDATE ADMINS SET FirstName = ?, LastName = ?, Username = ? WHERE AdminID = ?");
                $stmt->execute(array($first, $last, $user, $chosenID));

                Header("Location:admin.php?source=upd");
            } else {
                    //Update user info

                    $stmt = $con->prepare("UPDATE ADMINS SET FirstName = ?, LastName = ? WHERE AdminID = ?");
                    $stmt->execute(array($first, $last, $chosenID));

                    Header("Location:admin.php?source=upd");
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
        <title>Update your information</title>
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
                            <form id="updateInfo" action="adminUpdateInfo.php" method="post">
                                <!-- First Name (Left Hand) -->
                                <label for="firstName"><h3>Update first name <?php print $firstError?></h3></label>
                                <input type="text" name="firstName" onfocus="this.select()" value="<?php print $in_first?>">
                                    
                                <!-- Last Name -->
                                <label for="lastName"><h3>Update last name <?php print $lastError?></h3></label>
                                <input type="text" name="lastName" onfocus="this.select()" value="<?php print $in_last?>">
                            
                                <br>

                                <!-- E-mail address -->
                                <label for="username"><h3>Update username <?php print $usernameError?></h3></label>
                                <input type="text" name="username" onfocus="this.select()" value="<?php print $in_user?>">

                                <input type="submit" name="enter" id="submit" value="Submit" style="width:96%">
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