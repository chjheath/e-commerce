<?php

    session_start(); //retrieve current session
    session_destroy();

    include "header.php";
?>

<!--
	Name: Christian Heatherly
	File name: logout.php
	Date created: 10/16/22
	Date last modified: 10/16/22

	Sources:
        ch19_PDOdatabase
-->

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Information</title>
        <link rel="stylesheet" href="css/style.css" type="text/css">
        <link rel="stylesheet" type="text/css" href="css/mobile.css">
        <script src="js/mobile.js" type="text/javascript"></script>
    </head>

    <body>
            <div id="body">
                <!-- Top of Page (Not including nav) -->
                <div class="header">
                    <div class="contact">
                    <div id="loginBox" class="detailBox">
                        <h2>You have been logged out. Click <a href="index.php">here</a> to go back to the homepage.</h2>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>