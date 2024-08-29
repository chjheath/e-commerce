<?php
	include "header.php";

    require_once 'js/validation.php';
?>

<!--
	Name: Christian Heatherly
	File name: register.php
	Date created: 09/30/22
	Date last modified: 10/21/22

	Sources:
        https://www.w3schools.com/html/tryit.asp?filename=tryhtml_input_tel
        https://www.w3schools.com/css/tryit.asp?filename=trycss_image_text_bottom_left
        ch_13 functions
        ch_02_display_get_post
        https://www.w3schools.com/css/css_display_visibility.asp for hidden features
        ch_19_PDO_Database
    -->

<?php

    function stateOptionList()
    {  	
        //retrieve all states from database & insert them into a list

        $list = "";
        global $con; //sql connection

        $sql = "SELECT StateID, StateName, Active FROM STATES WHERE Active = 'YES'";
        $result = $con->query($sql);

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $list = $list . '<option value ="'.$row["StateID"].'">'.$row["StateName"].'</option>';
        }

        return $list;
    }

	//string variables to use when reporting specific errors to the user

	$firstError = "";
	$lastError = "";

	$emailError = "";
	$confEmailError = "";

	$passError = "";
	$confPassError = "";

    $phoneNumError = "";

    $addrOneError = "";
    $addrTwoError = "";
    $addrCityError = "";
    $addrZipError = "";
    $addrStateError = "";

	$agreedError = "Click to agree to the terms and conditions.";

    $submitted = "";

	//boolean variables to keep track of if the user has entered a value

	$enteredFirst = FALSE;
	$enteredLast = FALSE;

	$enteredEmail = FALSE;
	$hasConfirmedEmail = FALSE;
	$emailMatches = FALSE;

	$enteredPass = FALSE;
	$hasConfirmedPass = FALSE;
	$passMatches = FALSE;

	$enteredAddrOne = FALSE;
	$enteredCity = FALSE;
	$enteredState = FALSE;
    $enteredZip = FALSE;

	$hasAgreed = FALSE;

    $doesntExist = FALSE;

	//variables to keep track of the entered values

	$first = "";
	$last = "";

	$email = "";
	$confirmEmail = "";

	$pass = "";
	$confirmPass = "";

    $phone = "";

    $addrOne = "";
    $addrTwo = "";
	$city = "";
    $state = "";
    $zip = "";

	if (isset($_POST['enter'])) {

		//Name Posting

		if (isset($_POST['firstName']) && $_POST['firstName'] != "Type here...") {
			$enteredFirst = TRUE;
			$first = trim($_POST['firstName']);
		} else {
			//return to form
			$firstError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a first name!</p>";


		}

		if (isset($_POST['lastName']) && $_POST['lastName'] != "Type here...") {
			$enteredLast = TRUE;
			$last = trim($_POST['lastName']);
		} else {
			//return to form
			$lastError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a last name!</p>";


		}

		//Email Validation & Posting

		if (filter_input (INPUT_POST, 'emailAddr', FILTER_VALIDATE_EMAIL) && filter_input (INPUT_POST, 'confEmailAddr', FILTER_VALIDATE_EMAIL)) {
			$email = trim($_POST['emailAddr']);
			$confirmEmail = trim($_POST['confEmailAddr']);

			if ($email != $confirmEmail || ($email == "" || $confirmEmail == "")) {
				$emailMatches = FALSE;
				
				//return to form
				$confEmailError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>Your e-mails do not match!</p>";

		
			} else if ($email === $confirmEmail) {

                if (validateEmail($email, $emailError)) {
                    $emailMatches = TRUE;
                }
			}
		} else {
			$emailError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a valid e-mail! (e.g. email@website.com)</p>";
		}

		//Password Validation & Posting

		if (isset($_POST['pass']) && $_POST['pass'] !== "Type here...") {
			if (validateQuery(trim($_POST['pass']))) {
                $pass = trim($_POST['pass']);
            } else {
                $passError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>Your password does not meet specifications (Must be >= 10 characters, must contain both letters & digits)";
            }

		} else {
			//return to form
			$passError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You are required to enter a password!</p>";
		}

        //Confirm password Posting

		if (isset($_POST['confPass']) && $_POST['confPass'] !== "Type here...") {
			$confirmPass = trim($_POST['confPass']);
		}

		if ($pass != $confirmPass || ($pass == "" || $confirmPass == "")) {
			$passMatches = FALSE;
			
			//return to form
			$confPassError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-213px;'>Your passwords do not match!</p>";

		} else if ($pass === $confirmPass) {
			$passMatches = TRUE;
		}

		//State Posting

		if (isset($_POST['state'])) {
			$state = trim($_POST['state']);

			$enteredState = TRUE;
		} else {
			//return to form
			$addrStateError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You need to choose a state!</p>";
		}

        //Phone Posting

        if (isset($_POST['phoneNum'])) {
            $phone = trim($_POST['phoneNum']);

            $enteredPhone = TRUE;
        } else {
            //return to form
			$phoneNumError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You have to enter a phone number using the format.</p>";
        }

        //Address Posting

        if (isset($_POST['addrOne']) && $_POST['addrOne'] != "") {
            $addrOne = trim($_POST['addrOne']);

            $enteredAddrOne = TRUE;
        } else {
            //return to form
			$addrOneError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-175px;'>You have to enter at least one address.</p>";
        }

        if (isset($_POST['addrTwo']) && $_POST['addrTwo'] != "") {
            $addrTwo = trim($_POST['addrTwo']);
        }

        if (isset($_POST['addrCity']) && $_POST['addrCity'] != "") {
            $city = trim($_POST['addrCity']);

            $enteredCity = TRUE;
        } else {
            //return to form
			$addrCityError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-244px;'>You have to choose a city.</p>";
        }

        if (isset($_POST['addrZip']) && $_POST['addrZip'] != "") {
            $zip = trim($_POST['addrZip']);

            $enteredZip = TRUE;
        } else {
            //return to form
			$addrZipError = "<p style='color:red; margin-bottom:-10px; margin-top:5px; margin-left:-239px;'>You have to enter a zip code.</p>";
        }

		if (isset($_POST['agreeTerms'])) {
			$hasAgreed = TRUE;
		} else {
			$agreedError = "You MUST agree to the terms & conditions.";
		}

        //All entries valid & ready

		if ($enteredFirst && $enteredLast && $emailMatches && $passMatches && $enteredPhone && $enteredAddrOne && $enteredCity && $enteredState && $enteredZip && $hasAgreed) {
            
            //Cleaning input [Need to do SQL cleaning as well]
            $first = sqlReplace($first);
            $last = sqlReplace($last);
            $email = sqlReplace($email);
            $pass = sqlReplace($pass);
            $phone = sqlReplace($phone);
            $addrOne = sqlReplace($addrOne);
            $addrTwo = sqlReplace($addrTwo);
            $zip = sqlReplace($zip);
            $city = sqlReplace($city);
            $state = sqlReplace($state); //not a text input, while unlikely but possible someone manipulates the page to put scripts in the value for the options, would feel better to have it cleansed

            //Checking if user exists already

            $stmt = $con->prepare("SELECT COUNT(*) as c FROM USERS WHERE Email = ?");
            $stmt->execute(array($email));

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $count = $row->c;

            if ($count >= 1) {
                $doesntExist = FALSE;
            } else {
                $doesntExist = TRUE;
            }

            $stmt->closeCursor();

            //SQL Insertion

            if ($doesntExist) {
                if (!isset($_POST['addrTwo']) || $_POST['addrTwo'] == "") { // No Address Two supplied in form
                    $stmt = $con->prepare ("INSERT INTO ADDRESS VALUES (NULL, ?, NULL, ?, ?, ?, ?)");
                    if ($stmt->execute(array($addrOne, $zip, $city, $state, "YES")) == TRUE) {
                        $stmt->closeCursor();
    
                        $stmt = $con->prepare ("SELECT * FROM ADDRESS WHERE AddressOne = ? AND ZipCode = ? AND City = ? AND StateID = ?");
                        $stmt->execute(array($addrOne, $zip, $city, $state));
    
                        $addrIDRes = $stmt->fetch(PDO::FETCH_ASSOC);
    
                        $addrID = $addrIDRes["AddressID"];
    
                        $stmt->closeCursor();
    
                        $stmt = $con->prepare ("INSERT INTO USERS VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $time = time();
    
                        if ($stmt->execute(array($first, $last, $email, $pass, $phone, $time, $addrID, "YES")) == TRUE) {
                            Header ("Location:login.php?source=reg");
                        } else {
                            //Failed adding new user
                            Header ("Location:register.php");
    
                            echo '<p style="color: lime; position: absolute; left: 25px; top: 18px;">Failed to register. Please try again.</p>';
                        }
    
                        $stmt->closeCursor();
                    }
                } else if (isset($_POST['addrTwo']) || $_POST['addrTwo'] != "") { // Address Two supplied in form
                    $stmt = $con->prepare ("INSERT INTO ADDRESS VALUES (NULL, ?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute(array($addrOne, $addrTwo, $zip, $city, $state, "YES")) == TRUE) {
                        $stmt->closeCursor();
    
                        $stmt = $con->prepare ("SELECT * FROM ADDRESS WHERE AddressOne = ? AND AddressTwo = ? AND ZipCode = ? AND City = ? AND StateID = ?");
                        $stmt->execute(array($addrOne, $addrTwo, $zip, $city, $state));
    
                        $addrIDRes = $stmt->fetch(PDO::FETCH_ASSOC);
    
                        $addrID = $addrIDRes["AddressID"];
    
                        $stmt->closeCursor();
    
                        $stmt = $con->prepare ("INSERT INTO USERS VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $time = time();
    
                        if ($stmt->execute(array($first, $last, $email, $pass, $phone, $time, $addrID, "YES")) == TRUE) {
                            Header ("Location:login.php?source=reg");
                        } else {
                            //Failed adding new user
                            Header ("Location:register.php");
    
                            echo '<p style="color: red; position: absolute; left: 730px; top: 285px;">Failed to register. Please try again.</p>';
                        }
    
                        $stmt->closeCursor();
                    }
                }
            } else {
                //Failed adding new user
                Header ("Location:register.php");
    
                echo '<p style="color: red; position: absolute; left: 730px; top: 285px;">E-mail already exists.</p>';
            }
        }  else {

            echo '<p style="color: red; position: absolute; left: 730px; top: 285px;">Failed to register. Please try again.</p>';

            $enteredFirst = FALSE;
            $enteredLast = FALSE;
            $emailMatches = FALSE;
            $passMatches = FALSE;
            $enteredPhone = FALSE;
            $enteredAddrOne = FALSE;
            $enteredCity = FALSE;
            $enteredState = FALSE;
            $enteredZip = FALSE;
            $hasAgreed = FALSE;

            $first = "";
            $last = "";
                        
            $email = "";
            $pass = "";
                
            $phone = "";
                        
            $addrOne = "";
            $addrTwo = "";
            $city = "";
            $state = "";
            $zip = "";
        }
	}
?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register a new account</title>
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
                            <form id="register" action="register.php" method="post">
                                <!-- First Name (Left Hand) -->
                                <label for="firstName"><h3>Enter first name <?php print $firstError?></h3></label>
                                <input type="text" name="firstName" value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';">
                                    
                                <!-- Last Name -->
                                <label for="lastName"><h3>Enter last name <?php print $lastError?></h3></label>
                                <input type="text" name="lastName" value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';">
                                
                                <!-- E-mail address -->
                                <label for="emailAddr"><h3>Enter e-mail address <?php print $emailError?></h3></label>
                                <input type="email" name="emailAddr" value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';">
                                

                                <!-- Confirm E-mail address -->
                                <label for="confEmailAddr"><h3>Confirm e-mail address <?php print $confEmailError?></h3></label>
                                <input type="email" name="confEmailAddr" value="Type here..." onblur="this.value=!this.value?'Type here...':this.value;" onfocus="this.select()" onclick="this.value='';">
                
                                <!-- Password -->
                                <label for="pass"><h3>Enter password <?php print $passError?></h3></label>
                                <input type="password" name="pass" value="Type here..." onclick="this.value='';">
                            
                                <!-- Confirm Password -->
                                <label for="confPass"><h3>Confirm password <?php print $confPassError?></h3></label>
                                <input type="password" name="confPass" value="Type here..." onclick="this.value='';">

                                <br>

                                <!-- Phone -->

                                <label for="phoneNum"><h3>Enter phone number <h4 style="margin-top:-5px; margin-left:25px;">(Format: XXX-XXX-XXXX)</h4> <?php print $phoneNumError?></h3></label>
                                <input type="tel" name="phoneNum" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" onclick="this.value='';">

                                <!-- Address -->

                                <label for="addrOne"><h3>Enter first address <?php print $addrOneError?></h3></label>
                                <input type="text" name="addrOne" onclick="this.value='';">

                                <br>

                                <label for="addrTwo"><h3>Enter second address <h4 style="margin-top:-5px; margin-left:25px;">(Optional)</h4> <?php print $addrTwoError?></h3></label>
                                <input type="text" name="addrTwo" onclick="this.value='';">

                                <br>

                                <label for="addrCity"><h3>Enter city <?php print $addrCityError?></h3></label>
                                <input type="text" name="addrCity" onclick="this.value='';">

                                <br>

                                <label for="state"><h3>Choose your state <?php print $addrStateError?></h3></label>
                                <select name="state">
                                    <?php print stateOptionList(); ?> 
                                </select>

                                <br>

                                <label for="addrZip"><h3>Enter zip code <?php print $addrZipError?></h3></label>
                                <input type="text" name="addrZip" onclick="this.value='';">

                                <br><br><br><br>

                                <!-- Terms & Conditions -->
                                <input style="position:absolute;bottom:80px;left:540px;" type="checkbox" name="agreeTerms">
                                <p style="margin-top:-65px; color: red; width: 100%; max-width:100%;"><?php print $agreedError?></p>

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