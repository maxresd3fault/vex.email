<?php
	// ©vex.email
	
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	define('INCLUDE_CHECK', 1);
	include('config.php');
	include('functions.php');
	include('dbconnect.php');
	$db=DB::getInstance();
	
	if($_POST) {
		$email = post('email');
		
		if($email != "") {
			$sql = "SELECT COUNT(cemail) AS email from mailusers WHERE cemail =:cemail";
			$stmt=$db->prepare($sql);
			$stmt->bindParam(':cemail', $email);
			$stmt->execute();
			$row=$stmt->fetch(PDO::FETCH_OBJ);
			
			if($row->email > 0) {
				$sql = "SELECT  * FROM  mailusers WHERE cemail = :cemail";
				$stmt=$db->prepare($sql);
				$stmt->bindParam(':cemail', $email);
				$stmt->execute();
				$row=$stmt->fetch(PDO::FETCH_OBJ);
				
				$headers  = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
				$headers .= "From: Vex Email Support <support@" . C_DOMAIN . ">" . "\r\n";
				$subject = "Vex Email Password Recovery";
				$decrypted_pwd = vexSecure('dec', $row->cpassword, ENCRYPT_KEY);
				$notes ="Here is your requested login information: <br><br> Email: ".$row->cusername." <br> Password: ".$decrypted_pwd."<br><br> We recommend that you reset your password as soon as possible <a href=\"https://vex.email/account/change_password.html\">here</a>.<br><br> Thank you for using our service,<br> -Vex Support";
				
				mail($email, $subject, $notes, $headers);
				
				die('<span id="blink-text-green" style="color:#BFED46">Your password has been sent to:  <a href="mailto:'.$email.'">'.$email.'</a></span>');
			}
			else {
				die('<span id="blink-text-red" style="color:#F00">The email you provided is not linked to any Vex Email account.</span>');
			}
		}
		else {
			die('<span id="blink-text-red" style="color:#F00">Please enter a recovery email address.</span>');
		}
	}