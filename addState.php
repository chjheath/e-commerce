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
	File name: addState.php
	Date created: 11/06/22
	Date last modified: 12/02/22

	Sources:
        https://www.w3schools.com/tags/att_input_pattern.asp
-->

<?php

	//string variables to use when reporting specific errors to the user

	$stateNameError = "";
    $stateInitError = "";

	//boolean variables to keep track of if the user has entered a value

	$enteredName = FALSE;
    $enteredInitial = FALSE;

	//variables to keep track of the entered values

	$stateName = "";
    $stateInit = "";
    $activeStatus = "";

    $position = "bottom:280px";

	if (isset($_POST['enter'])) {

        //State Name Posting

        if (isset($_POST['stateName']) && $_POST['stateName'] !== 'Type here...') {
            $stmt = $con->prepare("SELECT COUNT(*) as c FROM STATES WHERE StateName = ?");
            $stmt->execute(array(sqlReplace(trim($_POST['stateName']))));
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $row['c'];

            if ($count == 0) {
                $enteredName = TRUE;
                $stateName = sqlReplace(trim($_POST['stateName']));
            } else {
                $enteredName = FALSE;
                $stateNameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>State name already exists.</p>";
            }

            
        } else {
            $position = "bottom:260px";
            $stateNameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a state name!</p>";
        }

        //State Initial Posting

        if (isset($_POST['stateInit']) && $_POST['stateInit'] !== 'Type here...') {
            $stmt = $con->prepare("SELECT COUNT(*) as c FROM STATES WHERE StateInitial = ?");
            $stmt->execute(array(sqlReplace(trim($_POST['stateInit']))));
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $row['c'];

            if ($count == 0) {
                $enteredInitial = TRUE;
                $stateInit = sqlReplace(trim($_POST['stateInit']));
            } else {
                $enteredInitial = FALSE;
                $stateInitError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>State with that initial already exists.</p>";
            }

        } else {
            $position = "bottom:260px";
            $stateInitError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a state initial!</p>";
        }

        if (isset($_POST['activeStatus'])) {
            $activeStatus = 'YES';
        } else {
            $activeStatus = 'NO';
        }

        //Fixing position of checkbox based on errors

        if ((isset($_POST['stateName']) && $_POST['stateName'] === 'Type here...') && (isset($_POST['stateInit']) && $_POST['stateInit'] === 'Type here...')) {
            $position = "bottom:240px";
        }

        //Active Status Posting

        if ($enteredName && $enteredInitial) {

            $stmt = $con->prepare("INSERT INTO STATES VALUES (NULL, ?, ?, ?)");
            $stmt->execute(array($stateName, $stateInit, $activeStatus));
            $stmt->closeCursor();

            Header("Location:admin.php?source=addstate");
        }

	}
?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add State</title>
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
                            <h1 style="color:black">Add new state</h1>
                            <form id="addState" action="addState.php" method="post">
                                <!-- First Name (Left Hand) -->
                                <label for="stateName"><h3>Enter state name <?php print $stateNameError?></h3></label>
                                <input type="text" name="stateName" value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';">
                                    
                                <!-- Last Name -->
                                <label for="stateInit"><h3>Enter state initial<?php print $stateInitError?></h3></label>
                                <input type="text" name="stateInit" value="Type here..." pattern="[A-z]{2}" onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';"> <!-- https://www.w3schools.com/tags/att_input_pattern.asp -->

                                <br><br><br><br>

                                <!-- Active Status -->
                                <input style="position:absolute;<?php print $position?>;left:530px;" type="checkbox" name="activeStatus">
                                <p style="margin-top:-75px; width: 100%; max-width:100%; margin-left:15px;">Choose whether this state will be active or not.</p>

                                <input type="submit" name="enter" id="submit" value="Add State" style="width:50%; margin-left:25%; margin-bottom:5px;">
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