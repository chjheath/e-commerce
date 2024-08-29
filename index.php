<?php

	session_start();

	$display = '<h3>REGISTER NOW</h3>
	<h1>LOG IN BELOW</h1>
	<h4>Create a new account or login below.</h4>
	<a href="login.php" class="more">CLICK HERE</a>';

	if (isset($_SESSION['email'])) {
		$display = '<h3>EXPLORE NOW</h3>
		<h1>TICKETS HERE</h1>
		<h4>View a complete list of all upcoming events</h4>
		<a href="search.php" class="more">VIEW NOW</a>';
	} else if (isset($_SESSION['aid']) || isset($_SESSION['sid'])) {
		$display = '<h3>BEGIN NOW</h3>
		<h1>MANAGE NOW</h1>
		<h4>Manage users, events, venues, performers.</h4>
		<a href="admin.php" class="more">CLICK HERE</a>';
	}

	include "header.php";
?>

<!doctype html>
<!-- Website template by freewebsitetemplates.com -->
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Ticketing</title>
	<link rel="stylesheet" href="css/style.css" type="text/css">
	<link rel="stylesheet" type="text/css" href="css/mobile.css">
	<script src="js/mobile.js" type="text/javascript"></script>
</head>
<body>
		<div id="body" class="home">
			<div class="header">
				<div>
					<?php print $display?>
				</div>
			</div>
			<div class="body">
				<div>
					<h1>MORE INFO</h1>
					<p>We offer users the ability to search for their favorite team, artist, or venue and find tickets to any upcoming events. Visit the "Events" page to get started.</p>
				</div>
			</div>

		<div id="footer">
			<div class="connect">
				<div>
					<h1>FOLLOW OUR SOCIAL MEDIA</h1>
					<div>
						<a href="http://freewebsitetemplates.com/go/facebook/" class="facebook">facebook</a>
						<a href="http://freewebsitetemplates.com/go/twitter/" class="twitter">twitter</a>
						<a href="http://freewebsitetemplates.com/go/googleplus/" class="googleplus">googleplus</a>
						<a href="http://pinterest.com/fwtemplates/" class="pinterest">pinterest</a>
					</div>
				</div>
			</div>
			<div class="footnote">

				<div>
					<p>&copy; 2023</p>
				</div>
			</div>
		</div>
		</div>
	</div>
</body>
</html>