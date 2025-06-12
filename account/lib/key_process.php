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
	
	if ($_POST) {
		$email = post('useremail');
		$password = post('pwd');
		$allowNewKey = false;
		$now = new DateTime();
		
		$sql_user = "SELECT * FROM mailusers WHERE cusername = :username";
		$stmt_user = $db->prepare($sql_user);
		$stmt_user->bindParam(':username', $email);
		$stmt_user->execute();
		$row_user = $stmt_user->fetch(PDO::FETCH_OBJ);
		
		if ($row_user) {
			
			$decrypted_pwd = vexSecure('dec', $row_user->cpassword, ENCRYPT_KEY);
			
			if ($decrypted_pwd != $password) {
				die('<span id="blink-text-red" style="color:#F00">Incorrect password.</span>');
			}
			
			$rdate = DateTime::createFromFormat('d/m/Y H:i', $row_user->rdate . ' 12:00');
			$waitPeriod = new DateInterval('P7D');
			$allowedDate = clone $rdate;
			$allowedDate->add($waitPeriod);
			
			if ($row_user->isgod == 0 && $now < $allowedDate) {
				$diff = $now->diff($allowedDate);
				die(formatWaitTimeMessage($diff, "Your account is too new. You must wait"));
			}
			
			$sql_keys = "SELECT * FROM vexkeys WHERE creator = :username ORDER BY dateissued DESC LIMIT 1";
			$stmt_keys = $db->prepare($sql_keys);
			$stmt_keys->bindParam(':username', $email);
			$stmt_keys->execute();
			$row_key = $stmt_keys->fetch(PDO::FETCH_OBJ);
			
			if ($row_key) {
				$latestKeyDate = new DateTime($row_key->dateissued);
				$sevenDaysAgo = new DateTime('-7 days');
				
				if ($row_user->isgod == 1 || $latestKeyDate <= $sevenDaysAgo) {
					$allowNewKey = true;
				} else {
					$nextAllowedDate = clone $latestKeyDate;
					$nextAllowedDate->add(new DateInterval('P7D'));
					$diff = $now->diff($nextAllowedDate);
					die(formatWaitTimeMessage($diff, "You have already been issued a key within the past week. Please wait"));
				}
				
			} else {
				$allowNewKey = true;
			}
			
		} else {
			die('<span id="blink-text-red" style="color:#F00">There is no Vex Email account with that name.</span>');
		}
		
		if ($allowNewKey) {
			
			do {
				$newKey = generateKey();
				
				$sql_check = "SELECT COUNT(*) FROM vexkeys WHERE keyvalue = :keyvalue";
				$stmt_check = $db->prepare($sql_check);
				$stmt_check->execute([':keyvalue' => $newKey]);
				$exists = $stmt_check->fetchColumn() > 0;
				
			} while ($exists);
			
			$now = (new DateTime())->format('Y-m-d H:i:s');
			
			$sql_insert = "INSERT INTO vexkeys (keyvalue, creator, dateissued) VALUES (:keyvalue, :creator, :dateissued)";
			$stmt_insert = $db->prepare($sql_insert);
			$stmt_insert->execute([
				':keyvalue'   => $newKey,
				':creator'    => $email,
				':dateissued' => $now
			]);
			
			$headers  = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
			$headers .= "From: Vex Email Support <support@" . C_DOMAIN . ">" . "\r\n";
			$subject = "Your Vex Email Key";
			$notes ="Here is your requested Vex Email key: ".$newKey."<br> We appreciate you recommending Vex Email to another user!<br><br> Thank you for using our service,<br> -Vex Support";
			
			mail($email, $subject, $notes, $headers);
			
			die('<span id="blink-text-green" style="color:#BFED46">New key issued: '.$newKey.'</span>');
		}
	}