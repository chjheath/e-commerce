<?php

/*
	Name: Christian Heatherly
	File name: validation.js
	Date created: 09/14/22
	Date last modified: 09/14/22

	Sources:
		ch13_functions/inc/util.php (Helped with figuring out how to validate numbers & letters being present)
*/

//Email validation

function validateEmail($email, $msg) {
	global $con; //sql connection

	$stmt = $con->prepare("SELECT count(*) as result FROM USERS WHERE Email = ?");
	$stmt->execute(array($email));
	$row = $stmt->fetch(PDO::FETCH_OBJ);
	$count = $row->result;

	if ($count == 1) { //found entry with same e-mail
		return false;
	} else {
		return true;
	}
}

//Password validation

function validateQuery ($query) {
	$query = trim($query);
	$length = strlen($query);

	//contains #'s & letters, and is >= 10 characters in length?
	if (($length >= 10) && (containsNumbers($query) && containsLetters($query))) {
		return true; //passes validation
	} else {
		return false; //fails validation
	}
}

//Contains # (slightly modified from ch13_functions/inc/util.php)

function containsNumbers ($str) {
	$foundNumber = FALSE;
	$length = strlen($str);
	$str = trim($str);
	$strChars = str_split($str);

	for ($x = 0; $x < $length; $x++) {
		if (preg_match("/[0-9]/", $strChars[$x])) { //is there a 0 to 9 in the string?
			$foundNumber = TRUE;
			break;
		}
	}

	if ($foundNumber) {
		return true;
	} else {
		false;
	}
}

//Contains letter (slightly modified from ch13_functions/inc/util.php)

function containsLetters ($str) {
	$foundLetter = FALSE;
	$length = strlen($str);
	$str = trim($str);
	$strChars = str_split($str);

	for ($x = 0; $x < $length; $x++) {
		if (preg_match("/[A-Za-z]/", $strChars[$x])) { //is there any letters in the string?
			$foundLetter = TRUE;
			break;
		}
	}

	if ($foundLetter) {
		return true;
	} else {
		false;
	}
}

//Generate random code (slightly modified from ch13_functions/inc/util.php)

function generateCode ($length) {
	$code = "";

	for ($x = 0; $x < $length; $x++) {
		$randomNum = mt_rand(1, 35); //26 characteers + 10 digits

		if ($randomNum > 26) {
			$randomNum -= 26;

			$code = $code.$randomNum;
		} else {
			$code = $code.toChar($randomNum);
		}
	}

	return $code;
}

//Verify random code

function verifyCode ($code) {
	if (strlen($code) >= 50 && (containsLetters($code) && containsNumbers($code))) {
		return true;
	} else {
		return false;
	}
}

function toChar($digit) { //(taken directly from ch13_functions/inc/util.php)
	$char = "";
	switch ($digit){
		   case 1: $char = "A"; break;
		   case 2: $char = "B"; break;
		   case 3: $char = "C"; break;
		   case 4: $char = "D"; break;
		   case 5: $char = "E"; break;
		   case 6: $char = "F"; break;
		   case 7: $char = "G"; break;
		   case 8: $char = "H"; break;
		   case 9: $char = "I"; break;
		   case 10: $char = "J"; break;
		   case 11: $char = "K"; break;
		   case 12: $char = "L"; break;
		   case 13: $char = "M"; break;
		   case 14: $char = "N"; break;
		   case 15: $char = "O"; break;
		   case 16: $char = "P"; break;
		   case 17: $char = "Q"; break;
		   case 18: $char = "R"; break;
		   case 19: $char = "S"; break;
		   case 20: $char = "T"; break;
		   case 21: $char = "U"; break;
		   case 22: $char = "V"; break;
		   case 23: $char = "W"; break;
		   case 24: $char = "X"; break;
		   case 25: $char = "Y"; break;
		   case 26: $char = "Z"; break;
		   default: "A";

	}
	return $char;
}

function sqlReplace ($text) {
	$search = array(
    		'@<script[^>]*?>.*?</script>@si',   // Strip out anything between the javascript tags
    		'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    		'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
  	);

	$text = preg_replace($search, '', $text);

	$text = htmlspecialchars($text, ENT_QUOTES);

	return $text;
}

?>
