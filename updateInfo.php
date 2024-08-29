<?php

    session_start();

	include "header.php";

    require_once 'js/validation.php';

    if (!isset($_SESSION['uid'])) { //if user not logged in anymore
        session_destroy();
        Header ("Location:login.php"); //send user to logout automatically
    }

    $stmt = $con->prepare("SELECT FirstName, LastName, Email, Phone, AddressID FROM USERS WHERE UserID = ?");
    $stmt->execute(array($_SESSION['uid']));

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $a_id = $row['AddressID']; //bring in addressid for further searching [can turn into compound query later]

    $in_first = $row['FirstName'];
    $in_last = $row['LastName'];
    $in_email = $row['Email'];
    $in_phone = $row["Phone"];

    $stmt->closeCursor(); //close current statement result

    //Retrieve Address Information

    $stmt = $con->prepare("SELECT AddressOne, AddressTwo, ZipCode, City, StateID FROM ADDRESS WHERE AddressID = ?");
    $stmt->execute(array($a_id));

    $a_row = $stmt->fetch(PDO::FETCH_ASSOC);

    $s_id = $a_row['StateID']; //bring in state id for further searching

    $addressHeader = '<h3>Address: </h3>';

    $in_addressOne = $a_row["AddressOne"];
    
    if ($a_row['AddressTwo'] == "") {
        $in_addressTwo = '';
    } else {
        $in_addressTwo = $a_row["AddressTwo"];
    }

    $in_city = $a_row["City"];
    $in_zip = $a_row["ZipCode"];

    $stmt->closeCursor(); //closing current result cursor

    //Retrieve State Information for supplied address 
    $stmt = $con->prepare("SELECT StateID, StateName, StateInitial FROM STATES WHERE StateID = ?");
    $stmt->execute(array($s_id));

    $s_row = $stmt->fetch(PDO::FETCH_ASSOC);

    $in_state = $s_row["StateName"];

    $stmt->closeCursor(); //closing current result cursor
?>

    <!--
	Name: Christian Heatherly
	File name: updateInfo.php
	Date created: 11/03/22
	Date last modified: 11/11/22

	Sources:
    -->

<?php

    function stateOptionList($addrID)
    {  	
        //retrieve all states from database & insert them into a list

        $list = "";
        global $con; //sql connection

        $sql = "SELECT StateID, StateName, Active FROM STATES WHERE Active = 'YES'";
        $result = $con->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if ($row['StateID'] == $addrID) {
                echo $row["StateID"];
                $list = $list . '<option value ="'.$row["StateID"].'" selected>'.$row["StateName"].'</option>';
            } else {
                $list = $list . '<option value ="'.$row["StateID"].'">'.$row["StateName"].'</option>';
            }
        }

        return $list;
    }

    //variables to keep track of states, errors, etc.

    $firstError = '';
    $lastError = '';
    $phoneNumError = '';
    $addrOneError = '';
    $addrCityError = '';
    $addrZipError = '';
    $emailError = '';
    $confEmailError = '';

    $firstOK = TRUE;
    $lastOK = TRUE;
    $emailOK = FALSE;
    $phoneOK = TRUE;
    $addrOneOK = TRUE;
    $addrTwoOK = TRUE;
    $cityOK = TRUE;
    $stateOK = TRUE;
    $zipOK = TRUE;

    $isChangingEmail = FALSE;

    $first = '';
    $last = '';

    $currEmail = '';
    $email = '';
    $confEmail = '';

    $phone = '';

    $addrOne = '';
    $addrTwo = '';
    $city = '';
    $state = '';
    $zip = '';


    if (isset($_POST['enter'])) { //form submitted

        //First Name

        if (isset($_POST['firstName']) && $_POST['firstName'] !== '') {
            $first = sqlReplace(trim($_POST['firstName']));
        } else {
            $firstOK = FALSE;
            $firstError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a first name! If you don't want to change your name, do not touch the textbox.</p>";
        }

        //Last Name

        if (isset($_POST['lastName']) && $_POST['lastName'] !== '') {
            $last = sqlReplace(trim($_POST['lastName']));
        } else {
            $lastOK = FALSE;
            $lastError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a last name! If you don't want to change your name, do not touch the textbox.</p>";
        }

        //Phone #

        if (isset($_POST['phoneNum']) && $_POST['phoneNum'] !== '') {
            $phone = sqlReplace(trim($_POST['phoneNum']));
        } else {
            $phoneOK = FALSE;
            $phoneNumError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a phone number! If you don't want to change your phone number, do not touch the textbox.</p>";
        }

        //First Address

        if (isset($_POST['addrOne']) && $_POST['addrOne'] !== '') {
            $addrOne = sqlReplace(trim($_POST['addrOne']));
        } else {
            $addrOneOK = FALSE;
            $addrOneError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter an address! If you don't want to change your address, do not touch the textbox.</p>";
        }

        //Second Address

        if (isset($_POST['addrTwo'])) {
            $addrTwo = sqlReplace(trim($_POST['addrTwo']));
        }

        //City

        if (isset($_POST['addrCity']) && $_POST['addrCity'] !== '') {
            $city = sqlReplace(trim($_POST['addrCity']));
        } else {
            $cityOK = FALSE;
            $addrCityError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a city! If you don't want to change your city, do not touch the textbox.</p>";
        }

        //Zip Code

        if (isset($_POST['addrZip']) && $_POST['addrZip'] !== '') {
            $zip = sqlReplace(trim($_POST['addrZip']));
        } else {
            $zipOK = FALSE;
            $addrZipError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a zip code! If you don't want to change your zip code, do not touch the textbox.</p>";
        }

        //State

        if (isset($_POST['state']) && $_POST['state'] !== '') {
            $state = sqlReplace(trim($_POST['state']));
        }

        //Checking if user is editing e-mails or passwords

        if ((isset($_POST['emailAddr']) && $_POST['emailAddr'] !== $in_email)) {
            $isChangingEmail = TRUE;
        }

        if ($isChangingEmail) { //only changing e-mail

            if ((isset($_POST['emailAddr']) && $_POST['emailAddr'] !== $in_email) && (isset($_POST['confEmailAddr']) && $_POST['confEmailAddr'] !== 'Type here...')) {
                $stmt = $con->prepare("SELECT COUNT(*) as c FROM USERS WHERE UserID = ? AND Email = ?");
                if ($stmt->execute(array($_SESSION['uid'], $_POST['emailAddr'])) && ((isset($_POST['emailAddr']) && $_POST['emailAddr'] !== $in_email) && (isset($_POST['confEmailAddr']) && $_POST['confEmailAddr'] !== 'Type here...'))) {
                    if (filter_input (INPUT_POST, 'emailAddr', FILTER_VALIDATE_EMAIL) && filter_input (INPUT_POST, 'confEmailAddr', FILTER_VALIDATE_EMAIL)) {
                        
                        $email = sqlReplace(trim($_POST['emailAddr']));
                        $confEmail = sqlReplace(trim($_POST['confEmailAddr']));
            
                        if ($email != $confEmail || ($email == "" || $confEmail == "")) {
                            $emailOK = FALSE;
                            
                            //return to form
                            $confEmailError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>Your e-mails do not match!</p>";
            
                    
                        } else if ($email === $confEmail) {
            
                            if (validateEmail($email, $emailError)) {
                                $emailOK = TRUE;
                            } else {
                                $emailOK = FALSE;
                                $emailError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>That e-mail exists already.</p>";
                            }
                        }
                    } else {
                        $emailError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a valid e-mail! (e.g. email@website.com)</p>";
                    }
                }
            }

            if ($firstOK && $lastOK && $phoneOK && $emailOK && $addrOneOK && $addrTwoOK && $cityOK && $stateOK && $zipOK) {
                if ($addrTwo != "") {
                    //Update address

                    $stmt = $con->prepare("UPDATE ADDRESS SET AddressOne = ?, AddressTwo = ?, City = ?, StateID = ?, ZipCode = ? WHERE AddressID = ?");
                    $stmt->execute(array($addrOne, $addrTwo, $city, $state, $zip, $_SESSION['uid'])); //using userID for addressID because they are ALWAYS the same number
                } else {
                    //Update address

                    $stmt = $con->prepare("UPDATE ADDRESS SET AddressOne = ?, City = ?, StateID = ?, ZipCode = ? WHERE AddressID = ?");
                    $stmt->execute(array($addrOne, $city, $state, $zip, $_SESSION['uid'])); //using userID for addressID because they are ALWAYS the same number
                }

                //Update user info

                $stmt = $con->prepare("UPDATE USERS SET FirstName = ?, LastName = ?, Email = ?, Phone = ? WHERE UserID = ?");
                $stmt->execute(array($first, $last, $email, $phone, $_SESSION['uid']));

                Header("Location:userInfo.php?source=upd");
            }
        } else { //changing neither email nor password
            if ($firstOK && $lastOK && $phoneOK && $addrOneOK && $addrTwoOK && $cityOK && $stateOK && $zipOK) {
                if ($addrTwo != "") {
                    //Update address

                    $stmt = $con->prepare("UPDATE ADDRESS SET AddressOne = ?, AddressTwo = ?, City = ?, StateID = ?, ZipCode = ? WHERE AddressID = ?");
                    $stmt->execute(array($addrOne, $addrTwo, $city, $state, $zip, $_SESSION['uid'])); //using userID for addressID because they are ALWAYS the same number
                } else {
                    //Update address

                    $stmt = $con->prepare("UPDATE ADDRESS SET AddressOne = ?, City = ?, StateID = ?, ZipCode = ? WHERE AddressID = ?");
                    $stmt->execute(array($addrOne, $city, $state, $zip, $_SESSION['uid'])); //using userID for addressID because they are ALWAYS the same number
                }

                //Update user info

                $stmt = $con->prepare("UPDATE USERS SET FirstName = ?, LastName = ?, Phone = ? WHERE UserID = ?");
                $stmt->execute(array($first, $last, $phone, $_SESSION['uid']));

                Header("Location:userInfo.php?source=upd");
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
        <title>Update your information</title>
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
                            <h1 style="color:black">Enter your information below.</h1>
                            <!-- Code from Lab1 -->
                            <form id="updateInfo" action="updateInfo.php" method="post">
                                <!-- First Name (Left Hand) -->
                                <label for="firstName"><h3>Update first name <?php print $firstError?></h3></label>
                                <input type="text" name="firstName" onfocus="this.select()" value="<?php print $in_first?>">
                                    
                                <!-- Last Name -->
                                <label for="lastName"><h3>Update last name <?php print $lastError?></h3></label>
                                <input type="text" name="lastName" onfocus="this.select()" value="<?php print $in_last?>">
                            
                                <br>

                                <!-- E-mail address -->
                                <label for="emailAddr"><h3>Update e-mail address <?php print $emailError?></h3></label>
                                <input type="text" name="emailAddr" onfocus="this.select()" value="<?php print $in_email?>">
                                

                                <!-- Confirm E-mail address -->
                                <label for="confEmailAddr"><h3>Confirm new e-mail address <?php print $confEmailError?></h3></label>
                                <input type="text" name="confEmailAddr" value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';">
                
                                <br>

                                <!-- Phone -->

                                <label for="phoneNum"><h3>Update phone number <h4 style="margin-top:-5px; margin-left:25px;">(Format: XXX-XXX-XXXX)</h4> <?php print $phoneNumError?></h3></label>
                                <input type="tel" name="phoneNum" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" onfocus="this.select()" value="<?php print $in_phone?>">

                                <!-- Address -->

                                <label for="addrOne"><h3>Update first address <?php print $addrOneError?></h3></label>
                                <input type="text" name="addrOne" onfocus="this.select()" value="<?php print $in_addressOne?>">

                                <br>

                                <label for="addrTwo"><h3>Update second address <h4 style="margin-top:-5px; margin-left:25px;">(Optional)</h4></h3></label>
                                <input type="text" name="addrTwo" onfocus="this.select()" value="<?php print $in_addressTwo?>">

                                <br>

                                <label for="addrCity"><h3>Update city <?php print $addrCityError?></h3></label>
                                <input type="text" name="addrCity" onfocus="this.select()" value="<?php print $in_city?>">

                                <br>

                                <label for="state"><h3>Update your state</h3></label>
                                <select name="state">
                                    <?php 
                                    
                                    $stmt = $con->prepare("SELECT StateID FROM ADDRESS WHERE AddressID = ?");
                                    $stmt->execute(array($a_id));
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $stateIDRes = $row['StateID'];

                                    print stateOptionList($stateIDRes); ?> 
                                </select>

                                <br>

                                <label for="addrZip"><h3>Update your zip code <?php print $addrZipError?></h3></label>
                                <input type="text" name="addrZip" onfocus="this.select()" value="<?php print $in_zip?>">

                                <input type="submit" name="enter" id="submit" value="Submit" style="width:96%">
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