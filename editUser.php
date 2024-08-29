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
        Header("Location:chooseUser.php");
    }

    if (isset($_SESSION['task']) && $_SESSION['task'] === '') {
        Header("Location:admin.php");
    }

    $stmt = $con->prepare("SELECT FirstName, LastName, Email, Phone, AddressID, Active FROM USERS WHERE UserID = ?");
    $stmt->execute(array($chosenID));

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $a_id = $row['AddressID']; //bring in addressid for further searching [can turn into compound query later]

    $in_first = $row['FirstName'];
    $in_last = $row['LastName'];
    $in_email = $row['Email'];
    $in_phone = $row["Phone"];
    $in_act = $row['Active'];

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
	File name: editUser.php
	Date created: 11/03/22
	Date last modified: 11/04/22

	Sources:
        https://stackoverflow.com/a/16239755
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
    $passError = '';
    $confPassError = '';
    $currPassError = '';
    $emailError = '';
    $confEmailError = '';

    $firstOK = TRUE;
    $lastOK = TRUE;
    $emailOK = FALSE;
    $currPassOK = FALSE;
    $passOK = FALSE;
    $phoneOK = TRUE;
    $addrOneOK = TRUE;
    $addrTwoOK = TRUE;
    $cityOK = TRUE;
    $stateOK = TRUE;
    $zipOK = TRUE;

    $isChangingEmail = FALSE;
    $isChangingPass = FALSE;

    $first = '';
    $last = '';

    $currEmail = '';
    $email = '';
    $confEmail = '';

    $currPass = '';
    $pass = '';
    $confPass = '';

    $phone = '';

    $addrOne = '';
    $addrTwo = '';
    $city = '';
    $state = '';
    $zip = '';

    $activeStatus = '';

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

        if ((isset($_POST['emailAddr']) && $_POST['emailAddr'] !== $in_email) && (isset($_POST['confEmailAddr']) && $_POST['emailAddr'] !== 'Type here...')) {
            $isChangingEmail = TRUE;
        }

        if ((isset($_POST['currPass']) && $_POST['currPass'] !== 'Type here...') && isset($_POST['pass']) && isset($_POST['confPass'])) {
            $isChangingPass = TRUE;
        }

        if (isset($_POST['activeStatus'])) {
            $activeStatus = "YES";
        } else {
            $activeStatus = "NO";
        }

        //Only changing password
        if (!$isChangingEmail && $isChangingPass) {

            if (isset($_POST['currPass']) && $_POST['currPass'] !== 'Type here...') {
                $currPass = sqlReplace(trim($_POST['currPass']));

                $stmt = $con->prepare("SELECT COUNT(*) as num FROM USERS WHERE UserID = ? AND Password = ?");
                if ($stmt->execute(array($chosenID, $currPass)) && isset($_POST['pass']) && isset($_POST['confPass'])) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $count = $row['num'];

                    if ($count < 1) {
                        $passOK = FALSE;
                        $currPassError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>Your current password is incorrect.</p>";
                    } else {
                        if (validateQuery(sqlReplace(trim($_POST['pass'])))) {
                        
                            $pass = sqlReplace(trim($_POST['pass']));
                            $confPass = sqlReplace(trim($_POST['confPass']));
    
                            if ($pass != $confPass || ($pass == "" || $confPass == "")) {
                                $passOK = FALSE;
                                
                                //return to form
                                $confPassError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-213px;'>Your passwords do not match!</p>";
                    
                            } else if ($pass === $confPass) {
                                $passOK = TRUE;
                            }
                        } else {
                            $passOK = FALSE;
                            $passError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>Your password does not meet specifications (Must be >= 10 characters, must contain both letters & digits)</p>";
                        }
                    }
                }
            }

            if ($firstOK && $lastOK && $phoneOK && $passOK && $addrOneOK && $addrTwoOK && $cityOK && $stateOK && $zipOK) {
                if ($addrTwo != "") {
                    //Update address

                    $stmt = $con->prepare("UPDATE ADDRESS SET AddressOne = ?, AddressTwo = ?, City = ?, StateID = ?, ZipCode = ? WHERE AddressID = ?");
                    $stmt->execute(array($addrOne, $addrTwo, $city, $state, $zip, $chosenID)); //using userID for addressID because they are ALWAYS the same number
                } else {
                    //Update address

                    $stmt = $con->prepare("UPDATE ADDRESS SET AddressOne = ?, City = ?, StateID = ?, ZipCode = ? WHERE AddressID = ?");
                    $stmt->execute(array($addrOne, $city, $state, $zip, $chosenID)); //using userID for addressID because they are ALWAYS the same number
                }

                //Update user info

                $stmt = $con->prepare("UPDATE USERS SET FirstName = ?, LastName = ?, Password = ?, Phone = ?, Active = ? WHERE UserID = ?");
                $stmt->execute(array($first, $last, $pass, $phone, $activeStatus, $chosenID));

                $_SESSION['task'] = '';
                $_SESSION['chosen'] = '';

                Header("Location:admin.php?source=editreg");
            }
        } else if ($isChangingEmail && !$isChangingPass) { //only changing e-mail

            if ((isset($_POST['emailAddr']) && $_POST['emailAddr'] !== $in_email) && (isset($_POST['confEmailAddr']) && $_POST['confEmailAddr'] !== 'Type here...')) {
                $stmt = $con->prepare("SELECT COUNT(*) as c FROM USERS WHERE UserID = ? AND Email = ?");
                if ($stmt->execute(array($chosenID, $_POST['emailAddr'])) && ((isset($_POST['emailAddr']) && $_POST['emailAddr'] !== $in_email) && (isset($_POST['confEmailAddr']) && $_POST['confEmailAddr'] !== 'Type here...'))) {
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
                    $stmt->execute(array($addrOne, $addrTwo, $city, $state, $zip, $chosenID)); //using userID for addressID because they are ALWAYS the same number
                } else {
                    //Update address

                    $stmt = $con->prepare("UPDATE ADDRESS SET AddressOne = ?, City = ?, StateID = ?, ZipCode = ? WHERE AddressID = ?");
                    $stmt->execute(array($addrOne, $city, $state, $zip, $chosenID)); //using userID for addressID because they are ALWAYS the same number
                }

                //Update user info

                $stmt = $con->prepare("UPDATE USERS SET FirstName = ?, LastName = ?, Email = ?, Phone = ?, Active = ? WHERE UserID = ?");
                $stmt->execute(array($first, $last, $email, $phone, $activeStatus, $chosenID));

                $_SESSION['task'] = '';
                $_SESSION['chosen'] = '';

                Header("Location:admin.php?source=editreg");
            }
        } else if ($isChangingEmail && $isChangingPass) { //changing both email & password

            if ((isset($_POST['emailAddr']) && $_POST['emailAddr'] !== $in_email) && (isset($_POST['confEmailAddr']) && $_POST['confEmailAddr'] !== 'Type here...')) {
                $stmt = $con->prepare("SELECT COUNT(*) as c FROM USERS WHERE UserID = ? AND Email = ?");
                if ($stmt->execute(array($chosenID, $_POST['emailAddr'])) && ((isset($_POST['emailAddr']) && $_POST['emailAddr'] !== $in_email) && (isset($_POST['confEmailAddr']) && $_POST['confEmailAddr'] !== 'Type here...'))) {
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

            if (isset($_POST['currPass']) && $_POST['currPass'] !== 'Type here...') {
                $currPass = sqlReplace(trim($_POST['currPass']));

                $stmt = $con->prepare("SELECT COUNT(*) as num FROM USERS WHERE UserID = ? AND Password = ?");
                if ($stmt->execute(array($chosenID, $currPass)) && isset($_POST['pass']) && isset($_POST['confPass'])) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $count = $row['num'];

                    if ($count < 1) {
                        $passOK = FALSE;
                        $currPassError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>Your current password is incorrect.</p>";
                    } else {
                        if (validateQuery(sqlReplace(trim($_POST['pass'])))) {
                        
                            $pass = sqlReplace(trim($_POST['pass']));
                            $confPass = sqlReplace(trim($_POST['confPass']));
    
                            if ($pass != $confPass || ($pass == "" || $confPass == "")) {
                                $passOK = FALSE;
                                
                                //return to form
                                $confPassError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-213px;'>Your passwords do not match!</p>";
                    
                            } else if ($pass === $confPass) {
                                $passOK = TRUE;
                            }
                        } else {
                            $passOK = FALSE;
                            $passError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>Your password does not meet specifications (Must be >= 10 characters, must contain both letters & digits)</p>";
                        }
                    }
                }
            }

            if ($firstOK && $lastOK && $phoneOK && $emailOK && $passOK && $addrOneOK && $addrTwoOK && $cityOK && $stateOK && $zipOK) {
                if ($addrTwo != "") {
                    //Update address

                    $stmt = $con->prepare("UPDATE ADDRESS SET AddressOne = ?, AddressTwo = ?, City = ?, StateID = ?, ZipCode = ? WHERE AddressID = ?");
                    $stmt->execute(array($addrOne, $addrTwo, $city, $state, $zip, $chosenID)); //using userID for addressID because they are ALWAYS the same number
                } else {
                    //Update address

                    $stmt = $con->prepare("UPDATE ADDRESS SET AddressOne = ?, City = ?, StateID = ?, ZipCode = ? WHERE AddressID = ?");
                    $stmt->execute(array($addrOne, $city, $state, $zip, $chosenID)); //using userID for addressID because they are ALWAYS the same number
                }

                //Update user info

                $stmt = $con->prepare("UPDATE USERS SET FirstName = ?, LastName = ?, Email = ?, Password = ?, Phone = ?, Active = ? WHERE UserID = ?");
                $stmt->execute(array($first, $last, $email, $pass, $phone, $activeStatus, $chosenID));

                Header("Location:admin.php?source=editreg");
            }
        } else if (!$isChangingEmail && !$isChangingPass) { //changing neither email nor password
            if ($firstOK && $lastOK && $phoneOK && $addrOneOK && $addrTwoOK && $cityOK && $stateOK && $zipOK) {
                if ($addrTwo != "") {
                    //Update address

                    $stmt = $con->prepare("UPDATE ADDRESS SET AddressOne = ?, AddressTwo = ?, City = ?, StateID = ?, ZipCode = ? WHERE AddressID = ?");
                    $stmt->execute(array($addrOne, $addrTwo, $city, $state, $zip, $chosenID)); //using userID for addressID because they are ALWAYS the same number
                } else {
                    //Update address

                    $stmt = $con->prepare("UPDATE ADDRESS SET AddressOne = ?, City = ?, StateID = ?, ZipCode = ? WHERE AddressID = ?");
                    $stmt->execute(array($addrOne, $city, $state, $zip, $chosenID)); //using userID for addressID because they are ALWAYS the same number
                }

                //Update user info

                $stmt = $con->prepare("UPDATE USERS SET FirstName = ?, LastName = ?, Phone = ?, Active = ? WHERE UserID = ?");
                $stmt->execute(array($first, $last, $phone, $activeStatus, $chosenID));

                $_SESSION['task'] = '';
                $_SESSION['chosen'] = '';

                Header("Location:admin.php?source=editreg");
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
        <title>Update user information</title>
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
                            <form id="updateInfo" action="editUser.php" method="post">
                                <!-- First Name (Left Hand) -->
                                <label for="firstName"><h3>Update their first name <?php print $firstError?></h3></label>
                                <input type="text" name="firstName" onfocus="this.select()" value="<?php print $in_first?>">
                                    
                                <!-- Last Name -->
                                <label for="lastName"><h3>Update their last name <?php print $lastError?></h3></label>
                                <input type="text" name="lastName" onfocus="this.select()" value="<?php print $in_last?>">
                            
                                <br>

                                <!-- E-mail address -->
                                <label for="emailAddr"><h3>Update their e-mail address <?php print $emailError?></h3></label>
                                <input type="text" name="emailAddr" onfocus="this.select()" value="<?php print $in_email?>">
                                

                                <!-- Confirm E-mail address -->
                                <label for="confEmailAddr"><h3>Confirm new e-mail address <?php print $confEmailError?></h3></label>
                                <input type="text" name="confEmailAddr" value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';">
                
                                <br>

                                <!-- Current Password -->
                                <label for="currPass"><h3>Enter their current password <?php print $currPassError?></h3></label>
                                <input type="password" name="currPass" value="Type here..." onclick="this.value='';">

                                <!-- Password -->
                                <label for="pass"><h3>Create new password <?php print $passError?></h3></label>
                                <input type="password" name="pass" value="Type here..." onclick="this.value='';">
                            
                                <!-- Confirm Password -->
                                <label for="confPass"><h3>Confirm new password <?php print $confPassError?></h3></label>
                                <input type="password" name="confPass" value="Type here..." onclick="this.value='';">

                                <!-- Phone -->

                                <label for="phoneNum"><h3>Update their phone number <h4 style="margin-top:-5px; margin-left:25px;">(Format: XXX-XXX-XXXX)</h4> <?php print $phoneNumError?></h3></label>
                                <input type="tel" name="phoneNum" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" onfocus="this.select()" value="<?php print $in_phone?>">

                                <!-- Address -->

                                <label for="addrOne"><h3>Update their first address <?php print $addrOneError?></h3></label>
                                <input type="text" name="addrOne" onfocus="this.select()" value="<?php print $in_addressOne?>">

                                <br>

                                <label for="addrTwo"><h3>Update their second address <h4 style="margin-top:-5px; margin-left:25px;">(Optional)</h4></h3></label>
                                <input type="text" name="addrTwo" onfocus="this.select()" value="<?php print $in_addressTwo?>">

                                <br>

                                <label for="addrCity"><h3>Update their city <?php print $addrCityError?></h3></label>
                                <input type="text" name="addrCity" onfocus="this.select()" value="<?php print $in_city?>">

                                <br>

                                <label for="state"><h3>Update their state</h3></label>
                                <select name="state">
                                    <?php 
                                    
                                    $stmt = $con->prepare("SELECT StateID FROM ADDRESS WHERE AddressID = ?");
                                    $stmt->execute(array($a_id));
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $stateIDRes = $row['StateID'];

                                    print stateOptionList($stateIDRes); ?> 
                                </select>

                                <br>

                                <label for="addrZip"><h3>Update their zip code <?php print $addrZipError?></h3></label>
                                <input type="text" name="addrZip" onfocus="this.select()" value="<?php print $in_zip?>">

                                <br><br><br><br>

                                <!-- Active? -->
                                <input style="position:absolute;bottom:80px;left:530px;" type="checkbox" name="activeStatus" <?php if ($in_act == 'YES') echo "checked='checked'";?>> <!-- https://stackoverflow.com/a/16239755 -->
                                <p style="margin-top:-75px; width: 100%; max-width:100%; margin-left:15px;">Choose whether this user is active or not.</p>

                                <input type="submit" name="enter" id="submit" value="Submit">
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