<?php
    session_start();

	include "header.php";

    if (!isset($_SESSION['sid'])) {
        Header("Location:admin.php");
    }

    $stmt = $con->prepare("SELECT COUNT(AdminID) as c FROM ADMINS");
    $stmt->execute(array());

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalMsg = 'Total Number of Admins: '.$row['c'];

    $stmt->closeCursor();
?>

<!--
	Name: Christian Heatherly
	File name: listAdmins.php
	Date created: 10/22/22
	Date last modified: 12/02/22

	Sources:
        https://stackoverflow.com/questions/11620698/how-to-trigger-a-file-download-when-clicking-an-html-button-or-javascript
        https://www.geeksforgeeks.org/convert-timestamp-to-readable-date-time-in-php/
        https://www.geeksforgeeks.org/how-to-call-php-function-on-the-click-of-a-button/
-->

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>List all admins</title>
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

                function createReport() {
                    global $con;

                    $stmt = $con->prepare("SELECT AdminID, FirstName, LastName, Username, LastLogin, SuperAdmin, Active FROM ADMINS ORDER BY AdminID");
                    $stmt->execute(array());

                    $time = date('m-d-Y', time());

                    $fp = fopen("reports/adminData-".$time.".csv", "w");

                    fputcsv($fp, array("ID", "Name", "Username", "Super Admin?", "Last Login", "Active?"));

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $lastLogin = date('m/d/Y H:i:s', $row['LastLogin']);
                        
                        fputcsv($fp, array($row['AdminID'], $row['FirstName'].' '.$row['LastName'], $row['Username'], $row['SuperAdmin'], $lastLogin, $row['Active']));
                    }

                    fclose($fp);

                    Header("Location:reports/adminData-".$time.".csv");
                }

                function displayTable() {
                    global $con;

                    $stmt = $con->prepare("SELECT AdminID, FirstName, LastName, Username, LastLogin, SuperAdmin, Active FROM ADMINS ORDER BY AdminID");
                    $stmt->execute(array());

                    print '<table id="results" class="display" cellspacing="0" width="100%">';
                    print '<thead>
				                <th>ID</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Super Admin?</th>
                                <th>Active?</th>
                            </thead><tfoot>
			                <th>ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Super Admin?</th>
                            <th>Active?</th>
                        </tfoot>';

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        print '<tr>';
                        print '<td>'.$row['AdminID'].'</td><td>'.$row['FirstName'].' '.$row['LastName'].'</td><td>'.$row['Username'].'</td><td>'.$row['SuperAdmin'].'</td><td>'.$row['Active'].'</td>';
                    }

                    $stmt->closeCursor();

                    print '</table>';
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
                        <?php print $totalMsg?>
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