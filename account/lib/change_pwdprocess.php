<?php
	// ©vex.email
	
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	define('INCLUDE_CHECK', 1);
	include('config.php');
	include('functions.php');
	include('dbconnect.php');
	require('vendor/autoload.php');
	include('class_cpanel.php');
	$new = new MyCpanel();
	$db=DB::getInstance();
	
	if ($_POST) {
		$email = post('useremail');
		$currentPassword = post('oldpwd');
		$newPassword = post('newpwd');
		$newPassword2 = post('newpwd2');
		
		$sql = "SELECT * from mailusers WHERE cusername =:username";
		$stmt=$db->prepare($sql);
		$stmt->bindParam(':username',$email);
		$stmt->execute();
		$row=$stmt->fetch(PDO::FETCH_OBJ);
		$decrypted_pwd = vexSecure('dec', $row->cpassword,ENCRYPT_KEY);
		
		if (!$row) {
			die('<span id="blink-text-red" style="color:#F00">There is no Vex Email account with that name.</span>');
		}
		if ($newPassword!=$newPassword2) {
			die('<span id="blink-text-red" style="color:#F00">The passwords do not match!</span>');
		}
		if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).{8,18}$/", $newPassword)) {
			die('<span id="blink-text-red" style="color:#F00">Your password must be 8–18 characters long and include uppercase, lowercase, a number, and a special symbol.</span>');
		}
		if ($decrypted_pwd != $currentPassword) {
			die('<span id="blink-text-red" style="color:#F00">Incorrect password.</span>');
		}
		
		$oldpass = $currentPassword;
		$newpass = $newPassword;
		
		if ($new->ChangeEmailPassword($email,$newpass) === true)
			{
				//$new_pwd=base64_encode($newPassword);
				$new_pwd = vexSecure('enc', $newPassword,ENCRYPT_KEY);
				$sql = "UPDATE mailusers SET cpassword=:cpassword WHERE cusername = :cusername";
				$stmt=$db->prepare($sql);
				$stmt->bindParam(':cpassword',$new_pwd);
				$stmt->bindParam(':cusername',$email);
				$stmt->execute();
				
				$headers  = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
				$headers .= "From: Vex Email Support <support@" . C_DOMAIN . ">" . "\r\n";
				$subject = "Vex Email Password Changed";
				$notes = "Your password has been changed successfully. If you did not request this change contact <a href=\"mailto:support@vex.email\">support@vex.email</a> immediately.<br><br> Thank you for using our service,<br> -Vex Support";
				mail($email, $subject, $notes, $headers);
				mail($row->cemail, $subject, $notes, $headers);
				
				die('<span id="blink-text-green" style="color:#BFED46">Password changed successfuly!</span>');
			} 
		else {
			die('<span id="blink-text-red" style="color:#F00">Password change failed, try complexifying your password.</span>');
		}
	}