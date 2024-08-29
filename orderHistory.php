<?php

    session_start();

	include "header.php";

    if (!isset($_SESSION['email'])) { //if user not logged in anymore
        Header ("Location:login.php"); //send user to login automatically
    }
?>

<!--
	Name: Christian Heatherly
	File name: orderHistory.php
	Date created: 10/20/22
	Date last modified: 11/30/22

	Sources:
-->

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View order history</title>
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

        <?php

            function displayTable() {
                global $con;

                $stmt = $con->prepare("SELECT ORDERS.OrderID, ORDERS.UserID, ORDERS.TicketID, ORDERS.Quantity, ORDERS.PurchaseDate, TICKETS.TicketID, TICKETS.Price, EVENTS.EventID, EVENTS.EventName FROM ORDERS, TICKETS, EVENTS WHERE ORDERS.UserID = ? AND (ORDERS.TicketID = TICKETS.TicketID AND TICKETS.EventID = EVENTS.EventID) ORDER BY ORDERS.OrderID");
                $stmt->execute(array($_SESSION['uid']));

                print '<table id="results" class="display" cellspacing="0" width="100%">';
                print '<thead>
                        <th>Order ID</th>
                        <th>Event Name</th>
                        <th>Date Purchased</th>
                        <th>Quantity Purchased</th>
                        <th>Total Price.</th>
                    </thead><tfoot>
                        <th>Order ID</th>
                        <th>Event Name</th>
                        <th>Date Purchased</th>
                        <th>Quantity Purchased</th>
                        <th>Total Price.</th>
                    </tfoot>';

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    print '<tr>';
                    print '<td>'.$row['OrderID'].'</td><td>'.$row['EventName'].'</td><td>'.$row['PurchaseDate'].'</td><td>'.$row['Quantity'].'</td><td>$'.($row['Quantity'] * $row['Price']).'</td>';
                }

                $stmt->closeCursor();

                print '</table>';
            }

            ?>

                <!-- Middle of page -->

                <div class="body" id="adminPage"> <!-- I know this isn't an admin page, just used for styling -->
                    <div class="list">
                        <?php displayTable();?>
                    </div>
                </div>
            </div>

            <!-- Bottom of page -->

            <div id="footer">
                
            </div>
        </div>
    </body>
</html>