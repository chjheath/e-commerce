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
    $venInfo = '';
    $msg = 'Are you sure you want to remove this venue?';

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] !== '') {
        $chosenID = $_SESSION['chosen'];

        $stmt = $con->prepare("SELECT VenueID, VenueName, City, StateID FROM VENUES WHERE VenueID = ?");
        $stmt->execute(array($chosenID));

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $venInfo = '<label><h3>Venue Info</h3></label>
                        <h4 style="margin-left:35px">Venue ID: #'.$row['VenueID'].'</h4>
                        <h4 style="margin-left:35px">Venue Name: '.$row['VenueName'].'</h4>
                      	
                        <hr/>';
    }

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] === '') {
        Header("Location:chooseVenue.php");
    }

    if (isset($_SESSION['task']) && $_SESSION['task'] === '') {
        Header("Location:admin.php");
    }
?>

<!--
	Name: Christian Heatherly
	File name: removeVenue.php
	Date created: 11/11/22
	Date last modified: 11/11/22

	Sources:
    -->

<?php

    if (isset($_POST['enter'])) { //form submitted

        $venInfo = '';
        $msg = 'Successfully deleted venue #'.$chosenID.' ';

        $stmt = $con->prepare("UPDATE VENUES SET Active = ? WHERE VenueID = ?");
        $stmt->execute(array('NO', $chosenID));

        $_SESSION['task'] = '';
        $_SESSION['chosen'] = '';

	    Header("Location:admin.php?source=removevenue");
    }

?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Remove a venue</title>
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
                            <h1 style="color:black">See venue information below.</h1>
                            <!-- Code from Lab1 -->
                            <form id="removeVenue" action="removeVenue.php" method="post">

                                <?php print $venInfo?>

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