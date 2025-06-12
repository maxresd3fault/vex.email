<?php
	// ©vex.email
	
	if (!defined('INCLUDE_CHECK')) {
		die('You are not allowed to execute this file directly');
	}
	
	function generateKey() {
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$key = '';
		for ($i = 0; $i < 8; $i++) {
			$key .= $characters[random_int(0, strlen($characters) - 1)];
		}
		return $key;
	}
	
	function formatWaitTimeMessage(DateInterval $diff, string $baseMessage): string {
		$days = $diff->days;
		$hours = $diff->h;
		$minutes = $diff->i;
		
		if ($minutes > 30) {
			$hours++;
			if ($hours == 24) {
				$days++;
				$hours = 0;
			}
		}
		
		$timeParts = [];
		if ($days > 0) {
			$timeParts[] = $days . ' day' . ($days > 1 ? 's' : '');
		}
		if ($hours > 0) {
			$timeParts[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
		}
		
		$timeString = implode(' and ', $timeParts);
		
		return '<span id="blink-text-red" style="color:#F00">' . $baseMessage . ' ' . $timeString . '.</span>';
	}
	
	function ValidEmail($email) {
		$es= filter_var($email, FILTER_SANITIZE_EMAIL);
		return filter_var($es, FILTER_VALIDATE_EMAIL);
	}
	
	function ValidNumber($number) {
		$nu= filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT);
		return filter_var($nu, FILTER_VALIDATE_FLOAT);
	}
	
	function post($var) {
		if (isset($_POST[$var])) {
			$data = trim($_POST[$var]);
			$data = htmlspecialchars($data);
			return filter_var($data, FILTER_SANITIZE_STRING);
		}
	}
	
	function get($var) {
		if (isset($_GET[$var])) {
			$data = trim($_GET[$var]);
			$data = htmlspecialchars($data);
			return filter_var($data, FILTER_SANITIZE_STRING);
		}
	}
	
	function session($var) {
		if (isset($_SESSION[$var])) {
			return $_SESSION[$var];
		}
	}
	
	function generate_options($from, $to, $callback=false) {
		$reverse=false;
		
		if ($from>$to) {
			$tmp=$from;
			$from=$to;
			$to=$tmp;
			
			$reverse=true;
		}
		
		$return_string=array();
		for ($i=$from;$i<=$to;$i++) {
			$return_string[]='
		<option value="'.$i.'">'.($callback?$callback($i):$i).'</option>
		';
		}
		
		if ($reverse) {
			$return_string=array_reverse($return_string);
		}
		
		return join('', $return_string);
	}
	
	function callback_month($month) {
		return date('M', mktime(0, 0, 0, $month, 1));
	}
	
	function UserCheck($username) {
		$username = mysqli_real_escape_string($_POST['username']);
		$result = mysqli_query('SELECT cusername from mailusers WHERE cusername = "'. $username .'"');
		if (mysqli_num_rows($result)>0) {
			echo 0;
		}
		else {
			echo 1;
		}
	}
	
	function vexSecure($action, $string, $secret_key) {
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$secret_iv = 'secrete';
		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		if ($action == 'enc') {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		}
		elseif ($action == 'dec') {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}
