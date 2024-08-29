<?php

    session_start();

    include "header.php";

    require_once 'js/validation.php';

    if (!isset($_SESSION['aid']) && !isset($_SESSION['sid'])) {
        Header("Location:index.php");
    }

    if (!isset($_SESSION['chosen'])) {
        Header("Location:admin.php");
    }

    $chosenID = '';

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] !== '') {
        $chosenID = $_SESSION['chosen'];
    }

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] === '') {
        Header("Location:chooseVenue.php");
    }

    if (isset($_SESSION['task']) && $_SESSION['task'] === '') {
        Header("Location:admin.php");
    }

    $stmt = $con->prepare("SELECT VenueName, City, StateID, Active FROM VENUES WHERE VenueID = ?");
    $stmt->execute(array($chosenID));

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $in_name = $row['VenueName'];
    $in_city = $row['City'];
    $in_state = $row['StateID'];
    $in_act = $row['Active'];

    $stmt->closeCursor(); //close current statement result

    function stateOptionList($addrID)
    {  	
        //retrieve all states from database & insert them into a list

        $list = "";
        global $con; //sql connection

        $sql = "SELECT StateID, StateName, Active FROM STATES WHERE Active = 'YES'";
        $result = $con->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if ($row['StateID'] == $addrID) {
                echo $row["StateID"];
                $list = $list . '<option value ="'.$row["StateID"].'" selected>'.$row["StateName"].'</option>';
            } else {
                $list = $list . '<option value ="'.$row["StateID"].'">'.$row["StateName"].'</option>';
            }
        }

        return $list;
    }

?>

<!--
	Name: Christian Heatherly
	File name: editVenue.php
	Date created: 11/10/22
	Date last modified: 11/12/22

	Sources:
    -->

<?php

    //variables to keep track of states, errors, etc.

    $boxPos = 'position:absolute;bottom:160px;left:530px;';

    $venueNameError = '';
    $venueCityError = '';

    $venueNameOK = TRUE;
    $venueCityOK = TRUE;
    $venueExists = FALSE;

    $venueName = '';
    $venueCity = '';
    $venueState = '';

    $activeStatus = '';

    if (isset($_POST['enter'])) { //form submitted
        if (isset($_POST['venueName']) && ($_POST['venueName'] !== "Type here..." && $_POST['venueName'] !== "")) {
            $stmt = $con->prepare("SELECT COUNT(VenueName) as c FROM VENUES WHERE VenueName = ?");
            $stmt->execute(array(sqlReplace(trim($_POST['venueName']))));

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $row['c'];

            if ($count == 0) {
                $venueName = sqlReplace(trim($_POST['venueName']));
                $venueNameOK = TRUE;
            } else if ($count !== 0 && $_POST['venueName'] !== $in_name) {
                $venueExists = TRUE;
                $venueNameOK = FALSE;
                $venueNameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>That venue name already exists.</p>";
            }
        } else {
            $venueNameOK = FALSE;
            $venueNameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a new name! If you don't want to change their name, do not touch the textbox.</p>";
        }

        if (isset($_POST['venueCity']) && ($_POST['venueCity'] !== "Type here..." && $_POST['venueCity'] !== "")) {
            $venueCity = sqlReplace(trim($_POST['venueCity']));
            $venueCityOK = TRUE;
        } else {
            $venueCityOK = FALSE;
            $venueCityError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a new city! If you don't want to change their city, do not touch the textbox.</p>";
        }

        if (isset($_POST['venueState'])) {
            $venueState = sqlReplace(trim($_POST['venueState']));
        }


        if (isset($_POST['activeStatus'])) {
            $activeStatus = 'YES';
        } else {
            $activeStatus = 'NO';
        }

        if ($venueNameOK && $venueCityOK) {
            $stmt = $con->prepare("UPDATE VENUES SET VenueName = ?, City = ?, StateID = ?, Active = ? WHERE VenueID = ?");
            $stmt->execute(array($venueName, $venueCity, $venueState, $activeStatus, $chosenID));
            $stmt->closeCursor();

            Header("Location:admin.php?source=editvenue");
        } else if (!$venueNameOK && $venueCityOK && !$venueExists) {
            $boxPos = 'position:absolute;bottom:115px;left:530px;';
        } else if (!$venueCityOK && $venueNameOK&& !$venueExists) {
            $boxPos = 'position:absolute;bottom:115px;left:530px;';
        } else if (!$venueNameOK && !$venueCityOK && !$venueExists) {
            $boxPos = 'position:absolute;bottom:95px;left:530px;';
        } else if ($venueExists) {
            $boxPos = 'position:absolute;bottom:145px;left:530px;';
        }

    }

?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Update venue information</title>
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
                            <form id="updateInfo" action="editVenue.php" method="post">
                                <!-- Name -->
                                <label for="venueName"><h3>Update their name <?php print $venueNameError?></h3></label>
                                <input type="text" name="venueName" onfocus="this.select()" value="<?php print $in_name?>">

                                <!-- City -->
                                <label for="venueCity"><h3>Update their city <?php print $venueCityError?></h3></label>
                                <input type="text" name="venueCity" onfocus="this.select()" value="<?php print $in_city?>">

				<!-- State -->
				<label for="venueState"><h3>Update their state</h3></label>

                                <select name="venueState">
                                    <?php 
                                    
                                    	$stmt = $con->prepare("SELECT StateID FROM VENUES WHERE VenueID = ?");
                                    	$stmt->execute(array($chosenID));
                                    	$row = $stmt->fetch(PDO::FETCH_ASSOC);
                                    	$stateIDRes = $row['StateID'];

                                    	print stateOptionList($stateIDRes);
				                    ?> 
                                </select>

                           
                                <br><br><br><br>

                                <!-- Active? -->
                                <input style="<?php print $boxPos?>" type="checkbox" name="activeStatus" <?php if ($in_act == 'YES') echo "checked='checked'";?>> <!-- https://stackoverflow.com/a/16239755 -->
                                <p style="margin-top:-75px; width: 100%; max-width:100%; margin-left:15px;">Choose whether this venue is active or not.</p>

                                <input type="submit" name="enter" id="submit" value="Submit" style="width:95%">
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