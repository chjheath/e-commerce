<?php
    session_start();

	include "header.php";

    require_once 'js/validation.php';

    if (!isset($_SESSION['aid']) && !isset($_SESSION['sid'])) {
        Header("Location:index.php");
    }
?>

<!--
	Name: Christian Heatherly
	File name: addAdmin.php
	Date created: 09/30/22
	Date last modified: 11/13/22

	Sources:
-->

<?php

    function eventOptionList() {  	
        //retrieve all states from database & insert them into a list

        $list = "";
        global $con; //sql connection

        $sql = "SELECT EventID, EventName FROM EVENTS WHERE Active = 'YES'";
        $result = $con->query($sql);

        $list = '<option value=0>No event / N/A</option>';

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $list = $list . '<option value ="'.$row["EventID"].'">'.$row["EventName"].'</option>';
        }

        return $list;
    }

	//string variables to use when reporting specific errors to the user

    $nameError = "";

    $submitted = "";

    $hiddenClass = "hidden detailBox";
    $hiddenStyle = "margin-top:-15px;";
    $submittedClass = "detailBox";

	//boolean variables to keep track of if the user has entered a value

    $enteredName = FALSE;
    $doesntExist;
    $eventChoiceError = "";

	//variables to keep track of the entered values

    $perfName = "";
    $activeStatus = "";

    if (isset($_GET['error'])) {
        if ($_GET['error'] == 'exists') {
            echo '<p style="color: red; position: absolute; left: 730px; top: 285px;">Failed to add performer. Performer already exists.</p>';
        }
    }

	if (isset($_POST['enter'])) {

		//Name Posting

		if (isset($_POST['perfName']) && $_POST['perfName'] != "Type here...") {
			$enteredName = TRUE;
			$perfName = trim($_POST['perfName']);
		} else {
			//return to form
			$nameError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a performer name!</p>";
		}

        $event = trim($_POST['event']);

		if ($enteredName) {

            $perfName = sqlReplace($perfName);
            $event = sqlReplace($event);

            if (isset($_POST['activeStatus'])) {
                $activeStatus = "YES";
            } else {
                $activeStatus = "NO";
            }

            $stmt = "";
            $count = 0;

            if ($event == '0') {
                $stmt = $con->prepare("SELECT DISTINCT COUNT(*) as c FROM PERFORMERS WHERE PERFORMERS.PerformerName = ?");
            } else {
                $stmt = $con->prepare("SELECT DISTINCT COUNT(*) as c FROM PERFORMER_EVENTS, EVENTS, PERFORMERS WHERE (PERFORMERS.PerformerName = ? AND PERFORMER_EVENTS.PerformerID = PERFORMERS.PerformerID AND PERFORMER_EVENTS.EventID = EVENTS.EventID)");
            }

            $stmt->execute(array($perfName));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $count = $row['c'];

            if ($count == '0') {
                $doesntExist = TRUE;
            } else {
                $doesntExist = FALSE;
            }

            $stmt->closeCursor();

            //SQL Insertion

            if ($doesntExist) {
                if ($event == '0') {
                    $stmt = $con->prepare("INSERT INTO PERFORMERS VALUES (NULL, ?, ?)");
                    $stmt->execute(array($perfName, $activeStatus));
                    $stmt->closeCursor();
    
                    Header ("Location:admin.php?source=perf");
                } else {
                    $stmt = $con->prepare("INSERT INTO PERFORMERS VALUES (NULL, ?, ?)");
                    $stmt->execute(array($perfName, $activeStatus));
                    $stmt->closeCursor();

                    $stmt = $con->prepare("SELECT EventID FROM EVENTS WHERE EventID = ?");
                    $stmt->execute(array($event));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    $evid = $row['EventID'];

                    $stmt->closeCursor();

                    $stmt = $con->prepare("SELECT PerformerID FROM PERFORMERS WHERE PerformerName = ?");
                    $stmt->execute(array($perfName));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    $pid = $row['PerformerID'];
                    $stmt->closeCursor();

                    $stmt = $con->prepare("INSERT INTO PERFORMER_EVENTS VALUES (NULL, ?, ?)");
                    $stmt->execute(array($evid, $pid));
                    $stmt->closeCursor();

                    Header ("Location:admin.php?source=perf");
                }
            } else {
                Header("Location:addPerformer.php?error=exists&event=".$event."&count=".$count);
            }
        }
	}
?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add a new event</title>
        <link rel="stylesheet" href="css/style.css" type="text/css">
        <link rel="stylesheet" type="text/css" href="css/mobile.css">
        <script src="js/mobile.js" type="text/javascript"></script>
    </head>

    <body>
            <div id="body">
                <!-- Top of Page (Not including nav) -->
                <div class="header">
                    <div class="contact">
                        <div class="<?php print $submittedClass?>" style="<?php print $hiddenStyle?>">
                            <h1 style="color:black">Add new performer</h1>
                            <form id="addPerformer" action="addPerformer.php" method="post">
                                <!-- Performer Name -->
                                <label for="perfName"><h3>Enter Performer Name <?php print $nameError?></h3></label>
                                <input type="text" name="perfName" value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';" required="true">

                                <br>

                                <!-- Event Choice -->
                                <label for="event"><h3>Choose an event <?php print $eventChoiceError?></h3></label>
                                <select name="event">
                                    <?php print eventOptionList(); ?> 
                                </select>

                                <br><br><br><br>

                                <!-- Active Status -->
                                <input style="position:absolute;bottom:275px;left:530px;" type="checkbox" name="activeStatus">
                                <p style="margin-top:-75px; width: 100%; max-width:100%; margin-left:15px;">Choose whether this perf. will be active or not.</p>

                                <input type="submit" name="enter" id="submit" value="Add Perf." style="width:50%; margin-left:25%; margin-bottom:5px;">
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