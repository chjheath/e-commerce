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
    $userInfo = '';
    $msg = 'Are you sure you want to remove this user?';

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] !== '') {
        $chosenID = $_SESSION['chosen'];

        $stmt = $con->prepare("SELECT UserID, FirstName, LastName, Email FROM USERS WHERE UserID = ?");
        $stmt->execute(array($chosenID));

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $userInfo = '<label><h3>User Info</h3></label>
                        <h4 style="margin-left:35px">User ID: #'.$row['UserID'].'</h4>
                        <h4 style="margin-left:35px">Name: '.$row['FirstName'].' '.$row['LastName'].'</h4>
                        <h4 style="margin-left:35px">Email: '.$row['Email'].'</h4>
                        
                        <hr/>';
    }

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] === '') {
        Header("Location:chooseUser.php");
    }

    if (isset($_SESSION['task']) && $_SESSION['task'] === '') {
        Header("Location:admin.php");
    }
?>

<!--
	Name: Christian Heatherly
	File name: removeUser.php
	Date created: 11/03/22
	Date last modified: 11/10/22

	Sources:
    -->

<?php

    if (isset($_POST['enter'])) { //form submitted

        $userInfo = '';
        $msg = 'Successfully deleted user #'.$chosenID.' ';

        $stmt = $con->prepare("UPDATE USERS SET Active = ? WHERE UserID = ?");
        $stmt->execute(array('NO', $chosenID));

        $_SESSION['task'] = '';
        $_SESSION['chosen'] = '';

	Header("Location:admin.php?source=removereg");
    }

?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Remove a user</title>
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
                            <h1 style="color:black">See user information below.</h1>
                            <!-- Code from Lab1 -->
                            <form id="removeUser" action="removeUser.php" method="post">

                                <?php print $userInfo?>

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