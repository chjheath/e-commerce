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
	File name: addEvent.php
	Date created: 09/30/22
	Date last modified: 11/12/22

	Sources:
        https://www.w3schools.com/tags/att_input_pattern.asp
-->

<?php

    function performerOptionList() {
        //retrieve all states from database & insert them into a list

        $list = "";
        global $con; //sql connection

        $sql = "SELECT * FROM PERFORMERS WHERE Active = 'YES'";
        $result = $con->query($sql);
        
        $list = '<option value="">N/A</option>';

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $list = $list . '<option value ="'.$row["PerformerID"].'">'.$row["PerformerName"].'</option>';
        }

        return $list;
    }

    function venueOptionList() {
        //retrieve all states from database & insert them into a list

        $list = "";
        global $con; //sql connection

        $sql = "SELECT * FROM VENUES WHERE Active = 'YES'";
        $result = $con->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $list = $list . '<option value ="'.$row["VenueID"].'">'.$row["VenueName"].'</option>';
        }

        return $list;
    }

	//string variables to use when reporting specific errors to the user

    $nameError = "";
    $descError = "";
    $perfError = "";
    $eventTimeError = "";
    $ticketError = "";
    $venueError = "";
    $priceError = "";

    $submitted = "";

    $hiddenClass = "hidden detailBox";
    $hiddenStyle = "margin-top:-25px;";
    $submittedClass = "detailBox";

	//boolean variables to keep track of if the user has entered a value

    $enteredName = FALSE;
    $enteredDesc = FALSE;
    $choseTime = FALSE;
    $choseVenue = FALSE;
    $enteredPrice = FALSE;
    $performersNotSame = FALSE;

	//variables to keep track of the entered values

    $eventName = "";
    $eventDesc = "";
    $eventPerf1 = "";
    $eventPerf2 = "";
    $eventVenue = "";
    $eventTime = "";
    $price = "";
    $tickets = "";
    $activeStatus = "";

	if (isset($_POST['enter'])) {

		//Name Posting

		if (isset($_POST['eventName']) && $_POST['eventName'] != "Type here...") {
			$enteredName = TRUE;
			$eventName = trim($_POST['eventName']);
		} else {
			//return to form
			$nameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter an event name!</p>";
		}

        //Description Posting

		if (isset($_POST['eventDesc']) && $_POST['eventDesc'] != "Type here...") {
			$enteredDesc = TRUE;
			$eventDesc = trim($_POST['eventDesc']);
		} else {
			//return to form
			$descError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a description of the event!</p>";
		}

        if (isset($_POST['tickets'])) {
            $tickets = trim($_POST['tickets']);
            $enteredTickets = TRUE;
        } else {
            $ticketError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to choose an amount of tickets.</p>";
        }

        if (isset($_POST['price'])) {
            $price = trim($_POST['price']);
            $enteredPrice = TRUE;
        } else {
            $priceError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to choose a price of tickets.</p>";
        }

        if (isset($_POST['eventPerf1'])) {
			$eventPerf1 = trim($_POST['eventPerf1']);
		}

        if (isset($_POST['eventPerf2'])) {
			$eventPerf2 = trim($_POST['eventPerf2']);
		}

        if (isset($_POST['eventVenue'])) {
			$eventVenue = trim($_POST['eventVenue']);
            $choseVenue = TRUE;
		} else {
            $venueError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to choose a venue.</p>";
        }

        if (isset($_POST['eventTime']) && $_POST['eventTime'] != "") {
			$eventTime = trim($_POST['eventTime']);
            $choseTime = TRUE;
		} else if ($_POST['eventTime'] == "") {
            $eventTimeError = "<p style='color:red; margin-bottom:20px; margin-top:5px; margin-left:-175px;'>You are required to choose a time!</p>";
        }

        //Checking that performers are not the same

		if ($eventPerf1 != $eventPerf2) {
			$performersNotSame = TRUE;

		} else if ($eventPerf1 === $eventPerf2) {
            //return to form
			$perfError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You cannot choose the same performer twice!</p>";
			$performersNotSame = FALSE;
		} else if (($eventPerf1 == "" && $eventPerf2 != "") || ($eventPerf2 == "" && $eventPerf1 != "")) {
            $performersNotSame = FALSE;
            $perfError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You must choose at least one performer!</p>";
        }

        if (isset($_POST['activeStatus'])) {
            $activeStatus = "YES";
        } else {
            $activeStatus = "NO";
        }

		if ($enteredName && $enteredDesc && $choseTime && $performersNotSame && $choseVenue && $enteredTickets && $enteredPrice) {
            $eventName = sqlReplace($eventName);
            $eventDesc = sqlReplace($eventDesc);

            $eventPerf1 = sqlReplace($eventPerf1);
            $eventPerf2 = sqlReplace($eventPerf2);

            $eventVenue = sqlReplace($eventVenue);
            $eventTime = sqlReplace($eventTime);

            $tickets = sqlReplace($tickets);
            $price = sqlReplace($price);

            //SQL

            //Create new event
            $stmt = $con->prepare("INSERT INTO EVENTS VALUES (NULL, ?, ?, ?, ?, ?)");
            $stmt->execute(array($eventName, $eventDesc, $eventTime, $eventVenue, $activeStatus));
            $stmt->closeCursor();

            //Retrieve event ID of newly created event.

            $stmt = $con->prepare("SELECT EventID FROM EVENTS WHERE EventName = ? AND EventDesc = ? AND EventTime = ? AND VenueID = ?");
            $stmt->execute(array($eventName, $eventDesc, $eventTime, $eventVenue));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $eid = $row['EventID'];

            $stmt->closeCursor(); //close conn.

            if ($eventPerf2 == "") {
                //Assign performers

                $stmt = $con->prepare("INSERT INTO PERFORMER_EVENTS VALUES (NULL, ?, ?, ?)");
                $stmt->execute(array($eid, $eventPerf1, 'YES'));

                $stmt->closeCursor();
            } else {
                //Assign performers (1)

                $stmt = $con->prepare("INSERT INTO PERFORMER_EVENTS VALUES (NULL, ?, ?, ?)");
                $stmt->execute(array($eid, $eventPerf1, 'YES'));

                $stmt->closeCursor();

                //Assign performers (2)

                $stmt = $con->prepare("INSERT INTO PERFORMER_EVENTS VALUES (NULL, ?, ?, ?)");
                $stmt->execute(array($eid, $eventPerf2, 'YES'));

                $stmt->closeCursor();
            }

            //Create new tickets

            $stmt = $con->prepare("INSERT INTO TICKETS VALUES (NULL, ?, ?, ?, 'YES')");
            $stmt->execute(array($eid, $price, $tickets));
            $stmt->closeCursor();

            Header("Location:admin.php?source=event");
        }
	}
?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add a new event</title>
        <link rel="stylesheet" href="css/style.css" type="text/css">
        <link rel="stylesheet" type="text/css" href="css/mobile.css">
        <script src="js/mobile.js" type="text/javascript"></script>
    </head>

    <body>
            <div id="body">
                <!-- Top of Page (Not including nav) -->
                <div class="header">
                    <div class="contact">
                        <div class="<?php print $submittedClass?>" style="<?php print $hiddenStyle?>">
                            <h1 style="color:black">Add new event</h1>
                            <form id="addEvent" action="addEvent.php" method="post">
                                <!-- Event Name -->
                                <label for="eventName"><h3>Enter Event Name <?php print $nameError?></h3></label>
                                <input type="text" name="eventName" value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';" required="true">
                                    
                                <!-- Event Description -->
                                <label for="eventDesc"><h3>Enter Event Description <?php print $descError?></h3></label>
                                <input type="text" name="eventDesc" value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';" required>
                                
                                <!-- Event Performer #1 -->
                                <label for="eventPerf1"><h3>Select Event Performer #1</h3></label>

                                <select name="eventPerf1">
                                    <?php print performerOptionList(); ?>
                                </select>

                                <!-- Event Performer #2 -->
                                <label for="eventPerf2"><h3>Select Event Performer #2 <?php print $perfError?></h3></label>

                                <select name="eventPerf2">
                                    <?php print performerOptionList(); ?>
                                </select>
                            
                                <!-- Event Venue -->
                                <label for="eventVenue"><h3>Select Event Venue <?php print $venueError?></h3></label>

                                <select name="eventVenue">
                                    <?php print venueOptionList(); ?>
                                </select>
    
                                <!-- Event Time -->
                                <label for="eventTime"><h3>Choose Event Date <h4 style="margin-top:-5px; margin-left:25px;">(Format: YYYY-MM-DD)</h4><?php print $eventTimeError?></h3></label>
                                <input type="text" name="eventTime" pattern="[0-9]{4}-[0-1]{1}[0-9]{1}-[0-3]{1}[0-9]{1}">

                                <!-- Tickets -->
                                <label for="tickets"><h3>Choose Ticket Amount <?php print $ticketError?></h3></label>
                                <input type="number" name="tickets" min="0">

                                <label for="price"><h3>Choose Ticket Price <?php print $priceError?></h3></label>
                                <input type="number" name="price" min="0">

                                <br><br><br><br>

                                <!-- Active Status -->
                                <input style="position:absolute;bottom:80px;left:530px;" type="checkbox" name="activeStatus">
                                <p style="margin-top:-75px; width: 100%; max-width:100%; margin-left:15px;">Choose whether this event will be active or not.</p>

                                <input type="submit" name="enter" id="submit" value="Add Event" style="width:50%; margin-left:25%; margin-bottom:5px;">
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