<?php
    session_start();

    include "header.php";

    if (!isset($_SESSION['aid']) && !isset($_SESSION['sid'])) {
        Header("Location:index.php");
    }

    $stmt = $con->prepare("SELECT SUM(NumEvents) as c FROM EVENTS_PER_PERFORMER");
    $stmt->execute(array());

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalEvents = 'Combined Number of Events Scheduled: '.$row['c'];

    $stmt->closeCursor();

    $stmt = $con->prepare("SELECT COUNT(PerformerID) as c FROM EVENTS_PER_PERFORMER");
    $stmt->execute(array());

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalPerf = 'Total Number of Performers: '.$row['c'];

    $stmt->closeCursor();
?>

<!--
	Name: Christian Heatherly
	File name: viewPerf.php
	Date created: 12/02/22
	Date last modified: 12/02/22

	Sources:
-->

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>List events per performer</title>
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

                global $con;

                function createReport() {
                    global $con;
                    $stmt = $con->prepare("SELECT * FROM EVENTS_PER_PERFORMER");
                    $stmt->execute(array());

                    $time = date('m-d-Y', time());

                    $fp = fopen("reports/perfReport-".$time.".csv", "w");

                    fputcsv($fp, array("ID", "Name", "Number of Events"));

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        fputcsv($fp, array($row['PerformerID'], $row['PerformerName'], $row['NumEvents']));
                    }

                    $stmt->closeCursor();

                    $stmt = $con->prepare("SELECT SUM(NumEvents) as c FROM EVENTS_PER_PERFORMER");
                    $stmt->execute(array());

                    fputcsv($fp, array()); //blank line

                    fputcsv($fp, array("Total Events")); //add new column for total rev.

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        fputcsv($fp, array($row['c'])); //insert revenue
                    }

                    $stmt->closeCursor();

                    fclose($fp);

                    Header("Location:reports/perfReport-".$time.".csv");
                }

                function displayTable() {
                    global $con;

                    $stmt = $con->prepare("SELECT * FROM EVENTS_PER_PERFORMER");
                    $stmt->execute(array());

                    print '<table id="results" class="display" cellspacing="0" width="100%">';
                    print '<thead>
                                <th>ID</th>
                                <th>Performer Name</th>
                                <th>Number of Scheduled Events</th>
                            </thead><tfoot>
                                <th>ID</th>
                                <th>Performer Name</th>
                                <th>Number of Scheduled Events</th>
                        </tfoot>';
    
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        print '<tr>';
                        print '<td>'.$row['PerformerID'].'</td><td>'.$row['PerformerName'].'</td><td>'.$row['NumEvents'].'</td>';
                    }
    
                    $stmt->closeCursor();
    
                    print '</tr></table>';
                }

                if (isset($_POST['dwnld'])) {
                    createReport();
                }

                ?>

                <!-- Middle of page -->

                <div class="body" id="adminPage">
                    <div class="list">
                        <form method="post">
                            <!-- Used this to see if I had to submit a form or if I could just use the name of the element https://www.geeksforgeeks.org/how-to-call-php-function-on-the-click-of-a-button/ -->
                            <input type="submit" name="dwnld" style="margin-left:89%;" value="Download Report">
                        </form>
                        <?php print $totalEvents?>
                        <br><br>
                        <?php print $totalPerf?>
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