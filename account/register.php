<?php
	session_start();
	define('INCLUDE_CHECK', 1);
	include('lib/config.php');
	include('lib/functions.php');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="robots" content="index, follow">
		<meta name="viewport" content="width=900">
		<link rel="icon" type="image/x-icon" href="/favicon.ico"/>
		<link rel="stylesheet" href="/css/global.css"/>
		<title>Vex Email - Registration</title>
		<meta name="description" content="Sign up for a free Vex Email account today!">
	</head>
	<body>
		<div id="wrapper">
			<div id="baseDisp">
				<script id="replace_with_navbar" src="/scripts/navbar.js"></script>
				<div id="center-welcome" style="background-image:url('/theme/signup.webp');">
					<p>Welcome to Vex</p>
				</div>
				<div id="contMain">
					<div id="leftColumn">
						<form action="lib/process_reg.php" id="msgSend" method="post" class="form" role="form" enctype="multipart/form-data">
							<h1>Vex Email Registration</h1>
							<div class="input-row">
								<input type="text" class="form-control" name="fname" id="fname" placeholder="First Name" required>
								<input type="text" class="form-control" name="lname" id="lname" placeholder="Last Name" required>
							</div>
							<div class="input-row">
								<input name="username" type="text" class="form-control" id="username" maxlength="15" placeholder="Username" required>
								<div id="username_availability_result"><span style="font-family:tahoma-bold;font-size:125%;padding-left:3px">@vex.email</span></div>
								<input type="button" id="check_username_availability" value="Check Availability" class="btn btn-success btn-sm">
							</div>
							<div class="input-row">
								<input type="password" class="form-control" name="pwd" maxlength="18" id="pwd" placeholder="Enter Password" required>
								<input type="password" name="pwd2" maxlength="18" value="" class="form-control" placeholder="Confirm Password" required>
							</div>
							<p style="margin-bottom:0">Birth Date</p>
							<div class="input-row">
								<select name="month" class="form-control">
									<option value="0">Month</option><?php echo generate_options(1, 12, 'callback_month')?>
								</select>
								<select name="day" class="form-control">
									<option value="0">Day</option><?php echo generate_options(1, 31)?>
								</select>
								<select name="year" class="form-control">
									<option value="0">Year</option><?php echo generate_options(date('Y'), 1900)?>
								</select>
							</div>
							<div class="input-row">
								<input type="text" class="form-control numbersOnly" name="phone" id="phone" placeholder="Phone Number" required>
								<input type="email" class="form-control" name="email" placeholder="Alternate Email" required>
							</div>
							<p style="margin-bottom:0">By clicking 'Create New Account', you agree to our <a href="/legal/EULA.txt" target="_blank">EULA</a> and <a href="/legal/Privacy.txt" target="_blank">Privacy Policy</a>.</p>
							<input name="submit" type="submit" class="signup-btn" id="msgButton" value="Create New Account">
							<input type="text" class="form-control key-input" maxlength="8" name="key" id="key" placeholder="Key" required>
						</form>
					</div>
					<div id="rightColumn">
						<h2>About Registration</h2> 
						<p>Welcome to Vex Email! We're excited that you're choosing to create an account with us. With Vex Email, you get a simple, reliable, and free email service designed to meet your everyday needs. Every user receives a 1GB mailbox, ensuring plenty of space for your messages, and you can send attachments up to 128MB, perfect for sharing important files without hassle.</p>
						<p>To sign up for Vex Email you need a key. You can get a key from a current account holder. Once you create an account you can issue and share your own keys. If you don't know anyone who has a Vex Email account don't worry! We occasionally post keys on our homepage.</p>
						<div id="output"></div>
					</div>
				</div>
			</div>
			<div id="endClear"></div>
			<script id="replace_with_footer" src="/scripts/footer.js"></script>
		</div>
		<script src="assets/js/jquery.js"></script>
		<script src="assets/js/jquery.form.js"></script>
		<script src="assets/js/custom.js"></script>
	</body>
</html>
