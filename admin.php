<?php
    session_start();

	include "header.php";

    $super = '';

    if (isset($_SESSION['sid'])) {
        $super = '<h1>Manage admins</h1>
        <ul>
            <li>
                <a href="addAdmin.php">ADD ADMIN</a>
                <a href="chooseAdmin.php?task=edit">EDIT ADMINS</a>
                <a href="chooseAdmin.php?task=remove">REMOVE ADMIN</a>
                <a href="listAdmins.php">LIST ADMINS</a>
            </li>
        </ul>';
    }

    if (!isset($_SESSION['aid']) && !isset($_SESSION['sid'])) {
        Header("Location:index.php");
    }

    if (isset($_SESSION['chosen'])) {
        $_SESSION['chosen'] = '';
    }

    if (isset($_SESSION['task'])) {
        $_SESSION['task'] = '';
    }

    $updatedMsg = '';

    if (isset($_GET['source'])) {
        if ($_GET['source'] == 'reg') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully added new user.</p>';
        } else if ($_GET['source'] == 'perf') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully added new performer.</p>';
        } else if ($_GET['source'] == 'event') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully added new event.</p>';
        } else if ($_GET['source'] == 'venue') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully added new venue.</p>';
        } else if ($_GET['source'] == 'admin' && isset($_SESSION['sid'])) {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully added new admin.</p>';
        } else if ($_GET['source'] == 'editreg') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully edited user.</p>';
        } else if ($_GET['source'] == 'editperf') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully edited performer.</p>';
        } else if ($_GET['source'] == 'editevent') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully edited event.</p>';
        } else if ($_GET['source'] == 'editvenue') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully edited venue.</p>';
        } else if ($_GET['source'] == 'editadmin' && isset($_SESSION['sid'])) {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully edited admin.</p>';
        } else if ($_GET['source'] == 'removereg') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully removed user.</p>';
        } else if ($_GET['source'] == 'removeperf') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully removed performer.</p>';
        } else if ($_GET['source'] == 'removeevent') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully removed event.</p>';
        } else if ($_GET['source'] == 'removevenue') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully removed venue.</p>';
        } else if ($_GET['source'] == 'removeadmin' && isset($_SESSION['sid'])) {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully removed admin.</p>';
        } else if ($_GET['source'] == 'addstate') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully added state.</p>';
        } else if ($_GET['source'] == 'editstate') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully edited state.</p>';
        } else if ($_GET['source'] == 'removestate') {
            echo '<p style="color: green; position: absolute; left: 1430px; top: 325px; font-size:24px;">Successfully removed state.</p>';
        } else if ($_GET['source'] === 'upd') {
            $updatedMsg = '<h2>See your updated information below</h2><hr>';
        }
    }
?>

<!--
	Name: Christian Heatherly
	File name: admin.php
	Date created: 09/30/22
	Date last modified: 12/02/22

	Sources:
-->

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manage website</title>
        <link rel="stylesheet" href="css/style.css" type="text/css">
        <link rel="stylesheet" type="text/css" href="css/mobile.css">
        <script src="js/mobile.js" type="text/javascript"></script>
    </head>

    <body>
            <div id="body" class="home">
                <!-- Top of Page (Not including nav) -->
                <div class="header">

                </div>

                <!-- Middle of page -->

                <div class="body" id="adminPage">
                    <?php print $updatedMsg?>

                    <h1>Manage yourself</h1>
                    <ul>
                        <li>
                            <a href="adminInfo.php">VIEW INFO</a>
                            <a href="adminUpdateInfo.php">UPDATE INFO</a>
                            <a href="adminChangePwd.php">CHANGE PWD</a>
                        </li>
                    </ul>

                    <br><br>
                    <hr/>

                    <h1>Manage users</h1>
                    <ul>
                        <li>
                            <a href="addUser.php">ADD USER</a>
                            <a href="chooseUser.php?task=edit">EDIT USERS</a>
                            <a href="chooseUser.php?task=remove">REMOVE USER</a>
                            <a href="listUsers.php">LIST USERS</a>
                        </li>
                    </ul>

                    <h1>Manage performers</h1>
                    <ul>
                        <li>
                            <a href="addPerformer.php">ADD PERF...</a>
                            <a href="choosePerformer.php?task=edit">EDIT PERF...</a>
                            <a href="choosePerformer.php?task=remove">REMOVE PERF...</a>
                            <a href="listPerformers.php">LIST PERF...</a>
                        </li>
                    </ul>

                    <h1>Manage events</h1>
                    <ul>
                        <li>
                            <a href="addEvent.php">ADD EVENT</a>
                            <a href="chooseEvent.php?task=edit">EDIT EVENTS</a>
                            <a href="chooseEvent.php?task=remove">REMOVE EVENT</a>
                            <a href="listEvents.php">LIST EVENTS</a>
                        </li>
                    </ul>

                    <h1>Manage venues</h1>
                    <ul>
                        <li>
                            <a href="addVenue.php">ADD VENUE</a>
                            <a href="chooseVenue.php?task=edit">EDIT VENUES</a>
                            <a href="chooseVenue.php?task=remove">REMOVE VENUE</a>
                            <a href="listVenues.php">LIST VENUES</a>
                        </li>
                    </ul>

                    <h1>Manage states</h1>
                    <ul>
                        <li>
                            <a href="addState.php">ADD STATE</a>
                            <a href="chooseState.php?task=edit">EDIT STATES</a>
                            <a href="chooseState.php?task=remove">REMOVE STATE</a>
                            <a href="listStates.php">LIST STATES</a>
                        </li>
                    </ul>

                    <?php print $super?>

                    <h1>Canned Website Reports</h1>
                    <ul>
                        <li>
                            <a href="viewRevenue.php">VIEW REVENUE</a>
                            <a href="viewUser.php">VIEW ORDERS</a>
                            <a href="viewPerf.php">VIEW PERFS...</a>
                        </li>
                    </ul>

                    <br><br>
                </div>
            </div>
        </div>
    </body>
</html>