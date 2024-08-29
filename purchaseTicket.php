<?php

    session_start();

    include "header.php";

    if (!isset($_SESSION['email'])) {
        Header("Location:login.php");
    }

    $eid = '';

    if (isset($_GET['eid'])) {
        $eid = htmlspecialchars(trim($_GET['eid']));
    }

    if (!isset($_GET['source'])) {
        Header("Location:search.php");
    }

    $name = '';
    $priceper = 0;

    $stmt = $con->prepare("SELECT DISTINCT EventName, Price, Quantity FROM EVENTS, TICKETS WHERE EVENTS.EventID = ? AND EVENTS.EventID = TICKETS.EventID");
    $stmt->execute(array($eid));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $name = $row['EventName'];
    $priceper = $row['Price'];
	
?>

<!--
	Name: Christian Heatherly
	File name: purchaseTicket.php
	Date created: 09/30/22
	Date last modified: 10/20/22

	Sources:
        https://www.w3schools.com/html/tryit.asp?filename=tryhtml_input_number_step
-->

<?php

    function getTotal($price, $amt) {
        return $price * $amt;
    }

    $choseAmt = FALSE;
    $submitted = "";
    $hiddenClass = "hidden detailBox";
    $hiddenStyle = "margin-top:-415px;";
    $submittedClass = "detailBox";
    $quantError = "";

	//variables to keep track of the entered values

    $ticket_amt = 0;
    $eid2 = '';

	if (isset($_POST['enter'])) {

        if (isset($_POST['ticket_amt'])) {
			$ticket_amt = $_POST['ticket_amt'];

            $choseAmt = TRUE;
		}

        $eid2 = $_POST['eid2'];
        $priceper2 = $_POST['priceper2'];

		if ($choseAmt) {
            //update in SQL

            $total = $priceper2 * $ticket_amt;

            $stmt = $con->prepare("SELECT Quantity FROM TICKETS WHERE EventID = ?");
            $stmt->execute(array($eid2));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $quant = $row['Quantity'];

            $stmt->closeCursor();

            $new_quant = $quant - $ticket_amt;

            if ($new_quant >= 0) {

                //Update ticket quantity
                $stmt = $con->prepare("UPDATE TICKETS SET Quantity = ? WHERE EventID = ?");
                $stmt->execute(array($new_quant, $eid2));

                $stmt->closeCursor();

                //Grab ticket ID

                $stmt = $con->prepare("SELECT TicketID FROM TICKETS WHERE EventID = ?");
                $stmt->execute(array($eid2));

                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $ticketID = $row['TicketID'];

                $stmt->closeCursor();

                //Create new order

                $stmt = $con->prepare("INSERT INTO ORDERS VALUES (NULL, ?, ?, ?, ?)");
                $stmt->execute(array($_SESSION['uid'], $ticketID, $ticket_amt, date('Y-m-d')));

                $stmt->closeCursor();

                $submitted = "TRUE";
                $hiddenClass = "detailBox";
                $submittedClass = "hidden detailBox";

                Header ("Location:checkout.php?eid=".$eid2."&price=".$priceper2."&ticket_amt=".$ticket_amt."&total=".$total."&amt_left=".$new_quant."&source=purchase");
            } else {
                Header ("Location:purchaseTicket.php?eid=".$eid2."&source=no_stock");
            }
        } else {
            $ticket_amt = 1;
        }
	}
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
                        <div class="<?php print $hiddenClass?>">
                            <h2><?php print $ticket_amt?> tickets for <?php print $name?> were purchased for $<?php print getTotal($priceper2, $ticket_amt);?>. <!-- 1 will be replaced with the database price --> <?php print 0?> tickets are left.</h2>
                            <h2>Please return to your user page <a href="userInfo.php">here</a>.</h2>
                        </div>

                        <div class="<?php print $submittedClass?>" style="<?php print $hiddenStyle?>">
                            <h1 style="color:black">Event #<?php print $eid?>: <?php print $name?></h1>
                            <form id="tickets" action="purchaseTicket.php" method="post">

                                <input type="hidden" name="eid2" value="<?php print $eid?>">

                                <input type="hidden" name="priceper2" value="<?php print $priceper?>">

                                <label for="ticket_amt"><h3>Choose a ticket amount (Max 20)</h3></label>

                                <input type="number" id="ticket_amt" name="ticket_amt" min="1" max="20" step="1" value="1">

                                <input type="submit" name="enter" id="submit" value="Buy Now" style="width:50%; margin-left:25%; margin-bottom:5px;">
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