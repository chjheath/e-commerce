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
	File name: chooseEvent.php
	Date created: 11/11/22
	Date last modified: 11/11/22

	Sources:
-->

<?php

    function eventOptionList() {
        //retrieve all venues from database & insert them into a list

        $list = "";
        global $con; //sql connection

        $sql = "";

        if ($_SESSION['task'] == "edit") {
            $sql = "SELECT * FROM EVENTS";
        } else {
            $sql = "SELECT * FROM EVENTS WHERE Active = 'YES'";
        }

        $result = $con->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if ($row['Active'] == 'YES') {
                $list = $list . '<option value="'.$row["EventID"].'">#'.$row['EventID'].' '.$row['EventName'].'</option>';
            } else {
                $list = $list . '<option value="'.$row["EventID"].'">(Inactive Event) #'.$row['EventID'].' '.$row['EventName'].'</option>';
            }
        }

        return $list;
    }

    if (isset($_POST['enter'])) {
        $_SESSION['chosen'] = $_POST['events'];
        
        if ($_SESSION['task'] == "remove") {
            Header("Location:removeEvent.php");
        } else if ($_SESSION['task'] == "edit") {
            Header("Location:editEvent.php");
        }
    }

	
?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Choose an event</title>
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
                            <h1 style="color:black">Select an event</h1>
                            <form id="chooseEvent" action="chooseEvent.php" method="post">
                                <select name="events">
                                    <?php print eventOptionList(); ?>
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