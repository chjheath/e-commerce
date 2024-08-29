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
    $stateInfo = '';
    $msg = 'Are you sure you want to remove this state?';

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] !== '') {
        $chosenID = $_SESSION['chosen'];

        $stmt = $con->prepare("SELECT StateID, StateName, StateInitial FROM STATES WHERE StateID = ?");
        $stmt->execute(array($chosenID));

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $stateInfo = '<label><h3>State Info</h3></label>
                        <h4 style="margin-left:35px">State ID: #'.$row['StateID'].'</h4>
                        <h4 style="margin-left:35px">State Name: '.$row['StateName'].' ('.$row['StateInitial'].')</h4>
                      	
                        <hr/>';
    }

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] === '') {
        Header("Location:chooseState.php");
    }

    if (isset($_SESSION['task']) && $_SESSION['task'] === '') {
        Header("Location:admin.php");
    }
?>

<!--
	Name: Christian Heatherly
	File name: removeState.php
	Date created: 11/11/22
	Date last modified: 11/11/22

	Sources:
    -->

<?php

    if (isset($_POST['enter'])) { //form submitted

        $stateInfo = '';
        $msg = 'Successfully deleted state #'.$chosenID.' ';

        $stmt = $con->prepare("UPDATE STATES SET Active = ? WHERE StateID = ?");
        $stmt->execute(array('NO', $chosenID));

        $_SESSION['task'] = '';
        $_SESSION['chosen'] = '';

	    Header("Location:admin.php?source=removestate");
    }

?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Remove a state</title>
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
                            <h1 style="color:black">See state information below.</h1>
                            <!-- Code from Lab1 -->
                            <form id="removeState" action="removeState.php" method="post">

                                <?php print $stateInfo?>

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