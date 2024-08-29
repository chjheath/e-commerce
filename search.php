<?php

    session_start();

	include "header.php";

    if (!isset($_SESSION['uid'])) { //if user not logged in anymore
        Header ("Location:login.php"); //send user to login automatically
    }
?>

<!--
	Name: Christian Heatherly
	File name: search.php
	Date created: 09/30/22
	Date last modified: 11/12/22

	Sources:
        https://stackoverflow.com/a/9233166
-->

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Search for an event</title>
        <link rel="stylesheet" href="css/style.css" type="text/css">
        <link rel="stylesheet" type="text/css" href="css/mobile.css">
        <script src="js/mobile.js" type="text/javascript"></script>

        <link rel="stylesheet" type="text/css" href="./media/css/jquery.dataTables.css">

	    <style type="text/css" class="init">
            label, #results_info {
                color: white;
            }
	    </style>
	
        <script type="text/javascript" language="javascript" src="./media/js/jquery.js"></script>
	    <script type="text/javascript" language="javascript" src="./media/js/jquery.dataTables.js"></script>
		<script type="text/javascript" language="javascript" class="init">
			$(document).ready(function(){
                $('#results').DataTable();
            });
		</script>
    </head>

    <body>
            <div id="body" class="about">
                <!-- Top of Page (Not including nav) -->
                <?php

                    $searchQuery = "";
                    $enteredQuery = FALSE;

                    function displayTable($sq, $eq) {
                        global $con;

                        if ($eq) {
                            $stmt = $con->prepare("SELECT DISTINCT EVENTS.EventID as evid, EVENTS.EventName, EVENTS.EventDesc, GROUP_CONCAT(PerformerName) as PN, VenueName, EventTime, Price, Quantity FROM EVENTS 
                            INNER JOIN TICKETS ON (TICKETS.TicketID = EVENTS.EventID)
                            INNER JOIN VENUES ON (VENUES.VenueID = EVENTS.VenueID)
                            INNER JOIN (PERFORMER_EVENTS INNER JOIN PERFORMERS ON (PERFORMER_EVENTS.PerformerID = PERFORMERS.PerformerID))
                            ON (PERFORMER_EVENTS.EventID = EVENTS.EventID)
                        WHERE (EVENTS.EventName LIKE '%$sq%' OR EVENTS.EventDesc LIKE '%$sq%' OR EVENTS.EventTime LIKE '%$sq%' OR VENUES.VenueName LIKE '%$sq%') AND (PERFORMER_EVENTS.ACTIVE = 'YES' AND EVENTS.Active = 'YES')
                        GROUP BY EVENTS.EventID
                        ORDER BY EVENTS.EventID");
                            $stmt->execute(array());

                            print '<table id="results" class="display" cellspacing="0" width="100%">';
                            print '<thead>
                                    <th>Event Name</th>
                                    <th>Event Date</th>
                                    <th>Performer(s)</th>
                                    <th>Venue</th>
                                    <th>Quantity Left</th>
                                    <th>Ticket Price</th>
                                    <th>Purchase</th>
                                </thead><tfoot>
                                <th>Event Name</th>
                                <th>Event Date</th>
                                <th>Performer(s)</th>
                                <th>Venue</th>
                                <th>Quantity Left</th>
                                <th>Ticket Price</th>
                                <th>Purchase</th>
                            </tfoot>';

                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                if ($row['Quantity'] == '0') {
                                    print '<tr>';
                                    print '<td>'.$row['EventName'].'</td><td>'.$row['EventTime'].'</td><td>'.$row['PN'].'</td><td>'.$row['VenueName'].'</td><td>'.$row['Quantity'].'</td><td>$'.$row['Price'].'</td><td><a>SOLD OUT</a></td>';
                                } else {
                                    print '<tr>';
                                    print '<td>'.$row['EventName'].'</td><td>'.$row['EventTime'].'</td><td>'.$row['PN'].'</td><td>'.$row['VenueName'].'</td><td>'.$row['Quantity'].'</td><td>$'.$row['Price'].'</td><td><a href="purchaseTicket.php?eid='.$row['evid'].'&source=search">Buy Now</a></td>';
                                }
                            }

                            $stmt->closeCursor();

                            print '</table>';
                        } else {
                            $stmt = $con->prepare("SELECT DISTINCT EVENTS.EventID as evid, EVENTS.EventName, EVENTS.EventDesc, GROUP_CONCAT(PerformerName) as PN, VenueName, EventTime, Price, Quantity FROM EVENTS 
                            INNER JOIN TICKETS ON (TICKETS.TicketID = EVENTS.EventID)
                            INNER JOIN VENUES ON (VENUES.VenueID = EVENTS.VenueID)
                            INNER JOIN (PERFORMER_EVENTS INNER JOIN PERFORMERS ON (PERFORMER_EVENTS.PerformerID = PERFORMERS.PerformerID))
                            ON (PERFORMER_EVENTS.EventID = EVENTS.EventID)
                        WHERE EVENTS.Active = 'YES' AND PERFORMER_EVENTS.Active = 'YES'
                        GROUP BY EVENTS.EventID
                        ORDER BY EVENTS.EventID");
                            $stmt->execute(array());

                            print '<table id="results" class="display" cellspacing="0" width="100%">';
                            print '<thead>
                                    <th>Event Name</th>
                                    <th>Event Date</th>
                                    <th>Performer(s)</th>
                                    <th>Venue</th>
                                    <th>Quantity Left</th>
                                    <th>Ticket Price</th>
                                    <th>Purchase</th>
                                </thead><tfoot>
                                <th>Event Name</th>
                                <th>Event Date</th>
                                <th>Performer(s)</th>
                                <th>Venue</th>
                                <th>Quantity Left</th>
                                <th>Ticket Price</th>
                                <th>Purchase</th>
                            </tfoot>';

                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                if ($row['Quantity'] == '0') {
                                    print '<tr>';
                                    print '<td>'.$row['EventName'].'</td><td>'.$row['EventTime'].'</td><td>'.$row['PN'].'</td><td>'.$row['VenueName'].'</td><td>'.$row['Quantity'].'</td><td>$'.$row['Price'].'</td><td><a>SOLD OUT</a></td>';
                                } else {
                                    print '<tr>';
                                    print '<td>'.$row['EventName'].'</td><td>'.$row['EventTime'].'</td><td>'.$row['PN'].'</td><td>'.$row['VenueName'].'</td><td>'.$row['Quantity'].'</td><td>$'.$row['Price'].'</td><td><a href="purchaseTicket.php?eid='.$row['evid'].'&source=search">Buy Now</a></td>';
                                }
                            }

                            $stmt->closeCursor();

                            print '</table>';
                        }
                    }

                    if (isset($_POST['enter'])) {
                        if (isset($_POST['searchQuery']) && $_POST['searchQuery'] != "Type here...") {
                            $searchQuery = trim($_POST['searchQuery']);
                            $enteredQuery = TRUE;
                        }

                        if ($enteredQuery) {
                            $searchQuery = sqlReplace($searchQuery);
                        }
                    }

                ?>

                <!-- Middle of page -->

                <div class="body" id="adminPage">
                    <div class="list">
                        <?php displayTable($searchQuery, $enteredQuery);?>
                    </div>
                </div>
            </div>

            <!-- Bottom of page -->

            <div id="footer">
                
            </div>
        </div>
    </body>
</html>