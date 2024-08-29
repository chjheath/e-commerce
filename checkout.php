 <?php

    session_start();

    if (!isset($_SESSION['email'])) {
        Header("Location:login.php");
    }

    if (!isset($_GET['source'])) {
        Header("Location:search.php");
    }

	include "header.php";
?>

<!--
	Name: Christian Heatherly
	File name: checkout.php
	Date created: 09/30/22
	Date last modified: 10/20/22

	Sources:
        https://www.w3schools.com/html/tryit.asp?filename=tryhtml_input_number_step
-->

<?php

    $eid = '';

    if (isset($_GET['eid'])) {
        $eid = sqlReplace(trim($_GET['eid']));
    }

    $name = '';
    $priceper = 0;

    $stmt = $con->prepare("SELECT DISTINCT EventName, Price FROM EVENTS, TICKETS WHERE EVENTS.EventID = ?");
    $stmt->execute(array($eid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        print "failed";
    }

    $name = $row['EventName'];
    $priceper = $row['Price'];

	//variables to keep track of the entered values

    $ticket_amt = 0;

    $ticket_amt = $_GET['ticket_amt'];

    $total = $_GET['total'];

    $left = $_GET['amt_left'];


    //count of tickets left after updates

?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Purchase Tickets</title>
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
                            <h2><?php print $ticket_amt?> tickets for <?php print $name?> were purchased for $<?php print $total?>. <!-- 1 will be replaced with the database price --> <?php print $left?> tickets are left.</h2>
                            <h2>Please return to your user page <a href="userInfo.php">here</a>.</h2>
                        </div>
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