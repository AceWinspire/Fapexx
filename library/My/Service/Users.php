<?php
/* Service class for communication with backend api extends My_Service class
 *
 * @author Aleksandar Markicevic <aleksandar.markicevic@golive.rs>
 * @author Aleksandar Stevanovic <aleksandar.stevanovic@golive.rs>
 * @author Milan Marjanovic <milan.marjanovic@golive.rs>
 * @version january 2015
 */

class My_Service_Users extends My_Service{

	public function __construct(){
		parent::__construct();
	}

	public function get($user_id){
		try {
			$this->http_client->setUri($this->api_url.'/users/get');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));
			$this->http_client->setParameterGet(array('id'	=> $user_id));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}

	public function auth($email, $password, $session_id){
		try {
			$this->http_client->setUri($this->api_url.'/users/auth');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));
			$this->http_client->setParameterGet(array('identity'	=> $email));
			$this->http_client->setParameterGet(array('password'	=> $password));
			$this->http_client->setParameterGet(array('session_id'	=> $session_id));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			//var_dump($this->http_client->getLastResponse()); var_dump($this->http_client->getLastRequest());die;

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}
	
	public function checkSession($user_id, $session_id){
		try {
			$this->http_client->setUri($this->api_url.'/users/check-session');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));
			$this->http_client->setParameterGet(array('user_id'	=> $user_id));
			$this->http_client->setParameterGet(array('session_id'	=> $session_id));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}

	public function charged($user_id){
		try {
			$this->http_client->setUri($this->api_url.'/users/charged');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));
			$this->http_client->setParameterGet(array('user_id'	=> $user_id));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}

	public function charge($user_id){
		try {
			$this->http_client->setUri($this->api_url.'/users/charge');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));
			$this->http_client->setParameterGet(array('user_id'	=> $user_id));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}

	public function uncharge($user_id){
		try {
			$this->http_client->setUri($this->api_url.'/users/uncharge');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));
			$this->http_client->setParameterGet(array('user_id'	=> $user_id));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}
	public function verify($code, $password, $repeated_password, $ip_address){
		try {
			$this->http_client->setUri($this->api_url.'/users/verify');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'				=> $this->app_id));
			$this->http_client->setParameterGet(array('code'				=> $code));
			$this->http_client->setParameterGet(array('password'			=> $password));
			$this->http_client->setParameterGet(array('repeated_password'	=> $repeated_password));
			$this->http_client->setParameterGet(array('ip_address'			=> $ip_address));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}

	public function passRecovery($email){
		try {
			$this->http_client->setUri($this->api_url.'/users/pass-recovery');
			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));
			$this->http_client->setParameterGet(array('identity'	=> $email));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());
			
			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}

	public function register($email, $password, $session_id){
		try {
			$this->http_client->setUri($this->api_url.'/users/register');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));
			$this->http_client->setParameterGet(array('identity'	=> $email));
			$this->http_client->setParameterGet(array('password'	=> $password));
			$this->http_client->setParameterGet(array('session_id'	=> $session_id));

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}

	public function update($data, $user_id){
		try {
			$this->http_client->setUri($this->api_url.'/users/update');

			$this->http_client->resetParameters();

			$this->http_client->setParameterGet(array('app_id'	=> $this->app_id));
			$this->http_client->setParameterGet(array('user_id'	=> $user_id));
			foreach ($data as $key => $value) {
				$this->http_client->setParameterGet(array($key	=> $value));
			}

			$response = $this->http_client->request('GET');
			$result   = json_decode($response->getBody());

			/*var_dump($this->http_client->getLastResponse()); 
			var_dump($this->http_client->getLastRequest());die;*/

			if($response->getStatus() == 200){
				return $result;
			}else{
				return array();
			}
		}
		catch (Exception $e) {
			throw new Exception('Service unavailable');
		}
	}
}