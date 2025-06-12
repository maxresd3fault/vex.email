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
	$email_quota=DISC_QUOTA;
	$db=DB::getInstance();
	
	if ($_POST) {
		$cfname = post('fname');
		$clname = post('lname');
		$cusername = post('username');
		$cpassword = post('pwd');
		$cpassword2 = post('pwd2');
		$month = post('month');
		$day = post('day');
		$year = post('year');
		$key = post('key');
		if ($month < 10) {
			$nmonth='0'.$month;
		}
		else {
			$nmonth=$month;
		}
		if ($day < 10) {
			$nday='0'.$day;
		}
		else {
			$nday=$day;
		}
		$cbirthday=$nmonth.'/'.$nday.'/'.$year;
		
		$cphone = post('phone');
		$cemail = post('email');
		$rdate = date('d/m/Y');
		
		$sql = "SELECT * FROM vexkeys WHERE keyvalue = :key AND valid = 1 AND usedby IS NULL LIMIT 1";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':key', $key);
		$stmt->execute();
		$key_row = $stmt->fetch(PDO::FETCH_OBJ);
		
		if (!$key_row) {
			die('<span id="blink-text-red" style="color:#F00">The provided key is invalid or already used.</span>');
		}
		
		if (empty($cfname) || empty($clname) || empty($clname) || empty($cusername) || empty($cpassword)) {
			die('<span id="blink-text-red" style="color:#F00">All the fields are required.</span>');
		}
		
		if (!(int)$day || !(int)$month || !(int)$year) {
			die('<span id="blink-text-red" style="color:#F00">You have to fill in your birthday!</span>');
		}
		if (!(int)$cphone || strlen($cphone) !== 10) {
			die('<span id="blink-text-red" style="color:#F00">Please enter a valid phone number!</span>');
		}
		
		if (!ValidEmail($cemail)) {
			die('<span id="blink-text-red" style="color:#F00">You have not provided a valid email</span>');
		}
		
		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $cusername)) {
			die('<span id="blink-text-red" style="color:#F00">The username you entered is invalid!</span>');
		}
		
		if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).{8,18}$/", $cpassword)) {
			die('<span id="blink-text-red" style="color:#F00">Your password must be 8–18 characters long and include uppercase, lowercase, a number, and a special symbol.</span>');
		}
		
		if (!empty($cpassword) and $cpassword!=$cpassword2) {
			die('<span id="blink-text-red" style="color:#F00">The passwords do not match!</span>');
		}
		
		if (!empty($cemail)) {
			$sql = "SELECT COUNT(cemail) AS email from mailusers WHERE cemail =:cemail";
			$stmt=$db->prepare($sql);
			$stmt->bindParam(':cemail',$cemail);
			$stmt->execute();
			$row=$stmt->fetch(PDO::FETCH_OBJ);
			if ($row->email > 0) {
				die('<span id="blink-text-red" style="color:#F00">That alternate email is already in use!</span>');
			}
		}
		
		if (!empty($cusername)) {
			$sql = "SELECT COUNT(cusername) AS username from mailusers WHERE cusername =:username";
			$stmt=$db->prepare($sql);
			$stmt->bindParam(':username',$cusername);
			$stmt->execute();
			$row=$stmt->fetch(PDO::FETCH_OBJ);
			if ($row->username > 0) {
				die('<span id="blink-text-red" style="color:#F00">That username is already in use!</span>');
			}
		}
		
		$finalpwd = vexSecure('enc', $cpassword,ENCRYPT_KEY);
		//$finalpwd=base64_encode($cpassword);
		if ($new->AddEmail($cusername.'@'.C_DOMAIN,$cpassword,$email_quota)===true) {
			
			$sql = $db->prepare("INSERT INTO `mailusers` (`cfname`, `clname`, `cusername`, `cpassword`, `cbirthday`, `cphone`, `cemail`, `rdate`) VALUES (?,?,?,?,?,?,?,?)");
			$sql->bindValue(1, $cfname);
			$sql->bindValue(2, $clname);
			$sql->bindValue(3, $cusername . '@' . C_DOMAIN);
			$sql->bindValue(4, $finalpwd);
			$sql->bindValue(5, $cbirthday);
			$sql->bindValue(6, $cphone);
			$sql->bindValue(7, $cemail);
			$sql->bindValue(8, $rdate);
			$sql->execute();
			
			//create email
			unset($scap);
			$_SESSION['cusername'] = $cusername;
			
			//deactivate key
			$update_key = $db->prepare("UPDATE vexkeys SET valid = 0, usedby = :usedby, dateused = NOW() WHERE keyvalue = :keyvalue");
			$update_key->execute([
				':usedby'   => $cusername . '@' . C_DOMAIN,
				':keyvalue' => $key_row->keyvalue
			]);
			
			die('<span id="blink-text-green" style="color:#BFED46">Your account, <a href="mailto:' . $cusername . '@' . C_DOMAIN . '">' . $cusername . '@' . C_DOMAIN . '</a>, was successfully created!</span>');
		}
		else {
			die('<span id="blink-text-red" style="color:#F00">Account creation failed, try complexifying your password.</span>');
		}
	}