<?php

	//print "THIS IS THE HEADER FILE"; Removed on 9/1/22

	require_once 'util/db_connect.php';
	require_once 'js/validation.php';

	$registerBtn = '<li style="margin-left:385px">
						<a style="float:right;" href="register.php">Register</a>
					</li>';
	$loginBtn = '<li>
					<a href="login.php">Login</a>
				</li>';

	$eventsBtn = '';
	$logoutBtn = '';
	$userInfoBtn = '';
	$adminBtn = '';

	if (isset($_SESSION['email'])) { //if user is logged in
        $registerBtn = '';
		$loginBtn = '';
		
		$logoutBtn = '<li>
						<a href="logout.php">Logout</a>
					 </li>';
		$userInfoBtn = '<li class="menu" style="margin-left:265px">
							<a href="userInfo.php">Account</a>
						</li>';

		$eventsBtn = '<li>
							<a href="search.php">Events</a>
				  	  </li>';
    } else if (isset($_SESSION['aid'])) { //admin session
		$registerBtn = '';
		$loginBtn = '';

		$adminBtn = '<li style="margin-left:385px">
						<a href="admin.php">Admin</a>
					 </li>';

		$logoutBtn = '<li>
						<a href="logout.php">Logout</a>
					 </li>';
	} else if (isset($_SESSION['sid'])) { //super admin session
		$registerBtn = '';
		$loginBtn = '';

		$adminBtn = '<li style="margin-left:385px">
						<a href="admin.php">Super</a>
					 </li>';

		$logoutBtn = '<li>
						<a href="logout.php">Logout</a>
					 </li>';
	}
?>

<!doctype html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width-device-width, initial-scale=1.0">

		<!-- External libraries -->

		<link rel="stylesheet" href="css/style.css" type="text/css">
		<link rel="stylesheet" href="css/mobile.css" type="text/css">

		<script src="js/mobile.js" type="text/javascript"></script>
    </head>

	<body>
		<div id="page">
			<div id="header">
				<div>
					<a href="index.php" class="logo"><h2 style="margin-top:5%; margin-left:10%; color:white">TICKETING</h2></a>
					<ul id="navigation" style="margin-top:15px;">
						<li>
							<a href="index.php">Home</a>
						</li>
						
						<?php print $eventsBtn?>

						<?php print $registerBtn?>

						<?php print $loginBtn?>

						<?php print $userInfoBtn?>

						<?php print $adminBtn?>

						<?php print $logoutBtn?>
					</ul>
				</div>
			</div>
	</body>
</html>