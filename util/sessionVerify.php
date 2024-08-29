<?php

	if (!isset($_SESSION['timeout'])) {
		Header("Location:logout.php");
	} else {
	   if ($_SESSION['timeout'] + 1 * 6000 < time()) {
		Header("Location:logout.php");
	   } else {
		$_SESSION['timeout'] = time();
	   }
	}

?>
