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
        Header("Location:chooseState.php");
    }

    if (isset($_SESSION['task']) && $_SESSION['task'] === '') {
        Header("Location:admin.php");
    }

    $stmt = $con->prepare("SELECT * FROM STATES WHERE StateID = ?");
    $stmt->execute(array($chosenID));

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $in_name = $row['StateName'];
    $in_initial = $row['StateInitial'];
    $in_act = $row['Active'];

    $stmt->closeCursor(); //close current statement result

?>

<!--
	Name: Christian Heatherly
	File name: editState.php
	Date created: 11/12/22
	Date last modified: 11/12/22

	Sources:
    -->

<?php

    //variables to keep track of states, errors, etc.

    $boxPos = 'position:absolute;bottom:280px;left:530px;';

    $stateNameError = '';
    $stateInitialError = '';

    $stateNameOK = TRUE;
    $stateInitialOK = TRUE;

    $isChangingName = FALSE;
    $isChangingInitial = FALSE;

    $stateExists = FALSE;
    $initialExists = FALSE;

    $stateName = '';
    $stateInitial = '';

    $activeStatus = '';

    if (isset($_POST['enter'])) { //form submitted
        //State Name

        if (isset($_POST['stateName']) && ($_POST['stateName'] !== 'Type here...' && $_POST['stateName'] !== '')) {

            if (isset($_POST['stateName']) && ($_POST['stateName'] == $in_name)) {
                $isChangingName = FALSE;

                $stateName = sqlReplace(trim($_POST['stateName']));
                $stateNameOK = TRUE;

            } else {
                $isChangingName = TRUE;
                $stmt = $con->prepare("SELECT COUNT(StateName) as c FROM STATES WHERE StateName = ?");
                $stmt->execute(array(sqlReplace(trim($_POST['stateName']))));

                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $count = $row['c'];

                if ($count == 0) {
                    $stateName = sqlReplace(trim($_POST['stateName']));
                    $stateNameOK = TRUE;
                } else {
                    $stateExists = TRUE;
                    $stateNameOK = FALSE;
                    $stateNameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>A state with that name already exists.</p>";
                }
            }
        } else {
            $stateNameOK = FALSE;
            $stateNameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a new name! If you don't want to change the state name, do not touch the textbox.</p>";
        }

        //State Initial

        if (isset($_POST['stateInitial']) && ($_POST['stateInitial'] !== 'Type here...' && $_POST['stateInitial'] !== '')) {

            if (isset($_POST['stateInitial']) && ($_POST['stateInitial'] == $in_initial)) {
                $isChangingInitial = FALSE;
                $stateInitial = sqlReplace(trim($_POST['stateInitial']));
                $stateInitialOK = TRUE;

            } else {
                $isChangingInitial = TRUE;

                $stmt = $con->prepare("SELECT COUNT(StateInitial) as c FROM STATES WHERE StateInitial = ?");
                $stmt->execute(array(sqlReplace(trim($_POST['stateInitial']))));
    
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $count = $row['c'];
    
                if ($count == 0) {
                    $stateInitial = sqlReplace(trim($_POST['stateInitial']));
                    $stateInitialOK = TRUE;
                } else {
                    $initialExists = TRUE;
                    $stateInitialOK = FALSE;
                    $stateInitialError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>A state with those initials already exists.</p>";
                }
            }
        } else {
            $stateInitialOK = FALSE;
            $stateInitialError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a new set of initials! If you don't want to change the states' initials, do not touch the textbox.</p>";
        }

        //Active Status

        if (isset($_POST['activeStatus'])) {
            $activeStatus = 'YES';
        } else {
            $activeStatus = 'NO';
        }

        //Updating

        if ($stateNameOK && $stateInitialOK) {
            
            if ($isChangingName && $isChangingInitial) {
                $stmt = $con->prepare("UPDATE STATES SET StateName = ?, StateInitial = ?, Active = ? WHERE StateID = ?");
                $stmt->execute(array($stateName, $stateInitial, $activeStatus, $chosenID));

                Header("Location:admin.php?source=editstate");
            } else if ($isChangingName && !$isChangingInitial) {
                $stmt = $con->prepare("UPDATE STATES SET StateName = ?, Active = ? WHERE StateID = ?");
                $stmt->execute(array($stateName, $activeStatus, $chosenID));

                Header("Location:admin.php?source=editstate");
            } else if (!$isChangingName && $isChangingInitial) {
                $stmt = $con->prepare("UPDATE STATES SET StateInitial = ?, Active = ? WHERE StateID = ?");
                $stmt->execute(array($stateInitial, $activeStatus, $chosenID));
    
                Header("Location:admin.php?source=editstate");
            } else if (!$isChangingName && !$isChangingInitial) {
                $stmt = $con->prepare("UPDATE STATES SET Active = ? WHERE StateID = ?");
                $stmt->execute(array($activeStatus, $chosenID));

                Header("Location:admin.php?source=editstate");
            }

        } else if (!$stateNameOK && !$stateInitialOK) { //Name Blank Or Exists && Initial Blank or Exists

            if ($stateExists && $initialExists) { //not blank but exists
                $boxPos = 'position:absolute;bottom:243px;left:530px;';
            } else if (!$stateExists && !$initialExists) { //both blank
                $boxPos = 'position:absolute;bottom:195px;left:530px;';
            } else if ($stateExists && !$initialExists) { //state name exists, state initial blank
                $boxPos = 'position:absolute;bottom:220px;left:530px;';
            } else if (!$stateExists && $initialExists) { //state name blank, state initial exists
                $boxPos = 'position:absolute;bottom:220px;left:530px;';
            }

        } else if (!$stateNameOK && $stateInitialOK) { //Name Blank or Exists, Initial OK

            if ($stateExists) { //state name exists
                $boxPos = 'position:absolute;bottom:265px;left:530px;';
            } else if (!$stateExists) { //state name blank
                $boxPos = 'position:absolute;bottom:235px;left:530px;';
            }

        } else if ($stateNameOK && !$stateInitialOK) {
            if ($initialExists) { //state initial exists
                $boxPos = 'position:absolute;bottom:265px;left:530px;';
            } else if (!$initialExists) { //state initial blank
                $boxPos = 'position:absolute;bottom:235px;left:530px;';
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
        <title>Update state information</title>
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
                            <h1 style="color:black">Enter state information below.</h1>
                            <!-- Code from Lab1 -->
                            <form id="updateInfo" action="editState.php" method="post">
                                <!-- Name -->
                                <label for="stateName"><h3>Update state name <?php print $stateNameError?></h3></label>
                                <input type="text" name="stateName" onfocus="this.select()" value="<?php print $in_name?>">

                                <!-- City -->
                                <label for="stateInitial"><h3>Update state initial <?php print $stateInitialError?></h3></label>
                                <input type="text" name="stateInitial" pattern="[A-z]{2}" onfocus="this.select()" value="<?php print $in_initial?>">
                           
                                <br><br><br><br>

                                <!-- Active? -->
                                <input style="<?php print $boxPos?>" type="checkbox" name="activeStatus" <?php if ($in_act == 'YES') echo "checked='checked'";?>> <!-- https://stackoverflow.com/a/16239755 -->
                                <p style="margin-top:-75px; width: 100%; max-width:100%; margin-left:15px;">Choose whether this state is active or not.</p>

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