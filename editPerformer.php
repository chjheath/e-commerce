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

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] !== '') {
        $chosenID = $_SESSION['chosen'];
    }

    if (isset($_SESSION['chosen']) && $_SESSION['chosen'] === '') {
        Header("Location:choosePerformer.php");
    }

    if (isset($_SESSION['task']) && $_SESSION['task'] === '') {
        Header("Location:admin.php");
    }

    $stmt = $con->prepare("SELECT PerformerName, Active FROM PERFORMERS WHERE PerformerID = ?");
    $stmt->execute(array($chosenID));

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $in_name = $row['PerformerName'];
    $in_act = $row['Active'];

    $stmt->closeCursor(); //close current statement result
?>

<!--
	Name: Christian Heatherly
	File name: editPerformer.php
	Date created: 11/10/22
	Date last modified: 11/12/22

	Sources:
    -->

<?php

    //variables to keep track of states, errors, etc.

    $perfNameError = '';

    $perfNameOK = TRUE;
    $performerExists = FALSE;

    $perfName = '';
    
    $boxPos = 'position:absolute;bottom:400px;left:530px;';

    $activeStatus = '';

    if (isset($_POST['enter'])) { //form submitted
        if (isset($_POST['perfName']) && ($_POST['perfName'] !== "Type here..." && $_POST['perfName'] !== "")) {

            if (isset($_POST['perfName']) && $_POST['perfName'] !== $in_name) {
                $stmt = $con->prepare("SELECT COUNT(PerformerName) as c FROM PERFORMERS WHERE PerformerName = ?");
                $stmt->execute(array($_POST['perfName']));
    
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $count = $row['c'];
    
                if ($count == 0) {
                    $perfName = sqlReplace(trim($_POST['perfName']));
                    $perfNameOK = TRUE;
                } else {
                    $performerExists = TRUE;
                    $perfNameOK = FALSE;
                    $perfNameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>That performer name already exists.</p>";
                }
            } else if (isset($_POST['perfName']) && $_POST['perfName'] == $in_name) {
                $perfName = sqlReplace(trim($_POST['perfName']));
                $perfNameOK = TRUE;
            }

            
        } else {
            $perfNameOK = FALSE;
            $perfNameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a new name! If you don't want to change their name, do not touch the textbox.</p>";
        }

        if (isset($_POST['activeStatus'])) {
            $activeStatus = 'YES';
        } else {
            $activeStatus = 'NO';
        }

        if ($perfNameOK) {
            $stmt = $con->prepare("UPDATE PERFORMERS SET PerformerName = ?, Active = ? WHERE PerformerID = ?");
            $stmt->execute(array($perfName, $activeStatus, $chosenID));
            $stmt->closeCursor();

            Header("Location:admin.php?source=editperf");
        } else if (!$perfNameOK && $performerExists) {
            $boxPos = 'position:absolute;bottom:380px;left:530px;';
        } else {
            $boxPos = 'position:absolute;bottom:360px;left:530px;';
        }

    }

?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Update performer information</title>
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
                            <h1 style="color:black">Enter their information below.</h1>
                            <!-- Code from Lab1 -->
                            <form id="updateInfo" action="editPerformer.php" method="post">
                                <!-- Name -->
                                <label for="perfName"><h3>Update their name <?php print $perfNameError?></h3></label>
                                <input type="text" name="perfName" onfocus="this.select()" value="<?php print $in_name?>">
                           
                                <br><br><br><br>

                                <!-- Active? -->
                                <input style="<?php print $boxPos?>" type="checkbox" name="activeStatus" <?php if ($in_act == 'YES') echo "checked='checked'";?>> <!-- https://stackoverflow.com/a/16239755 -->
                                <p style="margin-top:-75px; width: 100%; max-width:100%; margin-left:15px;">Choose whether this performer is active or not.</p>

                                <input type="submit" name="enter" id="submit" value="Submit" style="width:95%">
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