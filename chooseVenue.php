<?php
    session_start();

    include "header.php";

    require_once 'js/validation.php';

    if (!isset($_SESSION['aid']) && !isset($_SESSION['sid'])) {
        Header("Location:index.php");
    }

    if (isset($_GET['task'])) {
        $_SESSION['task'] = $_GET['task'];
    }

?>

<!--
	Name: Christian Heatherly
	File name: chooseVenue.php
	Date created: 11/10/22
	Date last modified: 11/10/22

	Sources:
-->

<?php

    function venueOptionList() {
        //retrieve all venues from database & insert them into a list

        $list = "";
        global $con; //sql connection

        $sql = "";

        if ($_SESSION['task'] == "edit") {
            $sql = "SELECT * FROM VENUES";
        } else {
            $sql = "SELECT * FROM VENUES WHERE Active = 'YES'";
        }

        $result = $con->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if ($row['Active'] == 'YES') {
                $list = $list . '<option value="'.$row["VenueID"].'">#'.$row['VenueID'].' '.$row['VenueName'].'</option>';
            } else {
                $list = $list . '<option value="'.$row["VenueID"].'">(Inactive Venue) #'.$row['VenueID'].' '.$row['VenueName'].'</option>';
            }
        }

        return $list;
    }

    if (isset($_POST['enter'])) {
        $_SESSION['chosen'] = $_POST['venues'];

        
        
        if ($_SESSION['task'] == "remove") {
            Header("Location:removeVenue.php");
        } else if ($_SESSION['task'] == "edit") {
            Header("Location:editVenue.php");
        }
    }

	
?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Choose a venue</title>
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
                            <h1 style="color:black">Select a venue</h1>
                            <form id="chooseVenue" action="chooseVenue.php" method="post">
                                <select name="venues">
                                    <?php print venueOptionList(); ?>
                                </select>
                                <input type="submit" name="enter" id="submit" value="Continue" style="width:50%; margin-left:25%; margin-bottom:5px;">
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