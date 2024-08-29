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
        Header("Location:chooseEvent.php");
    }

    if (isset($_SESSION['task']) && $_SESSION['task'] === '') {
        Header("Location:admin.php");
    }

    $stmt = $con->prepare("SELECT DISTINCT EVENTS.EventID as evid, EVENTS.EventName, EVENTS.EventDesc, GROUP_CONCAT(PerformerName) as PN, VenueName, EventTime, Price, Quantity, EVENTS.Active AS ea FROM EVENTS 
                            INNER JOIN TICKETS ON (TICKETS.TicketID = EVENTS.EventID)
                            INNER JOIN VENUES ON (VENUES.VenueID = EVENTS.VenueID)
                            INNER JOIN (PERFORMER_EVENTS INNER JOIN PERFORMERS ON (PERFORMER_EVENTS.PerformerID = PERFORMERS.PerformerID))
                            ON (PERFORMER_EVENTS.EventID = EVENTS.EventID)
                        WHERE EVENTS.EventID = ?
                        GROUP BY EVENTS.EventID
                        ORDER BY EVENTS.EventID");

    $stmt->execute(array($chosenID));

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $in_name = $row['EventName'];
    $in_desc = $row['EventDesc'];

    $in_perfs = $row['PN'];
    $in_perf_arr = explode(',', $in_perfs);

    if (count($in_perf_arr) != 1) {
        $in_pf1 = $in_perf_arr[0];
        $in_pf2 = $in_perf_arr[1];
    } else {
        $in_pf1 = $in_perf_arr[0];
        $in_pf2 = '0';
    }

    $in_date = $row['EventTime'];
    $in_price = $row['Price'];
    $in_quant = $row['Quantity'];
    $in_act = $row['ea'];

    $stmt->closeCursor(); //close current statement result
?>

<!--
	Name: Christian Heatherly
	File name: editEvent.php
	Date created: 11/12/22
	Date last modified: 11/13/22

	Sources:
        https://www.w3schools.com/tags/att_input_pattern.asp
        https://www.geeksforgeeks.org/split-a-comma-delimited-string-into-an-array-in-php/
        https://www.w3schools.com/php/func_array_count.asp
        https://stackoverflow.com/a/676703
-->

<?php


        $stmt = $con->prepare("SELECT SingleID, PerformerName FROM PERFORMER_EVENTS, PERFORMERS, EVENTS WHERE PERFORMER_EVENTS.PerformerID = PERFORMERS.PerformerID AND PERFORMER_EVENTS.EventID = EVENTS.EventID AND PERFORMER_EVENTS.EventID = ?");
        $stmt->execute(array($chosenID));

        $pev = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($pev, $row['SingleID']);
        }

    function performerOptionList($type, $arr, $perfID) {

        //retrieve all states from database & insert them into a list

        $list = "";
        global $con; //sql connection
        

        if ($type == '1') {
            $sql = "SELECT DISTINCT * FROM PERFORMERS WHERE PERFORMERS.Active = 'YES'";
            $result = $con->query($sql);

            $list = '<option value="0">N/A</option>';

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                if ($row['PerformerID'] == $perfID) {
                    $list = $list . '<option value ="'.$row["PerformerID"].'" selected>'.$row["PerformerName"].'</option>';
                } else {
                    $list = $list . '<option value ="'.$row["PerformerID"].'">'.$row["PerformerName"].'</option>';
                }
            }
        } else if ($type == '2') {
            $stmt = $con->prepare("SELECT * FROM PERFORMER_EVENTS WHERE SingleID = ?");
            $stmt->execute(array($arr[1]));


            $activeArr = $stmt->fetch(PDO::FETCH_ASSOC);
            $active = $activeArr['Active'];

            $stmt->closeCursor();

            $sql = "SELECT DISTINCT * FROM PERFORMERS WHERE PERFORMERS.Active = 'YES'";
            $result = $con->query($sql);

            if ($active == 'YES') {
                $list = '<option value="0">N/A</option>';

                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    if ($row['PerformerID'] == $perfID) {
                        $list = $list . '<option value ="'.$row["PerformerID"].'" selected>'.$row["PerformerName"].'</option>';
                    } else {
                        $list = $list . '<option value ="'.$row["PerformerID"].'">'.$row["PerformerName"].'</option>';
                    }
                }
            } else {
                $list = '<option value="0" selected>N/A</option>';

                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $list = $list . '<option value ="'.$row["PerformerID"].'">'.$row["PerformerName"].'</option>';
                }
            }
        }

        return $list;
    }

    function venueOptionList($venID) {
        //retrieve all states from database & insert them into a list

        $list = "";
        global $con; //sql connection

        $sql = "SELECT * FROM VENUES WHERE Active = 'YES'";
        $result = $con->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if ($row['VenueID'] == $venID) {
                $list = $list . '<option value ="'.$row["VenueID"].'" selected>'.$row["VenueName"].'</option>';
            } else {
                $list = $list . '<option value ="'.$row["VenueID"].'">'.$row["VenueName"].'</option>';
            }
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

	//boolean variables to keep track of if the user has entered a value

    $nameOK = TRUE;
    $descOK = TRUE;
    $dateOK = TRUE;
    $venueOK = TRUE;
    $ticketsOK = TRUE;
    $priceOK = TRUE;
    $perfOK = TRUE;

    $eventExists = FALSE;
    $checkedIfExists = FALSE;
    $sameEventOnSameDay = FALSE; //is name same & date same as an existing entry

    $isChangingName = FALSE;
    $isChangingDate = FALSE;

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

        //Name

        if (isset($_POST['eventName']) && $_POST['eventName'] !== '') {
            if (isset($_POST['eventName']) && $_POST['eventName'] !== $in_name) {
                $isChangingName = FALSE;

                $eventName = sqlReplace(trim($_POST['eventName']));
                $nameOK = TRUE;
            } else {
                $isChangingName = TRUE;

                $eventName = sqlReplace(trim($_POST['eventName']));
                $nameOK = TRUE;
            }
        } else {
            $nameOK = FALSE;
            $nameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter an event name!</p>";
        }

        //Description

        if (isset($_POST['eventDesc']) && $_POST['eventDesc'] !== '') {
            $eventDesc = sqlReplace(trim($_POST['eventDesc']));
            $descOK = TRUE;
        } else {
            $descOK = FALSE;
			$descError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a description of the event!</p>";
        }

        //Tickets

        if (isset($_POST['tickets'])) {
            $tickets = sqlReplace(trim($_POST['tickets']));
            $ticketsOK = TRUE;
        } else {
            $ticketsOK = FALSE;
            $ticketError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to choose an amount of tickets.</p>";
        }

        //Price
        
        if (isset($_POST['price'])) {
            $price = sqlReplace(trim($_POST['price']));
            $priceOK = TRUE;
        } else {
            $priceOK = FALSE;
            $priceError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to choose a price of tickets.</p>";
        }

        //Event Performer #1

        if (isset($_POST['eventPerf1'])) {
            $eventPerf1 = sqlReplace(trim($_POST['eventPerf1']));
        }

        //Event Performer #2

        if (isset($_POST['eventPerf2'])) {
            $eventPerf2 = sqlReplace(trim($_POST['eventPerf2']));
        }

        //Validate Performers

        if ($eventPerf1 != $eventPerf2) {
            $perfOK = TRUE;
        } else if ($eventPerf1 === $eventPerf2) {
            $perfOK = FALSE;
			$perfError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You cannot choose the same performer twice!</p>";
        } else if (($eventPerf1 == "" && $eventPerf2 != "") || ($eventPerf2 == "" && $eventPerf1 != "")) {
            $perfOK = FALSE;
            $perfError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You must choose at least one performer!</p>";
        }

        //Venue

        if (isset($_POST['eventVenue'])) {
            $eventVenue = sqlReplace(trim($_POST['eventVenue']));
            $venueOK = TRUE;
        }

        //Date

        if (isset($_POST['eventTime']) && $_POST['eventTime'] !== '') {
            $eventTime = sqlReplace(trim($_POST['eventTime']));
            $dateOK = TRUE;
        } else {
            $dateOK = FALSE;
            $eventTimeError = "<p style='color:red; margin-bottom:20px; margin-top:5px; margin-left:-175px;'>You are required to choose a time!</p>";
        }

        //Active

        if (isset($_POST['activeStatus'])) {
            $activeStatus = "YES";
        } else {
            $activeStatus = "NO";
        }

        if ($nameOK && $descOK && $dateOK && $venueOK && $priceOK && $perfOK) {
            $stmt = $con->prepare("UPDATE EVENTS SET EventName = ?, EventDesc = ?, EventTime = ?, VenueID = ?, Active = ? WHERE EventID = ?");
            $stmt->execute(array($eventName, $eventDesc, $eventTime, $eventVenue, $activeStatus, $chosenID));

            $stmt->closeCursor();

            if ($eventPerf2 == '0') {
                if (count($in_perf_arr) > 1) {
                    $stmt = $con->prepare("UPDATE PERFORMER_EVENTS SET PerformerID = ? WHERE EventID = ? AND SingleID = ?");
                    $stmt->execute(array($eventPerf1, $chosenID, $pev[0]));

                    $stmt = $con->prepare("UPDATE PERFORMER_EVENTS SET Active = ? WHERE EventID = ? AND SingleID = ?");
                    $stmt->execute(array('NO', $chosenID, $pev[1]));
                } else {
                    $stmt = $con->prepare("UPDATE PERFORMER_EVENTS SET PerformerID = ? WHERE EventID = ? AND SingleID = ?");
                    $stmt->execute(array($eventPerf1, $chosenID, $pev[0]));
                }
            } else {
                
                if (count($in_perf_arr) > 1) {
                    $stmt = $con->prepare("UPDATE PERFORMER_EVENTS SET PerformerID = ? WHERE EventID = ? AND SingleID = ?");
                    $stmt->execute(array($eventPerf1, $chosenID, $pev[0]));

                    $stmt->closeCursor();

                    $stmt = $con->prepare("UPDATE PERFORMER_EVENTS SET Active = ?, PerformerID = ? WHERE EventID = ? AND SingleID = ?");
                    $stmt->execute(array('YES', $eventPerf2, $chosenID, $pev[1]));
                } else {

                    $stmt = $con->prepare("UPDATE PERFORMER_EVENTS SET PerformerID = ? WHERE EventID = ? AND SingleID = ?");
                    $stmt->execute(array($eventPerf1, $chosenID, $pev[0]));
    
                    $stmt->closeCursor();

                    $stmt = $con->prepare("INSERT INTO PERFORMER_EVENTS VALUES (NULL, ?, ?, ?)");
                    $stmt->execute(array($chosenID, $eventPerf2, 'YES'));

                    $stmt->closeCursor();
                }
            }

            $stmt = $con->prepare("UPDATE TICKETS SET Price = ?, Quantity = ? WHERE EventID = ?");
            $stmt->execute(array($price, $tickets, $chosenID));

            Header("Location:admin.php?source=editevent");

        }
	}
?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit an event</title>
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
                            <h1 style="color:black">Edit an event</h1>
                            <form id="editEvent" action="editEvent.php" method="post">
                                <!-- Event Name -->
                                <label for="eventName"><h3>Update Event Name <?php print $nameError?></h3></label>
                                <input type="text" name="eventName" value="<?php print $in_name?>" onblur="this.value=!this.value?'':this.value;" onfocus="this.select()" onclick="this.value='';">
                                    
                                <!-- Event Description -->
                                <label for="eventDesc"><h3>Update Event Description <?php print $descError?></h3></label>
                                <input type="text" name="eventDesc" value="<?php print $in_desc?>" onblur="this.value=!this.value?'':this.value;" onfocus="this.select()" onclick="this.value='';">
                                
                                <!-- Event Performer #1 -->
                                <label for="eventPerf1"><h3>Select Event Performer #1</h3></label>

                                <select name="eventPerf1">

                                    <?php
                                        $stmt = $con->prepare("SELECT DISTINCT PERFORMERS.PerformerID as PID FROM PERFORMERS, PERFORMER_EVENTS, EVENTS WHERE PERFORMER_EVENTS.PerformerID = PERFORMERS.PerformerID AND PERFORMER_EVENTS.EventID = ? ORDER BY PERFORMERS.PerformerID ASC");
                                        $stmt->execute(array($chosenID));

                                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                                        print performerOptionList('1', $pev, $row['PID']);

                                        $stmt->closeCursor();
                                    ?>
                                </select>

                                <!-- Event Performer #2 -->
                                <label for="eventPerf2"><h3>Select Event Performer #2 <?php print $perfError?></h3></label>

                                <select name="eventPerf2">
                                    <?php
                     
                                            $stmt = $con->prepare("SELECT DISTINCT PERFORMERS.PerformerID as PID FROM PERFORMERS, PERFORMER_EVENTS, EVENTS WHERE PERFORMER_EVENTS.PerformerID = PERFORMERS.PerformerID AND PERFORMER_EVENTS.EventID = ? ORDER BY PERFORMERS.PerformerID DESC");
                                            $stmt->execute(array($chosenID));
    
                                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
                                            print performerOptionList('2', $pev, $row['PID']);
    
                                            $stmt->closeCursor();
                                    ?>
                                </select>
                            
                                <!-- Event Venue -->
                                <label for="eventVenue"><h3>Select Event Venue <?php print $venueError?></h3></label>

                                <select name="eventVenue">
                                    <?php
                                        $stmt = $con->prepare("SELECT EVENTS.VenueID FROM VENUES, EVENTS WHERE EVENTS.EventID = ? AND EVENTS.VenueID = VENUES.VenueID");
                                        $stmt->execute(array($chosenID));

                                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                                        print venueOptionList($row['VenueID']);

                                        $stmt->closeCursor();
                                    ?>
                                </select>
    
                                <!-- Event Time -->
                                <label for="eventTime"><h3>Update Event Date <?php print $eventTimeError?></h3> <h4 style="margin-top:-5px; margin-left:25px;">(Format: YYYY-MM-DD)</h4></label>
                                <input type="text" name="eventTime" value="<?php print $in_date?>" pattern="[0-9]{4}-[0-1]{1}[0-9]{1}-[0-3]{1}[0-9]{1}">

                                <!-- Tickets -->
                                <label for="tickets"><h3>Update Ticket Amount <?php print $ticketError?></h3></label>
                                <input type="number" name="tickets" value="<?php print $in_quant?>" min="0">

                                <label for="price"><h3>Update Ticket Price <?php print $priceError?></h3></label>
                                <input type="number" name="price" value="<?php print $in_price?>"min="0">

                                <br><br><br><br>

                                <!-- Active Status -->
                                <input style="position:absolute;bottom:95px;left:530px;" type="checkbox" name="activeStatus"<?php if ($in_act == 'YES') echo "checked='checked'";?>>
                                <p style="margin-top:-75px; width: 100%; max-width:100%; margin-left:15px;">Choose whether this event will be active or not.</p>

                                <input type="submit" name="enter" id="submit" value="Edit Event" style="width:96%;">
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