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
    $adminInfo = '';
    $msg = 'Are you sure you want to remove this admin?';

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] !== '') {
        $chosenID = $_SESSION['chosen'];

        $stmt = $con->prepare("SELECT AdminID, FirstName, LastName, Username, SuperAdmin FROM ADMINS WHERE AdminID = ?");
        $stmt->execute(array($chosenID));

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $adminInfo = '<label><h3>Admin Info</h3></label>
                        <h4 style="margin-left:35px">Admin ID: #'.$row['AdminID'].'</h4>
                        <h4 style="margin-left:35px">Name: '.$row['FirstName'].' '.$row['LastName'].'</h4>
                        <h4 style="margin-left:35px">Username: '.$row['Username'].'</h4>
                        <h4 style="margin-left:35px">Super Admin?: '.$row['SuperAdmin'].'</h4>
                        
                        <hr/>';
    }

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] === '') {
        Header("Location:chooseAdmin.php");
    }

    if (isset($_SESSION['task']) && $_SESSION['task'] === '') {
        Header("Location:admin.php");
    }
?>

<!--
	Name: Christian Heatherly
	File name: removeAdmin.php
	Date created: 11/12/22
	Date last modified: 11/12/22

	Sources:
    -->

<?php

    if (isset($_POST['enter'])) { //form submitted

        $adminInfo = '';
        $msg = 'Successfully deleted admin #'.$chosenID.' ';

        $stmt = $con->prepare("UPDATE ADMINS SET Active = ? WHERE AdminID = ?");
        $stmt->execute(array('NO', $chosenID));

        $_SESSION['task'] = '';
        $_SESSION['chosen'] = '';

	    Header("Location:admin.php?source=removeadmin");
    }

?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Remove an admin</title>
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
                            <h1 style="color:black">See admin information below.</h1>
                            <!-- Code from Lab1 -->
                            <form id="removeAdmin" action="removeAdmin.php" method="post">

                                <?php print $adminInfo?>

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