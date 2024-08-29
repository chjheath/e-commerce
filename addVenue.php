<?php
    session_start();

    include "header.php";

    require_once 'js/validation.php';

    if (!isset($_SESSION['aid']) && !isset($_SESSION['sid'])) {
        Header("Location:index.php");
    }
?>

<!--
	Name: Christian Heatherly
	File name: addVenue.php
	Date created: 09/30/22
	Date last modified: 11/30/22

	Sources:
    -->

<?php

    function stateOptionList()
    {  	
        //retrieve all states from database & insert them into a list

        $list = "";
        global $con; //sql connection

        $sql = "SELECT StateID, StateName, Active FROM STATES WHERE Active = 'YES'";
        $result = $con->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $list = $list . '<option value ="'.$row["StateID"].'">'.$row["StateName"].'</option>';
        }

        return $list;
    }

	//string variables to use when reporting specific errors to the user

	$nameError = "";

    $addrCityError = "";
    $addrStateError = "";

    $submitted = "";

    $hiddenClass = "hidden detailBox";
    $hiddenStyle = "margin-top:-75px;";
    $submittedClass = "detailBox";

	//boolean variables to keep track of if the user has entered a value

	$enteredName = FALSE;

	$enteredCity = FALSE;
	$enteredState = FALSE;

	//variables to keep track of the entered values

	$name = "";

	$city = "";
    $state = "";

	if (isset($_POST['enter'])) {

		//Name Posting

		if (isset($_POST['name']) && $_POST['name'] != "Type here...") {
			$stmt = $con->prepare("SELECT COUNT(*) as c FROM VENUES WHERE VenueName = ?");
            $stmt->execute(array(sqlReplace(trim($_POST['name']))));
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $row['c'];

            if ($count == 0) {
                $enteredName = TRUE;
			    $name = sqlReplace(trim($_POST['name']));
            } else {
                $enteredName = FALSE;
                $nameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>Venue already exists.</p>";
            }
		} else {
			//return to form
			$nameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a venue name!</p>";
		}

		//State Posting

		if (isset($_POST['state'])) {
			$state = trim($_POST['state']);

			$enteredState = TRUE;
		} else {
			//return to form
			$addrStateError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You need to choose a state!</p>";
		}

        if (isset($_POST['addrCity']) && $_POST['addrCity'] != "Type here...") {
            $city = trim($_POST['addrCity']);

            $enteredCity = TRUE;
        } else {
            //return to form
			$addrCityError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You have to choose a city.</p>";
        }

        if (isset($_POST['activeStatus'])) {
            $activeStatus = "YES";
        } else {
            $activeStatus = "NO";
        }

		if ($enteredName && $enteredCity && $enteredState) {
            $name = sqlReplace($name);

            $city = sqlReplace($city);
            $state = sqlReplace($state);

            $stmt = $con->prepare("INSERT INTO VENUES VALUES (NULL, ?, ?, ?, ?)");
            $stmt->execute(array($name, $city, $state, $activeStatus));
            $stmt->closeCursor();

            Header("Location:admin.php?source=venue");
        }
	}
?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add a new venue</title>
        <link rel="stylesheet" href="css/style.css" type="text/css">
        <link rel="stylesheet" type="text/css" href="css/mobile.css">
        <script src="js/mobile.js" type="text/javascript"></script>
    </head>

    <body>
            <div id="body">
                <!-- Top of Page (Not including nav) -->
                <div class="header">
                    <div class="contact">
                        <br><br>
                        <div class="<?php print $submittedClass?>" style="<?php print $hiddenStyle?>">
                            <h1 style="color:black">Add new venue</h1>
                            <!-- Code from Lab1 -->
                            <form id="addVenue" action="addVenue.php" method="post">
                                <!-- First Name (Left Hand) -->
                                <label for="name"><h3>Enter venue name <?php print $nameError?></h3></label>
                                <input type="text" name="name" value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';" required="true">

                                <label for="addrCity"><h3>Enter city <?php print $addrCityError?></h3></label>
                                <input type="text" name="addrCity"  value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';" required="true">

                                <label for="state"><h3>Choose state <?php print $addrStateError?></h3></label>
                                <select name="state">
                                    <?php print stateOptionList(); ?>
                                </select>

                                <br><br><br><br>

                                <!-- Active Status -->
                                <input style="position:absolute;bottom:195px;left:530px;" type="checkbox" name="activeStatus">
                                <p style="margin-top:-75px; width: 100%; max-width:100%; margin-left:15px;">Choose whether this venue will be active or not.</p>

                                <input style="width:96%" type="submit" name="enter" id="submit" value="Submit">
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