<?php

    session_start();

	include "header.php";

    require_once 'js/validation.php';

    if (!isset($_SESSION['sid'])) {
        Header("Location:index.php");
    }

    if (isset($_SESSION['aid']) && !isset($_SESSION['sid'])) {
        Header("Location:admin.php");
    }

    if (!isset($_SESSION['chosen'])) {
        Header("Location:admin.php");
    }

    $chosenID = '';

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] !== '') {
        $chosenID = $_SESSION['chosen'];
    }

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] === '') {
        Header("Location:chooseAdmin.php");
    }

    if (isset($_SESSION['task']) && $_SESSION['task'] === '') {
        Header("Location:admin.php");
    }

    $stmt = $con->prepare("SELECT FirstName, LastName, Username, SuperAdmin, Active FROM ADMINS WHERE AdminID = ?");
    $stmt->execute(array($chosenID));

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $in_first = $row['FirstName'];
    $in_last = $row['LastName'];
    $in_user = $row['Username'];
    $in_sup = $row["SuperAdmin"];
    $in_act = $row['Active'];

    $stmt->closeCursor(); //close current statement result
?>

<!--
	Name: Christian Heatherly
	File name: editUser.php
	Date created: 11/03/22
	Date last modified: 11/13/22

	Sources:
        https://stackoverflow.com/a/16239755
    -->

<?php

    //variables to keep track of states, errors, etc.

    $firstError = '';
    $lastError = '';
    $userError = '';
    $passError = '';
    $confPassError = '';
    $currPassError = '';

    $firstOK = TRUE;
    $lastOK = TRUE;
    $userOK = TRUE;
    $currPassOK = FALSE;
    $passOK = FALSE;

    $userExists = FALSE;
    $isChangingPass = FALSE;
    $isChangingName = FALSE;

    $first = '';
    $last = '';
    $username = '';

    $currPass = '';
    $pass = '';
    $confPass = '';

    $activeStatus = '';

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

        if (isset($_POST['username']) && $_POST['username'] !== '') {

            if (isset($_POST['username']) && ($_POST['username'] == $in_user)) {
                $isChangingName = FALSE;

                $userOK = TRUE;
                $username = sqlReplace(trim($_POST['username']));

            } else {
                $isChangingName = TRUE;

                $stmt = $con->prepare("SELECT COUNT(Username) as c FROM ADMINS WHERE Username = ?");
                $stmt->execute(array(sqlReplace(trim($_POST['username']))));

                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $count = $row['c'];

                if ($count == 0) {
                    $userOK = TRUE;
                    $username = sqlReplace(trim($_POST['username']));
                } else {
                    $userOK = FALSE;
                    $userExists = TRUE;
                    $userError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a unique username! If you don't want to change your phone number, do not touch the textbox.</p>";
                }
            }

        } else {
            $userOK = FALSE;
            $userError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a username! If you don't want to change your phone number, do not touch the textbox.</p>";
        }

        //Checking if admin is editing passwords

        if ((isset($_POST['currPass']) && $_POST['currPass'] !== 'Type here...') && isset($_POST['pass']) && isset($_POST['confPass'])) {
            $isChangingPass = TRUE;
        }

        if (isset($_POST['activeStatus'])) {
            $activeStatus = "YES";
        } else {
            $activeStatus = "NO";
        }

        if (isset($_POST['superStatus'])) {
            $superStatus = "YES";
        } else {
            $superStatus = "NO";
        }

        //Only changing password
        if ($isChangingPass && $isChangingName) {
            if (isset($_POST['currPass']) && $_POST['currPass'] !== 'Type here...') {
                $currPass = sqlReplace(trim($_POST['currPass']));

                $stmt = $con->prepare("SELECT COUNT(*) as num FROM ADMINS WHERE AdminID = ? AND Password = ?");
                if ($stmt->execute(array($chosenID, $currPass)) && isset($_POST['pass']) && isset($_POST['confPass'])) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $count = $row['num'];

                    if ($count < 1) {
                        $passOK = FALSE;
                        $currPassError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>Their current password is incorrect.</p>";
                    } else {
                        if (validateQuery(sqlReplace(trim($_POST['pass'])))) {
                        
                            $pass = sqlReplace(trim($_POST['pass']));
                            $confPass = sqlReplace(trim($_POST['confPass']));
    
                            if ($pass != $confPass || ($pass == "" || $confPass == "")) {
                                $passOK = FALSE;
                                
                                //return to form
                                $confPassError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-213px;'>The passwords do not match!</p>";
                    
                            } else if ($pass === $confPass) {
                                $passOK = TRUE;
                            }
                        } else {
                            $passOK = FALSE;
                            $passError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>The password does not meet specifications (Must be >= 10 characters, must contain both letters & digits)</p>";
                        }
                    }
                }
            }

            if ($firstOK && $lastOK && $userOK && $passOK) {

                //Update admin info

                $stmt = $con->prepare("UPDATE ADMINS SET FirstName = ?, LastName = ?, Username = ?, Password = ?, SuperAdmin = ?, Active = ? WHERE AdminID = ?");
                $stmt->execute(array($first, $last, $username, $pass, $superStatus, $activeStatus, $chosenID));

                $_SESSION['task'] = '';
                $_SESSION['chosen'] = '';

                Header("Location:admin.php?source=editadmin");
            }
        } else if (!$isChangingPass && $isChangingName) { //changing neither email nor password
            if ($firstOK && $lastOK && $userOK) {
                //Update admin info

                $stmt = $con->prepare("UPDATE ADMINS SET FirstName = ?, LastName = ?, Username = ?, SuperAdmin = ?, Active = ? WHERE AdminID = ?");
                $stmt->execute(array($first, $last, $username, $superStatus, $activeStatus, $chosenID));

                $_SESSION['task'] = '';
                $_SESSION['chosen'] = '';

                Header("Location:admin.php?source=editadmin");
            }
        } else if (!$isChangingName && $isChangingPass) {
            if (isset($_POST['currPass']) && $_POST['currPass'] !== 'Type here...') {
                $currPass = sqlReplace(trim($_POST['currPass']));

                $stmt = $con->prepare("SELECT COUNT(*) as num FROM ADMINS WHERE AdminID = ? AND Password = ?");
                if ($stmt->execute(array($chosenID, $currPass)) && isset($_POST['pass']) && isset($_POST['confPass'])) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $count = $row['num'];

                    if ($count < 1) {
                        $passOK = FALSE;
                        $currPassError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>Their current password is incorrect.</p>";
                    } else {
                        if (validateQuery(sqlReplace(trim($_POST['pass'])))) {
                        
                            $pass = sqlReplace(trim($_POST['pass']));
                            $confPass = sqlReplace(trim($_POST['confPass']));
    
                            if ($pass != $confPass || ($pass == "" || $confPass == "")) {
                                $passOK = FALSE;
                                
                                //return to form
                                $confPassError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-213px;'>The passwords do not match!</p>";
                    
                            } else if ($pass === $confPass) {
                                $passOK = TRUE;
                            }
                        } else {
                            $passOK = FALSE;
                            $passError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>The password does not meet specifications (Must be >= 10 characters, must contain both letters & digits)</p>";
                        }
                    }
                }
            }

            if ($firstOK && $lastOK && $passOK) {

                //Update admin info

                $stmt = $con->prepare("UPDATE ADMINS SET FirstName = ?, LastName = ?, Password = ?, SuperAdmin = ?, Active = ? WHERE AdminID = ?");
                $stmt->execute(array($first, $last, $pass, $superStatus, $activeStatus, $chosenID));

                $_SESSION['task'] = '';
                $_SESSION['chosen'] = '';

                Header("Location:admin.php?source=editadmin");
            }
        } else if (!$isChangingName && !$isChangingPass) {
            if ($firstOK && $lastOK) {
                //Update admin info

                $stmt = $con->prepare("UPDATE ADMINS SET FirstName = ?, LastName = ?, SuperAdmin = ?, Active = ? WHERE AdminID = ?");
                $stmt->execute(array($first, $last, $superStatus, $activeStatus, $chosenID));

                $_SESSION['task'] = '';
                $_SESSION['chosen'] = '';

                Header("Location:admin.php?source=editadmin");
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
        <title>Update admin information</title>
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
                            <h1 style="color:black">Enter their information below.</h1>
                            <!-- Code from Lab1 -->
                            <form id="updateInfo" action="editAdmin.php" method="post">
                                <!-- First Name (Left Hand) -->
                                <label for="firstName"><h3>Update their first name <?php print $firstError?></h3></label>
                                <input type="text" name="firstName" onfocus="this.select()" value="<?php print $in_first?>">
                                    
                                <!-- Last Name -->
                                <label for="lastName"><h3>Update their last name <?php print $lastError?></h3></label>
                                <input type="text" name="lastName" onfocus="this.select()" value="<?php print $in_last?>">
                            
                                <br>

                                <!-- E-mail address -->
                                <label for="username"><h3>Update their username <?php print $userError?></h3></label>
                                <input type="text" name="username" onfocus="this.select()" value="<?php print $in_user?>">

                                <!-- Current Password -->
                                <label for="currPass"><h3>Enter their current password <?php print $currPassError?></h3></label>
                                <input type="password" name="currPass" value="Type here..." onclick="this.value='';">

                                <!-- Password -->
                                <label for="pass"><h3>Create new password <?php print $passError?></h3></label>
                                <input type="password" name="pass" value="Type here..." onclick="this.value='';">
                            
                                <!-- Confirm Password -->
                                <label for="confPass"><h3>Confirm new password <?php print $confPassError?></h3></label>
                                <input type="password" name="confPass" value="Type here..." onclick="this.value='';">

                                <br><br><br><br>
                                <br><br>

                                <!-- Super Admin? -->
                                <input style="position:absolute;bottom:180px;left:530px;" type="checkbox" name="superStatus" <?php if ($in_sup == 'YES') echo "checked='checked'";?>> <!-- https://stackoverflow.com/a/16239755 -->
                                <p style="margin-top:-75px; width: 100%; max-width:100%; margin-left:44px;">Choose whether this admin is a super admin or not.</p>

                                <br><br><br><br>

                                <!-- Active? -->
                                <input style="position:absolute;bottom:80px;left:530px;" type="checkbox" name="activeStatus" <?php if ($in_act == 'YES') echo "checked='checked'";?>> <!-- https://stackoverflow.com/a/16239755 -->
                                <p style="margin-top:-75px; width: 100%; max-width:100%; margin-left:15px;">Choose whether this admin is active or not.</p>

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