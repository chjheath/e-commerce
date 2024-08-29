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
	File name: chooseUser.php
	Date created: 11/04/22
	Date last modified: 11/05/22

	Sources:
-->

<?php

    function userOptionList() {
        //retrieve all users from database & insert them into a list

        $list = "";
        global $con; //sql connection

        $sql = "";

        if ($_SESSION['task'] == "edit") {
            $sql = "SELECT * FROM USERS";
        } else {
            $sql = "SELECT * FROM USERS WHERE Active = 'YES'";
        }
        $result = $con->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if ($row['Active'] == 'YES') {
                $list = $list . '<option value="'.$row["UserID"].'">#'.$row['UserID'].' '.$row["FirstName"].' '.$row['LastName'].' ('.$row['Email'].')</option>';
            } else {
                $list = $list . '<option value="'.$row["UserID"].'">(Inactive User) #'.$row['UserID'].' '.$row["FirstName"].' '.$row['LastName'].' ('.$row['Email'].')</option>';
            }
        }

        return $list;
    }

    if (isset($_POST['enter'])) {
        $_SESSION['chosen'] = $_POST['users'];

        
        
        if ($_SESSION['task'] == "remove") {
            Header("Location:removeUser.php");
        } else if ($_SESSION['task'] == "edit") {
            Header("Location:editUser.php");
        }
    }

	
?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Choose a user</title>
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
                            <h1 style="color:black">Select a user</h1>
                            <form id="chooseUser" action="chooseUser.php" method="post">
                                <select name="users">
                                    <?php print userOptionList(); ?>
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