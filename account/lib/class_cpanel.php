<?php
	// ©vex.email
	
	class MyCpanel
	{
		protected $cpanel_token;
		protected $cpanel_user;
		protected $cpanel_host;
		protected $cpanel_port;
		protected $use_ssl;
		
		public function __construct()
		{
			$this->cpanel_token = C_API_TOKEN;
			$this->cpanel_user = C_USERNAME;
			$this->cpanel_host = C_DOMAIN;
			$this->cpanel_port = C_PORT ?? 2083;
			$this->use_ssl = true;
		}
		
		private function callUAPI($module, $function, $params = [])
		{
			$scheme = $this->use_ssl ? 'https' : 'http';
			$url = "$scheme://{$this->cpanel_host}:{$this->cpanel_port}/execute/$module/$function";
			
			if (!empty($params)) {
				$url .= '?' . http_build_query($params);
			}
			
			$headers = [
				'Authorization: cpanel ' . $this->cpanel_user . ':' . $this->cpanel_token,
			];
			
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // for self-signed certs
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			
			$response = curl_exec($ch);
			if (curl_errno($ch)) {
				return ['errors' => [curl_error($ch)]];
			}
			
			curl_close($ch);
			return json_decode($response, true);
		}
		
		public function AddEmail($email, $password, $quota, $forward = null)
		{
			[$user, $domain] = explode('@', $email);
			
			$result = $this->callUAPI('Email', 'add_pop', [
				'email' => $user,
				'password' => $password,
				'quota' => $quota,
				'domain' => $domain,
			]);
			
			if (!empty($result['errors'])) {
				return false;
			}
			
			if ($forward) {
				$this->callUAPI('Email', 'add_forwarder', [
					'domain' => $domain,
					'email' => "$user@$domain",
					'fwdopt' => 'fwd',
					'fwdemail' => $forward,
				]);
			}
			
			return true;
		}
		
		public function ChangeEmailPassword($email, $password)
		{
			[$user, $domain] = explode('@', $email);
			
			$result = $this->callUAPI('Email', 'passwd_pop', [
				'email' => $user,
				'password' => $password,
				'domain' => $domain,
			]);
			
			return empty($result['errors']);
		}
	}