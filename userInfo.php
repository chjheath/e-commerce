<?php

    session_start(); //begin session

    require_once "util/sessionVerify.php";

    if (!isset($_SESSION['uid'])) { //if user not logged in anymore
        session_destroy();
        Header ("Location:login.php"); //send user to logout automatically
    }

    $updatedMsg = '';

    if (isset($_GET['source'])) {
        if ($_GET['source'] === 'upd') {
            $updatedMsg = '<h2>See your updated information below</h2><hr>';
        }
    }

	include "header.php";

    //Retrieve User Information

    $stmt = $con->prepare("SELECT FirstName, LastName, Email, Phone, AddressID FROM USERS WHERE UserID = ?");
    $stmt->execute(array($_SESSION['uid']));

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $a_id = $row['AddressID']; //bring in addressid for further searching [can turn into compound query later]

    $first = '<h3>First Name:</h3> <p margin-left:20px">'.$row['FirstName'].'</p>';
    $last = '<h3>Last Name:</h3> <p style="margin-left:20px">'.$row['LastName'].'</p>';
    $email = '<h3>Email:</h3> <p style="margin-left:20px">'.$row["Email"].'</p>';
    $phone = '<h3>Phone Number:</h3> <p style="margin-left:20px">'.$row["Phone"].'</p>';

    $stmt->closeCursor(); //close current statement result

    //Retrieve Address Information

    $stmt = $con->prepare("SELECT AddressOne, AddressTwo, ZipCode, City, StateID FROM ADDRESS WHERE AddressID = ?");
    $stmt->execute(array($a_id));

    $a_row = $stmt->fetch(PDO::FETCH_ASSOC);

    $s_id = $a_row['StateID']; //bring in state id for further searching

    $addressHeader = '<h3>Address: </h3>';

    $addressOne = '<h3>Address One:</h3> <p style="margin-left:20px">'.$a_row["AddressOne"].'</p>';
    
    if ($a_row['AddressTwo'] == "") {
        $addressTwo = '<h3>Address Two:</h3> <p style="margin-left:20px">N/A</p>';
    } else {
        $addressTwo = '<h3>Address Two:</h3> <p style="margin-left:20px">'.$a_row["AddressTwo"].'</p>';
    }

    $city = '<h3>City:</h3> <p style="margin-left:20px">'.$a_row["City"].'</p>';
    $zip = '<h3>Zip Code:</h3> <p style="margin-left:20px">'.$a_row["ZipCode"].'</p>';

    $stmt->closeCursor(); //closing current result cursor

    //Retrieve State Information for supplied address 
    $stmt = $con->prepare("SELECT StateID, StateName, StateInitial FROM STATES WHERE StateID = ?");
    $stmt->execute(array($s_id));

    $s_row = $stmt->fetch(PDO::FETCH_ASSOC);

    $state = '<h3>State:</h3> <p style="margin-left:20px">'.$s_row["StateName"].' ('.$s_row["StateInitial"].')</p>';

    $stmt->closeCursor(); //closing current result cursor
?>

<!--
	Name: Christian Heatherly
	File name: userInfo.php
	Date created: 09/30/22
	Date last modified: 11/12/22

	Sources:
-->

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Information</title>
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
                        <li>
                            <a href="updateInfo.php">UPDATE INFO</a>
                        </li>

			            <li>
                            <a href="changePwd.php">CHANGE PWD</a>
                        </li>

                        <li>
                            <a href="orderHistory.php">ORDER HISTORY</a>
                        </li>

                        <br><br><br>

                        <hr style="width:1810px">

                        <li style="margin-left:20px">
                            <?php print $first?>
                            <?php print $last?>
                            <?php print $email?>
                            <?php print $phone?>
                            <?php print $addressHeader?>
                            <li style="margin-left:40px">
                                <?php print $addressOne?>
                                <?php print $addressTwo?>
                                <?php print $city?>
                                <?php print $state?>
                                <?php print $zip?>
                            </li>

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