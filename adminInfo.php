<?php

    session_start(); //begin session

    if (isset($_SESSION['aid']) || isset($_SESSION['sid'])) { //if user not logged in anymore
        
    } else {
        session_destroy();
        Header ("Location:index.php"); //send user to logout automatically
    }

    $updatedMsg = '';

    if (isset($_GET['source'])) {
        if ($_GET['source'] === 'upd') {
            $updatedMsg = '<h2>See your updated information below</h2><hr>';
        }
    }

	include "header.php";

    //Retrieve User Information

    $stmt = $con->prepare("SELECT FirstName, LastName, Username, SuperAdmin, Active FROM ADMINS WHERE AdminID = ?");

    if (isset($_SESSION['aid'])) { //if user not logged in anymore
        $stmt->execute(array($_SESSION['aid']));
    } else if (isset($_SESSION['sid'])) {
        $stmt->execute(array($_SESSION['sid']));
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $first = '<h3>First Name:</h3> <p margin-left:20px">'.$row['FirstName'].'</p>';
    $last = '<h3>Last Name:</h3> <p style="margin-left:20px">'.$row['LastName'].'</p>';
    $user = '<h3>Username:</h3> <p style="margin-left:20px">'.$row["Username"].'</p>';
    $status = '<h3>Super Admin?:</h3> <p style="margin-left:20px">'.$row["SuperAdmin"].'</p>';

    $stmt->closeCursor(); //close current statement result
?>

<!--
	Name: Christian Heatherly
	File name: adminInfo.php
	Date created: 11/11/22
	Date last modified: 11/11/22

	Sources:
-->

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Information</title>
        <link rel="stylesheet" href="css/style.css" type="text/css">
        <link rel="stylesheet" type="text/css" href="css/mobile.css">
        <script src="js/mobile.js" type="text/javascript"></script>
    </head>

    <body>
            <div id="body" class="home">
                <!-- Top of Page (Not including nav) -->
                <div class="header">

                </div>

                <!-- Middle of page -->

                <div class="body" id="adminPage">
                    <?php print $updatedMsg?>
                    <h1>Your information</h1>
                    <ul>

                        <li style="margin-left:20px">
                            <?php print $first?>
                            <?php print $last?>
                            <?php print $user?>
                            <?php print $status?>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom of page -->

            <div id="footer" style="background-color:white;">
                
            </div>
        </div>
    </body>
</html>