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
    $eventInfo = '';
    $msg = 'Are you sure you want to remove this event?';

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] !== '') {
        $chosenID = $_SESSION['chosen'];

        $stmt = $con->prepare("SELECT DISTINCT EVENTS.EventID as evid, EVENTS.EventName, GROUP_CONCAT(PerformerName) as PN, VenueName, EventTime FROM EVENTS 
                            INNER JOIN TICKETS ON (TICKETS.TicketID = EVENTS.EventID)
                            INNER JOIN VENUES ON (VENUES.VenueID = EVENTS.VenueID)
                            INNER JOIN (PERFORMER_EVENTS INNER JOIN PERFORMERS ON (PERFORMER_EVENTS.PerformerID = PERFORMERS.PerformerID))
                            ON (PERFORMER_EVENTS.EventID = EVENTS.EventID)
                        WHERE EVENTS.EventID = ?
                        GROUP BY EVENTS.EventID
                        ORDER BY EVENTS.EventID");

        $stmt->execute(array($chosenID));

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $eventInfo = '<label><h3>Event Info</h3></label>
                        <h4 style="margin-left:35px">Event ID: #'.$row['evid'].'</h4>
                        <h4 style="margin-left:35px">Name: '.$row['EventName'].'</h4>
                        <h4 style="margin-left:35px">Venue: '.$row['VenueName'].'</h4>
                        <h4 style="margin-left:35px">Date: '.$row['EventTime'].'</h4>
                        <h4 style="margin-left:35px">Performer(s): '.$row['PN'].'</h4>
                        
                        <hr/>';

    }

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] === '') {
        Header("Location:chooseEvent.php");
    }

    if (isset($_SESSION['task']) && $_SESSION['task'] === '') {
        Header("Location:admin.php");
    }
?>

<!--
	Name: Christian Heatherly
	File name: removeEvent.php
	Date created: 11/12/22
	Date last modified: 11/12/22

	Sources:
    -->

<?php

    if (isset($_POST['enter'])) { //form submitted

        $eventInfo = '';
        $msg = 'Successfully deleted event #'.$chosenID.' ';

        $stmt = $con->prepare("UPDATE EVENTS SET Active = ? WHERE EventID = ?");
        $stmt->execute(array('NO', $chosenID));

        $_SESSION['task'] = '';
        $_SESSION['chosen'] = '';

	    Header("Location:admin.php?source=removeevent");
    }

?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Remove an event</title>
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
                            <h1 style="color:black">See event information below.</h1>
                            <!-- Code from Lab1 -->
                            <form id="removeEvent" action="removeEvent.php" method="post">

                                <?php print $eventInfo?>

                                <label><h3 style="text-align:center"><?php print $msg?></h3></label>

                                <input type="submit" name="enter" id="submit" value="Remove">
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