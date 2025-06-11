<?php
	// ©vex.email
	
	include('config.php');
	include('dbconnect.php');
	$db=DB::getInstance();
	function post($var) {
		if (isset($_POST[$var])) {
			$data = trim($_POST[$var]);
			$data = htmlspecialchars($data);
			return filter_var($data, FILTER_SANITIZE_STRING);
		}
	}
	$username = post('username');
	$fuser=$username.'@'.C_DOMAIN;
	$sql = "SELECT COUNT(*) AS username from mailusers WHERE cusername =:username";
	$stmt=$db->prepare($sql);
	$stmt->bindParam(':username', $fuser);
	$stmt->execute();
	$row=$stmt->fetch(PDO::FETCH_OBJ);
	
	if ($row->username>0) {
		echo 0;
	}
	else {
		echo 1;
	}
