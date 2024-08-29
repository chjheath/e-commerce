<?php
    $hostname = '**REDACTED**';
    $username = '**REDACTED**';
    $password = '**REDACTED**';

    try {
        $con = new PDO ("mysql:host=$hostname;dbname=**REDACTED**", $username, $password);

        echo '<p style="color: #04AA6D; position: absolute; left: 25px; top: 18px;">Connected to database.</p>';
    } catch (PDOException $e) {
        echo '<p style="color: red; position: absolute; top: 3px; right: 50px; font-size: 14px;">Could not connect to database.<br><br>'.$e->getMessage().'</p>';
    }
?>
